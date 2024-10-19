<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarterCategory extends BaseModel
{
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
