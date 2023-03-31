<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社-ジャンル
 */
class ClientGenre extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'client_genre';

    protected $fillable = [
        'client_id',
        'genre_id',
    ];

    protected $casts = [
        'client_id' => 'int',
        'genre_id' => 'int',
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(genre::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
