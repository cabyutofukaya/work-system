<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Branch;
use App\Models\Client;
use App\Models\LatestEvaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportContentLike;
use App\Models\SalesMethod;
use App\Models\ContactPerson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ReportContent
 * 
 * @property int $id
 * @property int $report_id
 * @property string $type
 * @property string|null $title
 * @property int|null $client_id
 * @property int|null $branch_id
 * @property string|null $participants
 * @property int|null $sales_method_id
 * @property string|null $description
 * @property bool $is_complaint
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Branch|null $branch
 * @property Client|null $client
 * @property Report $report
 * @property SalesMethod|null $sales_method
 * @property Collection|LatestEvaluation[] $latest_evaluations
 * @property Collection|ReportContentLike[] $report_content_likes
 * @property Collection|Product[] $products
 *
 * @package App\Models\Base
 */
class ReportContent extends Model
{
	use SoftDeletes;
	protected $table = 'report_contents';

	protected $casts = [
		'report_id' => 'int',
		'client_id' => 'int',
		'branch_id' => 'int',
		'sales_method_id' => 'int',
		'is_complaint' => 'bool'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function report()
	{
		return $this->belongsTo(Report::class);
	}

	public function sales_method()
	{
		return $this->belongsTo(SalesMethod::class);
	}

	public function latest_evaluations()
	{
		return $this->hasMany(LatestEvaluation::class);
	}

	public function report_content_likes()
	{
		return $this->hasMany(ReportContentLike::class);
	}

	public function products()
	{
		return $this->belongsToMany(Product::class, 'report_content_product')
			->withPivot('id', 'evaluation_id')
			->withTimestamps();
	}

	public function contact_persons()
	{
		return $this->belongsToMany(ContactPerson::class, 'report_content_contact_person')
			->withPivot('id')
			->withTimestamps();
	}
}
