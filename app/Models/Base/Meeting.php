<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\MeetingComment;
use App\Models\MeetingLike;
use App\Models\MeetingVisitor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Meeting
 * 
 * @property int $id
 * @property Carbon $started_at
 * @property string $title
 * @property int $user_id
 * @property string|null $participants
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Collection|MeetingComment[] $meeting_comments
 * @property Collection|MeetingLike[] $meeting_likes
 * @property Collection|MeetingVisitor[] $meeting_visitors
 *
 * @package App\Models\Base
 */
class Meeting extends Model
{
	protected $table = 'meetings';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $dates = [
		'started_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function meeting_comments()
	{
		return $this->hasMany(MeetingComment::class);
	}

	public function meeting_likes()
	{
		return $this->hasMany(MeetingLike::class);
	}

	public function meeting_visitors()
	{
		return $this->hasMany(MeetingVisitor::class);
	}
}
