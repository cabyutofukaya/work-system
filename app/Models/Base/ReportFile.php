<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\LatestEvaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportContentLike;
use App\Models\SalesMethod;
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
 * @property string $name
 * @property string $path
 * 
 *
 * @package App\Models\Base
 */
class ReportFile extends Model
{
	use SoftDeletes;
	protected $table = 'report_files';

	protected $casts = [
		'report_id' => 'int',
		'name' => 'string',
		'path' => 'string',
		'type' => 'string',
		'is_deleted' => 'int'
	];
	
	public function report()
	{
		return $this->belongsTo(Report::class);
	}

}
