<?php

namespace App\Events;

use App\Models\Resume;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ResumeAnalyzed Event
 *
 * Fired after a resume analysis successfully completes.
 * Listeners can hook into this to:
 *  - Send email notification to user
 *  - Send Slack/webhook notification
 *  - Update analytics/statistics
 *  - Trigger any post-processing workflows
 */
class ResumeAnalyzed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Resume $resume
    ) {}
}
