<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BarterTransaction extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barter_acquirer_id',
        'barter_provider_id',
        'barter_service_id',
        'status',
    ];

    public function barter_acquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_acquirer_id');
    }

    public function barter_provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_provider_id');
    }

    public function barter_service(): BelongsTo
    {
        return $this->belongsTo(BarterService::class, 'barter_service_id');
    }

    public function barter_invoice(): HasOne
    {
        return $this->hasOne(BarterInvoice::class, 'barter_transaction_id');
    }

    public function barter_reviews(): HasMany
    {
        return $this->hasMany(BarterReview::class, 'barter_transaction_id');
    }
}
