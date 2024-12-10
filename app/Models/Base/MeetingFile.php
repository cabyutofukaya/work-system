<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Meeting;
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
class MeetingFile extends Model
{
	use SoftDeletes;
	protected $table = 'meeting_files';

	protected $casts = [
		'meeting_id' => 'int',
		'name' => 'string',
		'type' => 'string',
	];

	public function meeting()
	{
		return $this->belongsTo(Meeting::class);
	}

	
}
