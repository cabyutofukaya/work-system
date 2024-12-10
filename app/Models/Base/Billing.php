<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BillingCategory;
use App\Models\BillingType;

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
class Billing extends Model
{
	use SoftDeletes;
	protected $table = 'billings';

	protected $casts = [
        'category_id' => 'int',
        'year' => 'string',
        'month' => 'string',
        'budget' => 'int',
        'achievements' => 'int',
        'count	' => 'int',
		'profit	' => 'int',
	];

    public function billing_category()
	{
		return $this->belongsTo(BillingCategory::class);
	}

	public function billing_type()
	{
		return $this->belongsTo(BillingType::class);
	}
}
