<?php

namespace App\Models;

use App\Models\Base\Room as BaseRoom;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 施設予約
 */
class Room extends BaseRoom implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
    ];
}
