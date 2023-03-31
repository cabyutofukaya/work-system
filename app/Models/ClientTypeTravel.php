<?php

namespace App\Models;

use App\Models\Base\ClientTypeTravel as BaseClientTypeTravel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 旅行業者固有情報
 */
class ClientTypeTravel extends BaseClientTypeTravel implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'payment_method',
        'registration_number',
        'group'
    ];

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['client'];
}
