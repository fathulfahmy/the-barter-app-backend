<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarterService extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barter_provider_id',
        'barter_category_id',
        'title',
        'description',
        'min_price',
        'max_price',
        'rating',
        'status',
    ];

    public function barter_provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_provider_id');
    }

    public function barter_category(): BelongsTo
    {
        return $this->belongsTo(BarterCategory::class,'barter_category_id');
    }

    public function barter_transactions(): HasMany
    {
        return $this->hasMany(BarterTransaction::class,'barter_service_id');
    }

    public function barter_reviews(): HasMany
    {
        return $this->hasMany(BarterReview::class, 'barter_service_id');
    }

    public function barter_invoices(): BelongsToMany
    {
        return $this->belongsToMany(BarterInvoice::class)->using(BarterInvoiceBarterService::class);
    }
}
