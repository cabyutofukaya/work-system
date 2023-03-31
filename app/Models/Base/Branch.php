<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use App\Models\ReportContent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Branch
 * 
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string|null $postcode
 * @property string|null $prefecture
 * @property string|null $address
 * @property string|null $lat
 * @property string|null $lng
 * @property string|null $tel
 * @property string|null $fax
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Client $client
 * @property Collection|ReportContent[] $report_contents
 *
 * @package App\Models\Base
 */
class Branch extends Model
{
	use SoftDeletes;
	protected $table = 'branches';

	protected $casts = [
		'client_id' => 'int'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function report_contents()
	{
		return $this->hasMany(ReportContent::class);
	}
}
