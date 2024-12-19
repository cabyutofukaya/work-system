<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Models\Base\Product as BaseProduct;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商材
 */
class Product extends BaseProduct implements Auditable
{
    use EagerLoadPivotTrait;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name'
    ];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // // 日付順に並べる
        // static::addGlobalScope('order', function (Builder $builder) {
        //     $builder->orderBy('id');
        // });
    }
}
