<?php

namespace App\Policies;

use App\ApiResponse;
use App\Models\BarterService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BarterServicePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BarterService $barter_service): bool
    {
        return $user->id === $barter_service->barter_provider_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BarterService $barter_service): bool
    {
        return $user->id === $barter_service->barter_provider_id;
    }
}
