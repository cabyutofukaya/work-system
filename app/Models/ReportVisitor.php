<?php

namespace App\Models;

use App\Models\Base\ReportVisitor as BaseReportVisitor;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 日報閲覧者
 */
class ReportVisitor extends BaseReportVisitor
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id'
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['updated_at'];
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

        // 削除済みメンバーは閲覧者から除外する
        static::addGlobalScope('without_deleted_user', function (Builder $builder) {
            $builder->whereHas('user', function (Builder $query) {
                $query->whereNull('deleted_at');
            });
        });
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
