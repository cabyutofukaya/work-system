<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ReportComment
 *
 * @property int $id
 * @property int $report_id
 * @property int $user_id
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Report $report
 * @property User $user
 *
 * @package App\Models\Base
 */
class ReportComment extends Model
{
    use SoftDeletes;

    protected $table = 'report_comments';

    protected $casts = [
        'report_id' => 'int',
        'user_id' => 'int',
        'mention_id' => 'int',
        'mention_name' => 'string',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
