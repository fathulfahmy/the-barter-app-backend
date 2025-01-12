<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserSuspended;
use App\Notifications\UserUnsuspended;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updating" event.
     */
    public function updated(User $user): void
    {
        $user->load('suspension_reason');

        $is_suspension_starts_at_dirty = $user->wasChanged('suspension_starts_at');
        $is_suspension_ends_at_dirty = $user->wasChanged('suspension_ends_at');

        $is_being_suspended =
            ($is_suspension_starts_at_dirty && $user->suspension_starts_at) ||
            ($is_suspension_ends_at_dirty && $user->suspension_starts_at);

        $is_being_unsuspended =
            ($is_suspension_starts_at_dirty && ! $user->suspension_starts_at);

        if ($is_being_suspended) {
            $user->notify(new UserSuspended($user));
        }

        if ($is_being_unsuspended) {
            $user->notify(new UserUnsuspended($user));
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
