<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\LatestEvaluation;
use App\Models\ReportContentProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Evaluation
 * 
 * @property int $id
 * @property string $grade
 * @property string $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|LatestEvaluation[] $latest_evaluations
 * @property Collection|ReportContentProduct[] $report_content_products
 *
 * @package App\Models\Base
 */
class Evaluation extends Model
{
	protected $table = 'evaluations';

	public function latest_evaluations()
	{
		return $this->hasMany(LatestEvaluation::class);
	}

	public function report_content_products()
	{
		return $this->hasMany(ReportContentProduct::class);
	}
}
