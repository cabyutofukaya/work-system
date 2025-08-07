<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MeetingVisitor
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
class MeetingVisitor extends Model
{
	protected $table = 'meeting_visitors';

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
