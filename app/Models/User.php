<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterTransaction> $acquired_barter_transactions
 * @property-read int|null $acquired_barter_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterInvoice> $barter_invoices
 * @property-read int|null $barter_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterReview> $barter_reviews
 * @property-read int|null $barter_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterService> $barter_services
 * @property-read int|null $barter_services_count
 * @property-read mixed $avatar
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterTransaction> $provided_barter_transactions
 * @property-read int|null $provided_barter_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['avatar'];

    public function barter_services(): HasMany
    {
        return $this->hasMany(BarterService::class, 'barter_provider_id');
    }

    public function acquired_barter_transactions(): HasMany
    {
        return $this->hasMany(BarterTransaction::class, 'barter_acquirer_id');
    }

    public function provided_barter_transactions(): HasMany
    {
        return $this->hasMany(BarterTransaction::class, 'barter_provider_id');
    }

    public function barter_invoices(): HasMany
    {
        return $this->hasMany(BarterInvoice::class, 'barter_acquirer_id');
    }

    public function barter_reviews(): HasMany
    {
        return $this->hasMany(BarterReview::class, 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('user_avatar')->singleFile()
            ->useFallbackUrl(config('app.default.image.uri'))
            ->useFallbackPath(public_path(config('app.default.image.uri')))
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100);
            });
    }

    protected function getAvatarAttribute()
    {
        $media = $this->getFirstMedia('user_avatar');

        if ($media) {
            return [
                'uri' => $media->getFullUrl(),
                'name' => $media->file_name,
                'type' => $media->mime_type,
            ];
        }

        return [
            'uri' => config('app.default.image.uri'),
            'name' => config('app.default.image.name'),
            'type' => config('app.default.image.type'),
        ];
    }
}
