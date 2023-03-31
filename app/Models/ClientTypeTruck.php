<?php

namespace App\Models;

use App\Models\Base\ClientTypeTruck as BaseClientTypeTruck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * トラック会社固有情報
 */
class ClientTypeTruck extends BaseClientTypeTruck implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'drivers_count'
    ];

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['client'];
}
