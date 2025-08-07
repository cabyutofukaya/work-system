<?php

namespace App\Models;

use App\Models\Base\Meeting as BaseMeeting;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会議記録
 */
class Meeting extends BaseMeeting implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'started_at',
        'title',
        'user_id',
        'participants',
        'content'
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 管理者用の非表示属性
     *
     * @var array
     */
    protected array $hiddenAdmin = [];

    /**
     * 1ページあたりの表示件数
     *
     * @var int
     */
    protected $perPage = 100;

    /**
     * コンストラクタ
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // 管理ページへのアクセスであれば
        if (!app()->runningInConsole() && auth('admin')->check() && request()->routeIs('admin.*')) {
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

        // 開催日時逆順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('started_at')->orderByDesc('id');
        });
    }

    /**
     * 削除済みのメンバーを含むユーザー情報を取得するリレーション
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
