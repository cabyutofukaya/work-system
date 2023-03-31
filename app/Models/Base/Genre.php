<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Genre
 * 
 * @property int $id
 * @property string $client_type_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Client[] $clients
 *
 * @package App\Models\Base
 */
class Genre extends Model
{
	protected $table = 'genres';

	public function clients()
	{
		return $this->belongsToMany(Client::class)
					->withPivot('id')
					->withTimestamps();
	}
}
