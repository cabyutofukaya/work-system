<?php

namespace App\Models;

use App\Events\LatestEvaluationUpdate;
use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 日報コンテンツ-商材
 */
class ReportContentProduct extends Pivot
{
    protected $table = 'report_content_product';

    protected $fillable = [
        'report_content_id',
        'product_id',
        'evaluation_id'
    ];

    protected $casts = [
        'report_content_id' => 'int',
        'product_id' => 'int',
        'evaluation_id' => 'int'
    ];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function booted()
    {
        // 最新の評価の更新 評価情報が変更された場合に再生成する
        static::saved(function ($report_content_product) {
            AppHelper::latestEvaluationsUpdate(ReportContent::find($report_content_product["report_content_id"])->client_id);
        });
        static::deleted(function ($report_content_product) {
            AppHelper::latestEvaluationsUpdate(ReportContent::find($report_content_product["report_content_id"])->client_id);
        });
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function report_content(): BelongsTo
    {
        return $this->belongsTo(ReportContent::class);
    }
}
