<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Product;
use App\Models\ReportContent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LatestEvaluation
 * 
 * @property int $id
 * @property int $client_id
 * @property int $product_id
 * @property int $evaluation_id
 * @property int $report_content_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 * @property Evaluation $evaluation
 * @property Product $product
 * @property ReportContent $report_content
 *
 * @package App\Models\Base
 */
class LatestEvaluation extends Model
{
	protected $table = 'latest_evaluations';

	protected $casts = [
		'client_id' => 'int',
		'product_id' => 'int',
		'evaluation_id' => 'int',
		'report_content_id' => 'int'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function evaluation()
	{
		return $this->belongsTo(Evaluation::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function report_content()
	{
		return $this->belongsTo(ReportContent::class);
	}
}
