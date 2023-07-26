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
class ReportCommentMention extends Model
{
    use SoftDeletes;

    protected $table = 'report_comment_mention';

    protected $casts = [
        'report_comment_id' => 'int',
        'user_id' => 'int',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)
        ->withPivot('id', 'report_comment_id');
    }

    public function comment_mention()
	{
		return $this->belongsToMany(Product::class, 'report_content_product')
					->withPivot('id', 'evaluation_id')
					->withTimestamps();
	}
}
