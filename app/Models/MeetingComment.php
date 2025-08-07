<?php

namespace App\Models;

use App\Models\Base\MeetingComment as BaseMeetingComment;
use App\Models\Traits\WhereLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会議記録コメント
 */
class MeetingComment extends BaseMeetingComment implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    /**
     * 一括代入可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'meeting_id',
        'user_id',
        'comment',
    ];
}
