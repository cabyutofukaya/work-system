<?php

namespace App\Models;

use App\Models\Base\NoticeFile as BaseNoticeFile;
use App\Models\Traits\WhereLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 社内連絡事項添付ファイル
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
        'file_name',
        'file_path',
        'file_extension',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['created_at', 'updated_at'];
    // 管理者

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['notice'];


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
