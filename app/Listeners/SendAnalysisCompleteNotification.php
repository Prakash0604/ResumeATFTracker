<?php

namespace App\Listeners;

use App\Events\ResumeAnalyzed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendAnalysisCompleteNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ResumeAnalyzed $event): void
    {
        $resume   = $event->resume;
        $user     = $resume->user;
        $analysis = $resume->analysis;

        if (!$analysis) {
            Log::warning("ResumeAnalyzed event fired but no analysis found for resume #{$resume->id}");
            return;
        }

        $score = $analysis->overall_score;

        Log::info("Sending analysis complete notification to {$user->email} — Score: {$score}");

        // Send notification email
        // Mail::to($user->email)->send(new \App\Mail\AnalysisComplete($resume));

        /*
         * NOTE: The actual Mailable class (App\Mail\AnalysisComplete) would be
         * created separately with:
         *   php artisan make:mail AnalysisComplete --markdown=emails.analysis-complete
         *
         * The mail would include:
         *  - Candidate name
         *  - Overall ATS score with color coding
         *  - Count of critical/high issues
         *  - Direct link to analysis page
         *  - Top 3 improvement tips
         *
         * For now, logging the intent so the system works without email configured.
         */

        Log::info("Analysis notification queued for {$user->email}", [
            'resume_id' => $resume->id,
            'score'     => $score,
            'url'       => url("/resumes/{$resume->id}"),
        ]);
    }

   
    public function failed(ResumeAnalyzed $event, \Throwable $exception): void
    {
        Log::error("Failed to send analysis notification for resume #{$event->resume->id}", [
            'error' => $exception->getMessage(),
        ]);
    }
}