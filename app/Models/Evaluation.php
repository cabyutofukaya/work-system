<?php

namespace App\Models;

use App\Models\Base\Evaluation as BaseEvaluation;
use OwenIt\Auditing\Contracts\Auditable;

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
}
