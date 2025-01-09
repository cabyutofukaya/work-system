<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 * 
 * @property int $id
 * @property string $name
 * 
 *
 * @package App\Models\Base
 */
class Room extends Model
{
	protected $table = 'rooms';

	protected $casts = [
		'name' => 'string'
	];

}
