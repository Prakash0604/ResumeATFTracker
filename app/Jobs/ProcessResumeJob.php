<?php

namespace App\Jobs;

use App\Models\Resume;
use App\Services\ResumeAnalyzerService;
use App\Events\ResumeAnalyzed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ProcessResumeJob
 *
 * Queued background job that handles the full resume analysis pipeline.
 *
 * WHY A JOB?
 * AI analysis takes 10-30 seconds. We can't block the HTTP request.
 * The user uploads → gets immediate "processing" feedback → job runs async.
 * Frontend polls /api/resumes/{id}/status every 3s until 'analyzed'.
 *
 * Queue: 'analysis' (separate from default to prioritize)
 * Retries: 3 attempts with exponential backoff (30s, 60s, 120s)
 * Timeout: 180 seconds (AI can be slow)
 */
class ProcessResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(
        private readonly Resume $resume
    ) {
        $this->onQueue('analysis');
    }
    

    /**
     * Execute the job.
     * ResumeAnalyzerService is resolved from the service container.
     */
    public function handle(ResumeAnalyzerService $analyzer): void
    {
        Log::info("Processing resume #{$this->resume->id} for user #{$this->resume->user_id}");

        // Mark as processing so frontend spinner shows
        $this->resume->update(['status' => 'processing']);

        try {
            $analyzer->analyze($this->resume);

            // Fire event → listeners can send email notifications
            event(new ResumeAnalyzed($this->resume));

            Log::info("Resume #{$this->resume->id} analyzed. Score: {$this->resume->analysis->overall_score}");

        } catch (\Exception $e) {
            Log::error("Resume #{$this->resume->id} analysis failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->resume->update([
                'status'           => 'failed',
                'processing_error' => $e->getMessage(),
            ]);

            // Re-throw so Laravel marks job as failed (triggers retries)
            throw $e;
        }
    }

    /**
     * Handle final job failure after all retries exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("Resume #{$this->resume->id} permanently failed", [
            'error' => $exception->getMessage(),
        ]);

        $this->resume->update([
            'status'           => 'failed',
            'processing_error' => 'Analysis failed after multiple attempts: ' . $exception->getMessage(),
        ]);
    }

    /**
     * Exponential backoff: retry after 30s, then 60s, then 120s
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }
}
