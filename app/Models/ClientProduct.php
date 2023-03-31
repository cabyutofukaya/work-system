<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社-商材
 */
class ClientProduct extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'client_product';

    protected $fillable = [
        'client_id',
        'product_id',
    ];

    protected $casts = [
        'client_id' => 'int',
        'product_id' => 'int',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
