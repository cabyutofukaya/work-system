<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ReportContent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportContentLike
 *
 * @property int $id
 * @property int $report_content_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property ReportContent $report_content
 * @property User $user
 *
 * @package App\Models\Base
 */
class ReportContentLike extends Model
{
    protected $table = 'report_content_likes';

    protected $casts = [
        'report_content_id' => 'int',
        'user_id' => 'int'
    ];

    public function report_content()
    {
        return $this->belongsTo(ReportContent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
