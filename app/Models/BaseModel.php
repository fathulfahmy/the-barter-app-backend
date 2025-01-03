<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
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

    protected $hidden = [
        'media',
        'pivot',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'barter_provider_id' => 'string',
        'barter_acquirer_id' => 'string',
        'author_id' => 'string',
        'barter_service_id' => 'string',
        'barter_transaction_id' => 'string',
        'barter_invoice_id' => 'string',
        'barter_review_id' => 'string',
        'chat_conversation_id' => 'string',
        'chat_message_id' => 'string',
        'min_price' => 'float',
        'max_price' => 'float',
        'amount' => 'float',
        'rating' => 'float',
    ];
}
