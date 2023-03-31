<?php

namespace App\Models;

use App\Models\Base\ClientTypeRestaurant as BaseClientTypeRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * レストラン会社固有情報
 */
class ClientTypeRestaurant extends BaseClientTypeRestaurant implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'languages'
    ];

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['client'];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(function ($client_type_restaurant) {
            // 言語にnullが渡されたら空配列で保存する
            // Laravel-Admin側仕様に統一のため
            if (empty($client_type_restaurant->languages)) {
                $client_type_restaurant->languages = [];
            }
        });
    }
}
