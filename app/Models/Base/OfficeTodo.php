<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\OfficeTodoParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OfficeTodo
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon $scheduled_at
 * @property string $title
 * @property string $description
 * @property bool $is_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|OfficeTodoParticipant[] $office_todo_participants
 *
 * @package App\Models\Base
 */
class OfficeTodo extends Model
{
	use SoftDeletes;
	protected $table = 'office_todos';

	protected $casts = [
		'user_id' => 'int',
		'is_completed' => 'bool'
	];

	protected $dates = [
		'scheduled_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function office_todo_participants()
	{
		return $this->hasMany(OfficeTodoParticipant::class);
	}
}
