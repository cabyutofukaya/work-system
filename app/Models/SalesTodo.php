<?php

namespace App\Models;

use App\Models\Base\SalesTodo as BaseSalesTodo;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 営業ToDo
 */
class SalesTodo extends BaseSalesTodo implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'client_id',
        'scheduled_at',
        'contact_person',
        'description',
        'is_completed'
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

        // 未対応の項目を直近から将来へと並べ、
        // 未対応がなくなれば対応済みの項目を新しいから並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('is_completed')->orderByRaw('CAST(scheduled_at as SIGNED) * IF(is_completed = 0, 1, -1)')->orderByDesc('id');
        });
    }

    /**
     * 社内担当者情報を取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function participant_users(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, SalesTodoParticipant::class);
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
     * 削除済みを含む会社情報を得する会社するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
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
