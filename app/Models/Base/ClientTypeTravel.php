<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientTypeTravel
 * 
 * @property int $id
 * @property int $client_id
 * @property string|null $payment_method
 * @property string|null $registration_number
 * @property string|null $group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class ClientTypeTravel extends Model
{
	protected $table = 'client_type_travel';

	protected $casts = [
		'client_id' => 'int'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
