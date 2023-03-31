<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use App\Models\LatestEvaluation;
use App\Models\ReportContent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Client[] $clients
 * @property Collection|LatestEvaluation[] $latest_evaluations
 * @property Collection|ReportContent[] $report_contents
 * @property Collection|User[] $users
 *
 * @package App\Models\Base
 */
class Product extends Model
{
	protected $table = 'products';

	public function clients()
	{
		return $this->belongsToMany(Client::class)
					->withPivot('id')
					->withTimestamps();
	}

	public function latest_evaluations()
	{
		return $this->hasMany(LatestEvaluation::class);
	}

	public function report_contents()
	{
		return $this->belongsToMany(ReportContent::class, 'report_content_product')
					->withPivot('id', 'evaluation_id')
					->withTimestamps();
	}

	public function users()
	{
		return $this->belongsToMany(User::class, 'user_product')
					->withPivot('id')
					->withTimestamps();
	}
}
