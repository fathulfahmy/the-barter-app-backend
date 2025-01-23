<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $author_id
 * @property string $user_report_reason_id
 * @property string $model_id
 * @property string $model_name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $author
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\UserReportReason $user_report_reason
 *
 * @method static \Database\Factories\UserReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereUserReportReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport withoutTrashed()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 */
class UserReport extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'user_report_reason_id',
        'model_id',
        'model_name',
        'status',
    ];

    protected $with = ['user_report_reason:id,name'];

    /* ======================================== RELATIONSHIPS */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function user_report_reason(): BelongsTo
    {
        return $this->belongsTo(UserReportReason::class, 'user_report_reason_id');
    }
}
