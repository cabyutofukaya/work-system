<?php

namespace App\Models;

use App\Models\Base\ReportComment as BaseReportComment;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 日報コメント
 */
class ReportComment extends BaseReportComment implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'report_id',
        'user_id',
        'comment'
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['updated_at', 'deleted_at'];
    // 管理者
    protected array $hiddenAdmin = [];

    /**
     * コンストラクタ
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // 管理ページへのアクセスであれば
        if (Auth('admin')->check() && request()->routeIs('admin.*')) {
            // hiddenを変更
            $this->setHidden($this->hiddenAdmin);
        }
    }

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // ID逆順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('id');
        });

        // 親テーブルのコメント更新日時カラムを更新
        static::created(function ($report_comment) {
            $report = Report::find($report_comment["report_id"]);
            $report->fill(["comment_updated_at" => now()]);
            $report->timestamps = false; // updated_atを更新しない
            $report->save();
        });
    }

    /**
     * 削除済みのメンバーを含む会社情報を得する会社するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * 配列/JSONシリアル化の日付の準備
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format("Y-m-d H:i");
    }
}
