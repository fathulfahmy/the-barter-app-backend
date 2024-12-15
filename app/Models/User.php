<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
