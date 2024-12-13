<?php

namespace App\Policies;

use App\Models\BarterInvoice;
use App\Models\User;

class BarterInvoicePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BarterInvoice $barter_invoice): bool
    {
        return $user->id == $barter_invoice->barter_acquirer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BarterInvoice $barter_invoice): bool
    {
        return $user->id == $barter_invoice->barter_acquirer_id;
    }
}
