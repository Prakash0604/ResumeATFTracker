<?php

namespace App\Policies;

use App\Models\Resume;
use App\Models\User;

/**
 * ResumePolicy
 *
 * Enforces that users can only access their own resumes.
 * Registered in AuthServiceProvider and used via $this->authorize() in controllers.
 *
 * Usage in controller:
 *   $this->authorize('view', $resume);
 *   $this->authorize('delete', $resume);
 */
class ResumePolicy
{
    /** Can user view this resume? */
    public function view(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    /** Can user update (re-analyze) this resume? */
    public function update(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    /** Can user delete this resume? */
    public function delete(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }
}
