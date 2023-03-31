<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use App\Models\SalesTodoParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SalesTodo
 * 
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property Carbon $scheduled_at
 * @property string $contact_person
 * @property string $description
 * @property bool $is_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Client $client
 * @property User $user
 * @property Collection|SalesTodoParticipant[] $sales_todo_participants
 *
 * @package App\Models\Base
 */
class SalesTodo extends Model
{
	use SoftDeletes;
	protected $table = 'sales_todos';

	protected $casts = [
		'user_id' => 'int',
		'client_id' => 'int',
		'is_completed' => 'bool'
	];

	protected $dates = [
		'scheduled_at'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function sales_todo_participants()
	{
		return $this->hasMany(SalesTodoParticipant::class);
	}
}
