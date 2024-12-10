<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use App\Models\ContactPerson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ContactPerson
 * 
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string|null $email
 * @property string|null $tel
 * @property string|null $department
 * @property string|null $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class ContactPersonImage extends Model
{
	use SoftDeletes;
	protected $table = 'contact_person_images';

	protected $casts = [
		'contact_person_id' => 'int',
		'name' => 'string'
	];

}
