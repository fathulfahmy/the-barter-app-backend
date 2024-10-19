<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, InteractsWithMedia, Notifiable, SoftDeletes;

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
        return $this->hasMany(BarterReview::class,'author_id');
    }
}
