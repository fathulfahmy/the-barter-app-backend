<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BarterInvoice extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barter_acquirer_id',
        'barter_transaction_id',
        'amount',
        'status',
    ];

    public function barter_acquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_acquirer_id');
    }

    public function barter_transaction(): BelongsTo
    {
        return $this->belongsTo(BarterTransaction::class, 'barter_transaction_id');
    }

    public function barter_services(): BelongsToMany
    {
        return $this->belongsToMany(BarterService::class)->using(BarterInvoiceBarterService::class);
    }
}
