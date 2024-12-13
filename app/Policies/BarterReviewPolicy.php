<?php

namespace App\Policies;

use App\Models\BarterReview;
use App\Models\User;

class BarterReviewPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BarterReview $barter_review): bool
    {
        return $user->id == $barter_review->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BarterReview $barter_review): bool
    {
        return $user->id == $barter_review->author_id;
    }
}
