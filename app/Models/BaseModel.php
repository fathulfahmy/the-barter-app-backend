<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 */
class BaseModel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use LogsActivity;

    protected $hidden = [
        'media',
        'pivot',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'barter_provider_id' => 'string',
        'barter_acquirer_id' => 'string',
        'awaiting_user_id' => 'string',
        'reviewer_id' => 'string',
        'reporter_id' => 'string',
        'barter_service_id' => 'string',
        'barter_transaction_id' => 'string',
        'barter_invoice_id' => 'string',
        'barter_review_id' => 'string',
        'user_report_id' => 'string',
        'user_report_reason_id' => 'string',
        'model_id' => 'string',
        'min_price' => 'float',
        'max_price' => 'float',
        'amount' => 'float',
        'rating' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
