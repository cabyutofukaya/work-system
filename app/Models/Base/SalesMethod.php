<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ReportContent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalesMethod
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ReportContent[] $report_contents
 *
 * @package App\Models\Base
 */
class SalesMethod extends Model
{
	protected $table = 'sales_methods';

	public function report_contents()
	{
		return $this->hasMany(ReportContent::class);
	}
}
