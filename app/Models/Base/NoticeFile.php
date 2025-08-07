<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class NoticeFile (Base)
 * 
 * @property int $id
 * @property int $notice_id
 * @property string $file_name
 * @property string $file_path
 * @property string $file_extension
 * @property string $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Notice $notice
 *
 * @package App\Models\Base
 */
class NoticeFile extends Model
{
	use SoftDeletes;

	protected $table = 'notice_files';

	protected $casts = [
		'notice_id' => 'int',
		'file_name'      => 'string',
		'file_path'      => 'string',
		'file_extension'      => 'string',
		'type'      => 'string',
	];

	/**
	 * 社内連絡事項とのリレーション
	 */
	public function notice(): BelongsTo
	{
		return $this->belongsTo(Notice::class);
	}
}
