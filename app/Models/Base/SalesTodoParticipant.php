<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\SalesTodo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalesTodoParticipant
 * 
 * @property int $id
 * @property int $sales_todo_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property SalesTodo $sales_todo
 * @property User $user
 *
 * @package App\Models\Base
 */
class SalesTodoParticipant extends Model
{
	protected $table = 'sales_todo_participants';

	protected $casts = [
		'sales_todo_id' => 'int',
		'user_id' => 'int'
	];

	public function sales_todo()
	{
		return $this->belongsTo(SalesTodo::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
