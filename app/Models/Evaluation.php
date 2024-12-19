<?php

namespace App\Models;

use App\Models\Base\Evaluation as BaseEvaluation;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;

/**
 * 評価
 */
class Evaluation extends BaseEvaluation implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'grade',
        'label'
    ];


    protected static function boot()
    {
        parent::boot();

        // ID順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('sort');
        });
    }
}
