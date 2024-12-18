<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $author_id
 * @property int $barter_service_id
 * @property int $barter_transaction_id
 * @property string $description
 * @property float $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\BarterService $barter_service
 * @property-read \App\Models\BarterTransaction $barter_transaction
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BarterReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereBarterServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereBarterTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview withoutTrashed()
 */
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
