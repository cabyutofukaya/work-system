<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\OfficeTodo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfficeTodoParticipant
 * 
 * @property int $id
 * @property int $office_todo_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property OfficeTodo $office_todo
 * @property User $user
 *
 * @package App\Models\Base
 */
class OfficeTodoParticipant extends Model
{
	protected $table = 'office_todo_participants';

	protected $casts = [
		'office_todo_id' => 'int',
		'user_id' => 'int'
	];

	public function office_todo()
	{
		return $this->belongsTo(OfficeTodo::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
