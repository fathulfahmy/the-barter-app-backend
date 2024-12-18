<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $barter_invoice_id
 * @property int $barter_service_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoiceBarterService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoiceBarterService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoiceBarterService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoiceBarterService whereBarterInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarterInvoiceBarterService whereBarterServiceId($value)
 */
class BarterInvoiceBarterService extends Pivot
{
    //
}
