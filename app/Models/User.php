<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use App\Traits\Suspendable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
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
 *
 * @property string $role
 * @property-read mixed $is_admin
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isNotAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 *
 * @property \Illuminate\Support\Carbon|null $suspension_starts_at
 * @property \Illuminate\Support\Carbon|null $suspension_ends_at
 * @property int|null $suspension_reason_id
 * @property-read mixed $is_suspended_permanently
 * @property-read mixed $is_suspended_temporarily
 * @property-read \App\Models\UserReportReason|null $suspension_reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserReport> $user_reports
 * @property-read int|null $user_reports_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspensionEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspensionReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspensionStartsAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isSuspendedPermanently()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isSuspendedTemporarily()
 *
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankName($value)
 */
#[ObservedBy([UserObserver::class])]
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, FilamentUser
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasApiTokens, Notifiable, SoftDeletes;
    use Notifiable, Suspendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bank_name',
        'bank_account_number',
        'role',
        'suspension_starts_at',
        'suspension_ends_at',
        'suspension_reason_id',
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
            'suspension_starts_at' => 'datetime',
            'suspension_ends_at' => 'datetime',
        ];
    }

    /* ======================================== SCOPES */
    protected $with = [
        'suspension_reason:id,name',
    ];

    public function scopeIsAdmin(Builder $query): void
    {
        $query->where('role', 'admin');
    }

    public function scopeIsNotAdmin(Builder $query): void
    {
        $query->whereNot('role', 'admin');
    }

    /* ======================================== RELATIONSHIPS */
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

    public function user_reports(): HasMany
    {
        return $this->hasMany(UserReport::class, 'author_id');
    }

    public function suspension_reason(): BelongsTo
    {
        return $this->belongsTo(UserReportReason::class, 'suspension_reason_id');
    }

    /* ======================================== ATTRIBUTES */
    protected $appends = ['avatar'];

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

    protected function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    /* ======================================== PACKAGES */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('user_avatar')
            ->singleFile()
            ->useFallbackUrl(config('app.default.image.uri'))
            ->useFallbackPath(config('app.default.image.path'))
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100);
            });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }
}
