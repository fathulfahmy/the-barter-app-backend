<?php

namespace App\Policies;

use App\Models\BarterTransaction;
use App\Models\User;

class BarterTransactionPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BarterTransaction $barter_transaction): bool
    {
        return $user->id === $barter_transaction->barter_acquirer_id || $user->id === $barter_transaction->barter_provider_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BarterTransaction $barter_transaction): bool
    {
        return $user->id === $barter_transaction->barter_acquirer_id;
    }
}
