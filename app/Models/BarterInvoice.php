<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarterInvoice extends BaseModel
{
    use SoftDeletes;

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

    protected $appends = ['exchanged_services'];

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

    public function getExchangedServicesAttribute()
    {
        return $this->barter_services->pluck('title');
    }
}
