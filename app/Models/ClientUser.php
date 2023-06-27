<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社-商材
 */
class ClientUser extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'client_user';

    protected $fillable = [
        'client_id',
        'user_id',
    ];

    protected $casts = [
        'client_id' => 'int',
        'user_id' => 'int',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
