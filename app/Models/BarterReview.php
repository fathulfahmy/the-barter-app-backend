<?php

namespace App\Models;

use App\Observers\BarterReviewObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $description
 * @property float $rating
 * @property string $reviewer_id
 * @property string|null $barter_service_id
 * @property string $barter_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\BarterService|null $barter_service
 * @property-read \App\Models\BarterTransaction $barter_transaction
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $reviewer
 *
 * @method static \Database\Factories\BarterReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereBarterServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereBarterTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterReview withoutTrashed()
 */
#[ObservedBy([BarterReviewObserver::class])]
class BarterReview extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reviewer_id',
        'barter_service_id',
        'barter_transaction_id',
        'description',
        'rating',
    ];

    /* ======================================== RELATIONSHIPS */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function barter_service()
    {
        return $this->belongsTo(BarterService::class, 'barter_service_id')->withTrashed();
    }

    public function barter_transaction(): BelongsTo
    {
        return $this->belongsTo(BarterTransaction::class, 'barter_transaction_id');
    }
}
