<?php

namespace App\Models;

use App\Events\LatestEvaluationUpdate;
use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 日報コンテンツ-商材
 */
class ReportCommentMention extends Pivot
{
    protected $table = 'report_comment_mention';

    protected $fillable = [
        'report_comment_id',
        'user_id',
    ];

    protected $casts = [
        'report_content_id' => 'int',
        'product_id' => 'int',
    ];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function booted()
    {
    
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report_comment(): BelongsTo
    {
        return $this->belongsTo(ReportComment::class);
    }
}
