<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MeetingComment
 * 
 * @property int $id
 * @property int $meeting_id
 * @property int $user_id
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Meeting $meeting
 * @property User $user
 *
 * @package App\Models\Base
 */
class MeetingComment extends Model
{
	use SoftDeletes;
	protected $table = 'meeting_comments';

	protected $casts = [
		'meeting_id' => 'int',
		'user_id' => 'int'
	];

	public function meeting()
	{
		return $this->belongsTo(Meeting::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
