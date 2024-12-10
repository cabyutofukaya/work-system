<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * メンバー
 */
class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'tel',
        'department',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'updated_at',
    ];
    // 管理者
    protected array $hiddenAdmin = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 1ページあたりの表示件数
     *
     * @var int
     */
    protected $perPage = 20;

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

        static::deleted(function ($user) {
            // 論理削除のためユニーク制約のあるログインID、メールアドレスが同じユーザを再作成できるようリネームする
            $user->fill([
                "username" => $user->username . "-deleted_at-" . now()->format('YmdHis'),
                "email" => $user->email . "-deleted_at-" . now()->format('YmdHis'),
            ])->save();

            // 削除したユーザをToDoの社内担当者から除外する
            SalesTodoParticipant::where("user_id", $user->id)->delete();
            OfficeTodoParticipant::where("user_id", $user->id)->delete();
        });
    }

    public function report_visitors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReportVisitor::class);
    }

    public function meeting_visitors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MeetingVisitor::class);
    }

    public function notice_visitors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NoticeVisitor::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'user_product')->withTimestamps();
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

        /**
     * クエリスコープ 特定の会社タイプ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $client_type_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfClientType(\Illuminate\Database\Eloquent\Builder $query, mixed $user_id): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('type_id', $user_id);
    }

}
