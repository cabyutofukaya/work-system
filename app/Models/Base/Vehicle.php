<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vehicle
 * 
 * @property int $id
 * @property int $client_id
 * @property string|null $image
 * @property string $type
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class Vehicle extends Model
{
	use SoftDeletes;
	protected $table = 'vehicles';

	protected $casts = [
		'client_id' => 'int'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
