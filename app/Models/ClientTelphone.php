<?php

namespace App\Models;

use App\Models\Base\ClientTelphone as BaseClientTelphone;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 担当者
 */
class ClientTelphone extends BaseClientTelphone implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'name',
        'tel',
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
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['client'];

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

        // ID順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id');
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
