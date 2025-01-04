<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $barter_acquirer_id
 * @property int $barter_transaction_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $barter_acquirer
 * @property-read \App\Models\BarterInvoiceBarterService|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarterService> $barter_services
 * @property-read int|null $barter_services_count
 * @property-read \App\Models\BarterTransaction $barter_transaction
 * @property-read mixed $exchanged_services
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BarterInvoiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereBarterAcquirerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereBarterTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoice withoutTrashed()
 */
class BarterInvoice extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barter_acquirer_id',
        'barter_transaction_id',
        'amount',
    ];

    protected $with = ['barter_services'];

    public function barter_acquirer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barter_acquirer_id');
    }

    public function barter_transaction(): BelongsTo
    {
        return $this->belongsTo(BarterTransaction::class, 'barter_transaction_id');
    }

    public function barter_services(): BelongsToMany
    {
        return $this->belongsToMany(BarterService::class)->using(BarterInvoiceBarterService::class);
    }
}
