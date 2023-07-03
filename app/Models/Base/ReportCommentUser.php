<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ReportComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportContentLike
 *
 * @property int $id
 * @property int $report_comment_id
 * @property int $user_id
 *  @property int $is_readed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property ReportComment $report_comment
 * @property User $user
 *
 * @package App\Models\Base
 */
class ReportCommentUser extends Model
{
    protected $table = 'report_comment_users';

    protected $casts = [
        'report_comment_id' => 'int',
        'user_id' => 'int'
    ];

    public function report_comment()
    {
        return $this->belongsTo(ReportComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
