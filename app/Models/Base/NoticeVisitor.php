<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class NoticeVisitor (Base)
 * 
 * @property int $id
 * @property int $notice_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Notice $notice
 * @property User $user
 *
 * @package App\Models\Base
 */
class NoticeVisitor extends Model
{
	protected $table = 'notice_visitors';

	protected $casts = [
		'notice_id' => 'int',
		'user_id'   => 'int',
	];

	/**
	 * 社内連絡事項とのリレーション
	 */
	public function notice(): BelongsTo
	{
		return $this->belongsTo(Notice::class);
	}

	/**
	 * ユーザーとのリレーション
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
