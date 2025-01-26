<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $suspended_users
 * @property-read int|null $suspended_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserReport> $user_reports
 * @property-read int|null $user_reports_count
 *
 * @method static \Database\Factories\UserReportReasonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReportReason withoutTrashed()
 */
class UserReportReason extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function user_reports(): HasMany
    {
        return $this->hasMany(UserReport::class, 'user_report_reason_id');
    }

    public function suspended_users(): HasMany
    {
        return $this->hasMany(User::class, 'suspension_reason_id');
    }
}
