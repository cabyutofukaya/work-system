<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 * 
 * @property int $id
 * @property Carbon $started_at
 * @property string $title
 * @property int $user_id
 * 
 * @property User $user
 *
 * @package App\Models\Base
 */
class Booking extends Model
{
	protected $table = 'bookings';

	protected $casts = [
		'user_id' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function room()
	{
		return $this->belongsTo(Room::class);
	}
}
