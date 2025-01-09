<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $barter_acquirer_id
 * @property int $barter_provider_id
 * @property int $barter_service_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $barter_acquirer
 * @property-read \App\Models\BarterInvoice|null $barter_invoice
 * @property-read \App\Models\User $barter_provider
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterReview> $barter_reviews
 * @property-read int|null $barter_reviews_count
 * @property-read \App\Models\BarterService $barter_service
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BarterTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereBarterAcquirerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereBarterProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereBarterServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction withoutTrashed()
 *
 * @property string|null $awaiting_completed_user_id
 * @property-read \App\Models\User|null $awaiting_completed_user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterTransaction whereAwaitingCompletedUserId($value)
 */
class BarterTransaction extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barter_acquirer_id',
        'barter_provider_id',
        'awaiting_completed_user_id',
        'barter_service_id',
        'status',
    ];

    /* ======================================== RELATIONSHIPS */
    public function barter_acquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_acquirer_id');
    }

    public function barter_provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_provider_id');
    }

    public function awaiting_completed_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awaiting_completed_user_id');
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
