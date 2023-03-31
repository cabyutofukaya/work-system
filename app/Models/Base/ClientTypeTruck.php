<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientTypeTruck
 * 
 * @property int $id
 * @property int $client_id
 * @property string|null $drivers_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class ClientTypeTruck extends Model
{
	protected $table = 'client_type_truck';

	protected $casts = [
		'client_id' => 'int'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
