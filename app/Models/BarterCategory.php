<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterService> $barter_services
 * @property-read int|null $barter_services_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BarterCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory whereUpdatedAt($value)
 *
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterCategory withoutTrashed()
 */
class BarterCategory extends BaseModel
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

    public function barter_services(): HasMany
    {
        return $this->hasMany(BarterService::class, 'barter_category_id');
    }
}
