<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportLike
 *
 * @property int $id
 * @property int $report_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Report $report
 * @property User $user
 *
 * @package App\Models\Base
 */
class ReportLike extends Model
{
    protected $table = 'report_likes';

    protected $casts = [
        'report_id' => 'int',
        'user_id' => 'int'
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
