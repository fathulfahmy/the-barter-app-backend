<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $datetime
 * @property string|null $address
 * @property array<array-key, mixed>|null $deliverables
 * @property string|null $note
 * @property string $user_id
 * @property string $barter_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\BarterTransaction $barter_transaction
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\BarterRemarkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereBarterTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereDeliverables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterRemark withoutTrashed()
 */
class BarterRemark extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'barter_transaction_id',
        'datetime',
        'address',
        'deliverables',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
            'deliverables' => 'array',
        ];
    }

    /* ======================================== RELATIONSHIPS */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barter_transaction(): BelongsTo
    {
        return $this->belongsTo(BarterTransaction::class, 'barter_transaction_id');
    }
}
