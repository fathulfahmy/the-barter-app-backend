<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BaseModel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $casts = [
        'min_price' => 'float',
        'max_price' => 'float',
        'amount' => 'float',
        'rating' => 'float',
    ];
}
