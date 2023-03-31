<?php

namespace App\Models;

use App\Models\Base\ClientTypeTaxibus as BaseClientTypeTaxibus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * タクシー・バス会社固有情報
 */
class ClientTypeTaxibus extends BaseClientTypeTaxibus implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'membership_fee',
        'fee_taxi_cab',
        'fee_taxi_tabinoashi',
        'fee_bus_cab',
        'fee_bus_tabinoashi',
        'category',
        'has_dr_sightseeing',
        'has_dr_female',
        'has_dr_language_english',
        'has_dr_language_chinese',
        'has_dr_language_korean',
        'has_dr_language_other',
        'has_wheelchair',
        'has_baby_seat',
        'has_child_seat',
        'fee_child_seat',
        'has_junior_seat',
        'fee_junior_seat',
        'is_bus_association_member',
        'has_safety_mark'
    ];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = [
        "category_name",
    ];

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['client'];

    /**
     * アクセサ カテゴリ名
     *
     * @noinspection PhpUnused
     */
    public function getCategoryNameAttribute()
    {
        return config("const.client_types.taxibus.categories." . $this->category);
    }

}
