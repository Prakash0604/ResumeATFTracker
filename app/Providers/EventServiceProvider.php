<?php

namespace App\Providers;

use App\Events\ResumeAnalyzed;
use App\Listeners\SendAnalysisCompleteNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * EventServiceProvider
 *
 * Maps Events → their Listeners.
 *
 * When an event is fired (e.g. event(new ResumeAnalyzed($resume))),
 * Laravel finds all listeners registered here and calls their handle() method.
 *
 * Multiple listeners can listen to the same event (fan-out pattern).
 * Listeners can be queued by implementing ShouldQueue.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Event → Listener map.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Laravel built-in: sends email verification after registration
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Custom: notify user when their resume analysis finishes
        ResumeAnalyzed::class => [
            SendAnalysisCompleteNotification::class,
        ],
    ];

    /**
     * Auto-discover events by scanning the app/Listeners directory.
     * Set to false for performance in production (use explicit map above).
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
