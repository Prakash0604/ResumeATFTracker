<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadResumeRequest;
use App\Jobs\ProcessResumeJob;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ResumeController extends Controller
{
    
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

        if ($resume->status !== 'analyzed') {
            return view('resume.processing', compact('resume'));
        }
        $resume->load(['analysis', 'feedback_items', 'skills', 'keywords' => function ($q) {
            $q->where('is_ats_keyword', true)->orderByDesc('frequency')->limit(30);
        }]);
        // dd($resume);

        return view('resume.show', compact('resume'));
    }

    
    public function upload(UploadResumeRequest $request): JsonResponse
    {
        if (!Auth::user()->canUpload()) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your free plan limit of 5 resumes. Please upgrade to Pro.',
            ], 403);
        }
        $file     = $request->file('resume');
        $fileType = strtolower($file->getClientOriginalExtension());

        // Generate secure, collision-proof filename
        $storedName = Str::uuid() . '.' . $fileType;
        $storagePath = "resumes/" . Auth::id() . "/{$storedName}";

        // Store file (disk: 'local' by default, can swap to 's3')
        $dataim=Storage::put($storagePath, file_get_contents($file->getRealPath()));

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
        Log::info('File stored at: ' . $storagePath, ['file' => $file, 'file type' => $fileType, 'storage path' => $storagePath,'store name' => $storedName,'path' => $dataim,'resume'=>$resume]);
        // Dispatch async job — returns immediately to client
        ProcessResumeJob::dispatch($resume);

        return response()->json([
            'success'   => true,
            'resume_id' => $resume->id,
            'message'   => 'Resume uploaded successfully. Analysis in progress...',
            'redirect'  => route('resumes.show', $resume->id),
        ], 201);
    }

    public function status(Resume $resume): JsonResponse
    {
        $this->authorize('view', $resume);
        $resume = $resume->fresh(['analysis']);

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

    public function addressFeedback(Resume $resume, int $feedbackId): JsonResponse
    {
        $this->authorize('view', $resume);

        $feedback = $resume->feedback_items()->findOrFail($feedbackId);
        $feedback->update([
            'is_addressed' => !$feedback->is_addressed,  // Toggle
            'addressed_at' => $feedback->is_addressed ? null : now(),
        ]);

        return response()->json([
            'addressed' => $feedback->fresh()->is_addressed,
        ]);
    }

    public function destroy(Resume $resume): JsonResponse
    {
        $this->authorize('delete', $resume);
        $resume->delete();

        return response()->json(['success' => true]);
    }

    public function reanalyze(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('update', $resume);
         $resume->analysis?->delete();
        $resume->feedback_items()->delete();
        $resume->skills()->delete();
        $resume->keywords()->delete();

        $resume->update([
            'status'                 => 'uploaded',
            'processing_error'       => null,
            'analyzed_at'            => null,
            'target_job_title'       => $request->input('job_title', $resume->target_job_title),
            'target_job_description' => $request->input('job_description', $resume->target_job_description),
        ]);


        ProcessResumeJob::dispatch($resume);

        return response()->json([
            'success' => true,
            'message' => 'Re-analysis started.',
            'redirect' => route('resumes.show', $resume->id),
        ]);
    }
}
