<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Notice;
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
class NoticeFile extends Model
{
	use SoftDeletes;
	protected $table = 'notice_files';

	protected $casts = [
		'notice_id' => 'int',
		'name' => 'string',
		'path' => 'string',
		'type' => 'string',
		'is_deleted' => 'int'
	];

	public function notice()
	{
		return $this->belongsTo(Notice::class);
	}

	
}
