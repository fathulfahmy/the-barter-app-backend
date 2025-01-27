<?php

namespace App\Policies;

use App\Models\BarterRemark;
use App\Models\User;

class BarterRemarkPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BarterRemark $barter_remark): bool
    {
        return $user->id == $barter_remark->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BarterRemark $barter_remark): bool
    {
        return $user->id == $barter_remark->user_id;
    }
}
