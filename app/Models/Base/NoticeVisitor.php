<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MeetingVisitor
 * 
 * @property int $id
 * @property int $meeting_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Notice $notice
 * @property User $user
 *
 * @package App\Models\Base
 */
class NoticeVisitor extends Model
{
	protected $table = 'notice_visitors';

	protected $casts = [
		'notice_id' => 'int',
		'user_id' => 'int'
	];

	public function notice()
	{
		return $this->belongsTo(Notice::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
