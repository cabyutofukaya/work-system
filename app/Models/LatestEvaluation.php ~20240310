<?php

namespace App\Models;

use App\Models\Base\LatestEvaluation as BaseLatestEvaluation;
use Illuminate\Database\Eloquent\Builder;

/**
 * 最新の評価
 */
class LatestEvaluation extends BaseLatestEvaluation
{
    protected $fillable = [
        'client_id',
        'product_id',
        'evaluation_id',
        'report_content_id'
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // 商材ID順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('product_id');
        });
    }
}
