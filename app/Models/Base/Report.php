<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ReportComment;
use App\Models\ReportContent;
use App\Models\ReportVisitor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ReportCommentUser;

/**
 * Class Report
 * 
 * @property int $id
 * @property Carbon $date
 * @property int $user_id
 * @property bool $is_private
 * @property Carbon|null $comment_updated_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|ReportComment[] $report_comments
 * @property Collection|ReportContent[] $report_contents
 * @property Collection|ReportVisitor[] $report_visitors
 *
 * @package App\Models\Base
 */
class Report extends Model
{
	use SoftDeletes;
	protected $table = 'reports';

	protected $casts = [
		'user_id' => 'int',
		'is_private' => 'bool'
	];

	protected $dates = [
		'date',
		'comment_updated_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function report_comments()
	{
		return $this->hasMany(ReportComment::class);
	}

	public function report_contents()
	{
		return $this->hasMany(ReportContent::class);
	}

	public function report_visitors()
	{
		return $this->hasMany(ReportVisitor::class);
	}

	public function report_comment_users()
	{
		return $this->hasMany(ReportCommentUser::class);
	}
}
