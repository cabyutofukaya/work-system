<?php

namespace App\Models;

use App\Models\Base\NoticeVisitor as BaseNoticeVisitor;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 社内連絡事項既読管理
 */
class NoticeVisitor extends BaseNoticeVisitor
{
    use HasFactory;

    protected $fillable = [
        'notice_id',
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

}
