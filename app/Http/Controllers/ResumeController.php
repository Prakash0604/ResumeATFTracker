<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadResumeRequest;
use App\Models\Resume;
use App\Jobs\ProcessResumeJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ResumeController
 *
 * Handles all resume-related HTTP actions:
 *   GET  /resumes           → index()   — list user's resumes
 *   GET  /resumes/{id}      → show()    — full analysis view
 *   POST /resumes/upload    → upload()  — receive file, dispatch job
 *   DELETE /resumes/{id}    → destroy() — soft delete resume
 *   GET  /api/resumes/{id}/status → status() — polling endpoint
 *   POST /api/resumes/{id}/feedback/{fid}/address → addressFeedback()
 */
class ResumeController extends Controller
{
    /**
     * List all resumes for the authenticated user.
     * Eager-load analysis to avoid N+1 on score display.
     */
    public function index()
    {
        $resumes = Auth::user()->resumes()
            ->with('analysis')
            ->latest()
            ->paginate(10);

        return view('resume.index', compact('resumes'));
    }

    /**
     * Show full analysis for a specific resume.
     * Eager-loads all relationships to populate the dashboard.
     */
    public function show(Resume $resume)
    {
        // Policy: user can only see their own resumes
        $this->authorize('view', $resume);

        $resume->load(['analysis', 'feedbacks', 'skills', 'keywords' => function ($q) {
            $q->where('is_ats_keyword', true)->orderByDesc('frequency')->limit(30);
        }]);

        return view('resume.show', compact('resume'));
    }

    /**
     * Handle resume upload.
     *
     * Flow:
     *  1. Validate file (PDF/DOCX/TXT, max 5MB)
     *  2. Generate secure UUID filename
     *  3. Store to disk (storage/app/resumes/{user_id}/)
     *  4. Create Resume DB record with status='uploaded'
     *  5. Dispatch ProcessResumeJob to queue
     *  6. Return JSON with resume ID (frontend polls status)
     */
    public function upload(UploadResumeRequest $request): JsonResponse
    {
        $file     = $request->file('resume');
        $fileType = strtolower($file->getClientOriginalExtension());

        // Generate secure, collision-proof filename
        $storedName = Str::uuid() . '.' . $fileType;
        $storagePath = "resumes/{$request->user()->id}/{$storedName}";

        // Store file (disk: 'local' by default, can swap to 's3')
        Storage::put($storagePath, file_get_contents($file->getRealPath()));

        // Create DB record
        $resume = Resume::create([
            'user_id'           => Auth::id(),
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $storedName,
            'file_path'         => $storagePath,
            'file_type'         => $fileType,
            'file_size'         => $file->getSize(),
            'target_job_title'  => $request->input('job_title'),
            'target_job_description' => $request->input('job_description'),
            'status'            => 'uploaded',
        ]);

        // Increment user upload count
        Auth::user()->increment('upload_count');

        // Dispatch async job — returns immediately to client
        ProcessResumeJob::dispatch($resume);

        return response()->json([
            'success'   => true,
            'resume_id' => $resume->id,
            'message'   => 'Resume uploaded successfully. Analysis in progress...',
            'redirect'  => route('resumes.show', $resume->id),
        ], 201);
    }

    /**
     * Polling endpoint — frontend calls this every 3 seconds.
     * Returns current processing status and score when done.
     */
    public function status(Resume $resume): JsonResponse
    {
        $this->authorize('view', $resume);

        $data = [
            'status' => $resume->status,
            'label'  => $resume->status_label,
        ];

        if ($resume->status === 'analyzed' && $resume->analysis) {
            $data['score']          = $resume->analysis->overall_score;
            $data['ats_compatible'] = $resume->analysis->ats_compatibility;
            $data['redirect']       = route('resumes.show', $resume->id);
        }

        if ($resume->status === 'failed') {
            $data['error'] = $resume->processing_error;
        }

        return response()->json($data);
    }

    /**
     * Mark a feedback item as addressed by the user.
     */
    public function addressFeedback(Resume $resume, int $feedbackId): JsonResponse
    {
        $this->authorize('view', $resume);

        $feedback = $resume->feedbacks()->findOrFail($feedbackId);
        $feedback->update([
            'is_addressed' => !$feedback->is_addressed,  // Toggle
            'addressed_at' => $feedback->is_addressed ? null : now(),
        ]);

        return response()->json([
            'addressed' => $feedback->fresh()->is_addressed,
        ]);
    }

    /**
     * Soft delete a resume (recoverable within 30 days).
     */
    public function destroy(Resume $resume): JsonResponse
    {
        $this->authorize('delete', $resume);
        $resume->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Re-analyze an existing resume (e.g., with new job description).
     */
    public function reanalyze(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('update', $resume);

        $resume->update([
            'status'            => 'uploaded',
            'target_job_title'  => $request->input('job_title', $resume->target_job_title),
            'target_job_description' => $request->input('job_description', $resume->target_job_description),
            'processing_error'  => null,
        ]);

        // Clear old analysis data
        $resume->analysis?->delete();
        $resume->feedbacks()->delete();
        $resume->skills()->delete();
        $resume->keywords()->delete();

        ProcessResumeJob::dispatch($resume);

        return response()->json([
            'success' => true,
            'message' => 'Re-analysis started.',
        ]);
    }
}
