<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\NoticeFile as BaseNoticeFile;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 日報コンテンツ
 */
class NoticeFile extends BaseNoticeFile implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'notice_id',
        'type',
        'name',
        'path',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['created_at', 'updated_at'];
    // 管理者
    protected array $hiddenAdmin = [];

 
    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['notice'];

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
     * クエリスコープ 非公開の項目を除外する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExceptPrivate(Builder $query): Builder
    {
        return $query->whereHas('notice', function ($query) {
            $query->where('is_private', false);
        });
    }

     /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

    }


}
