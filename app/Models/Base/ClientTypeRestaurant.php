<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientTypeRestaurant
 * 
 * @property int $id
 * @property int $client_id
 * @property array|null $languages
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class ClientTypeRestaurant extends Model
{
	protected $table = 'client_type_restaurant';

	protected $casts = [
		'client_id' => 'int',
		'languages' => 'json'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
