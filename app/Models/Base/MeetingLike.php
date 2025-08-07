<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class MeetingLike
 * 
 * @property int $id
 * @property int $meeting_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Meeting $meeting
 * @property User $user
 */
class MeetingLike extends Model
{
	protected $table = 'meeting_likes';

	protected $casts = [
		'meeting_id' => 'int',
		'user_id' => 'int',
	];

	/**
	 * 関連会議記録
	 */
	public function meeting(): BelongsTo
	{
		return $this->belongsTo(Meeting::class);
	}

	/**
	 * 関連ユーザー
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
