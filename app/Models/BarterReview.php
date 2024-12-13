<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarterReview extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'barter_service_id',
        'barter_transaction_id',
        'description',
        'rating',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function barter_service(): BelongsTo
    {
        return $this->belongsTo(BarterService::class, 'barter_service_id');
    }

    public function barter_transaction(): BelongsTo
    {
        return $this->belongsTo(BarterTransaction::class, 'barter_transaction_id');
    }
}
