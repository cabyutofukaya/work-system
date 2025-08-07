<?php

namespace App\Models;

use App\Models\Base\MeetingLike as BaseMeetingLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use DateTimeInterface;

/**
 * 会議記録いいねモデル
 */
class MeetingLike extends BaseMeetingLike
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
    ];

    /**
     * デフォルトで非表示にする属性
     */
    protected $hidden = ['updated_at'];

    /**
     * 管理者用に非表示項目を変更したい場合に使用
     */
    protected array $hiddenAdmin = [];

    /**
     * モデル起動時の処理
     */
    protected static function booted(): void
    {
        // 管理画面アクセス時に hidden を切り替える
        if (Auth::guard('admin')->check() && request()->routeIs('admin.*')) {
            static::retrieved(function ($model) {
                $model->setHidden($model->hiddenAdmin);
            });
        }
    }

    /**
     * 関連ユーザー（論理削除されたユーザーも取得）
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * JSONシリアライズ時の日付フォーマット
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i');
    }
}
