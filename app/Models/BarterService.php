<?php

namespace App\Models;

use App\Enums\BarterServiceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $barter_provider_id
 * @property int $barter_category_id
 * @property string $title
 * @property string $description
 * @property float $min_price
 * @property float $max_price
 * @property string $price_unit
 * @property float $rating
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\BarterCategory $barter_category
 * @property-read \App\Models\BarterInvoiceBarterService|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterInvoice> $barter_invoices
 * @property-read int|null $barter_invoices_count
 * @property-read \App\Models\User $barter_provider
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterReview> $barter_reviews
 * @property-read int|null $barter_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterTransaction> $barter_transactions
 * @property-read int|null $barter_transactions_count
 * @property-read int $completed_count
 * @property-read mixed $images
 * @property-read int $pending_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BarterServiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereBarterCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereBarterProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService wherePriceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterService withoutTrashed()
 *
 * @property-read mixed $reviews_count
 */
class BarterService extends BaseModel
{
    use SoftDeletes;

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
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BarterServiceStatus::class,
        ];
    }

    /* ======================================== RELATIONSHIPS */
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

    /* ======================================== ATTRIBUTES */
    protected $appends = [
        'rating',
        'pending_count',
        'completed_count',
        'reviews_count',
        'images',
    ];

    protected function getImagesAttribute()
    {
        return $this->getMedia('barter_service_images')->map(function ($media) {
            return [
                'uri' => $media->getFullUrl(),
                'name' => $media->file_name,
                'type' => $media->mime_type,
            ];
        })->toArray();
    }

    protected function getRatingAttribute()
    {
        $rating = $this->barter_reviews()->avg('rating');

        return $rating ? round($rating, 2) : 0;
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

    protected function getReviewsCountAttribute()
    {
        return $this->barter_reviews()->count();
    }

    /* ======================================== PACKAGES */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('barter_service_images')
            ->useFallbackUrl(config('app.default.image.uri'))
            ->useFallbackPath(public_path(config('app.default.image.uri')))
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(300)
                    ->height(300);
            });
    }
}
