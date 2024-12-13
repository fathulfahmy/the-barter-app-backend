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
        'price_unit',
        'rating',
        'status',
    ];

    protected $appends = ['pending_count', 'completed_count'];

    public function barter_provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_provider_id');
    }

    public function barter_category(): BelongsTo
    {
        return $this->belongsTo(BarterCategory::class, 'barter_category_id');
    }

    public function barter_transactions(): HasMany
    {
        return $this->hasMany(BarterTransaction::class, 'barter_service_id');
    }

    public function barter_reviews(): HasMany
    {
        return $this->hasMany(BarterReview::class, 'barter_service_id');
    }

    public function barter_invoices(): BelongsToMany
    {
        return $this->belongsToMany(BarterInvoice::class)->using(BarterInvoiceBarterService::class);
    }

    protected function getPendingCountAttribute(): int
    {
        return $this->barter_transactions()->where('status', 'pending')->count();
    }

    protected function getCompletedCountAttribute(): int
    {
        $completed_transactions = $this->barter_transactions()
            ->where('status', 'completed')->count();
        $completed_invoices = $this->barter_invoices()
            ->whereHas('barter_transaction', function ($query) {
                $query->where('status', 'completed');
            })
            ->count();

        $result = $completed_transactions + $completed_invoices;

        return $result;
    }
}
