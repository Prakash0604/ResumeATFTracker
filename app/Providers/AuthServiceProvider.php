<?php

namespace App\Providers;

use App\Models\Resume;
use App\Policies\ResumePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * AuthServiceProvider
 *
 * Registers model-to-policy mappings for Laravel's Gate/Policy system.
 *
 * When a controller calls:
 *   $this->authorize('view', $resume);
 *
 * Laravel looks up this $policies array, finds ResumePolicy,
 * and calls ResumePolicy::view($user, $resume).
 *
 * This provider must be listed in config/app.php providers array.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model → policy mappings.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Resume::class => ResumePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
    
}
