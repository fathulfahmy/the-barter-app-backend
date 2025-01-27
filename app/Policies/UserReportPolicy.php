<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserReport;

class UserReportPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserReport $user_report): bool
    {
        return $user->id == $user_report->reporter_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserReport $user_report): bool
    {
        return $user->id == $user_report->reporter_id;
    }
}
