<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\User;
use App\Models\NoticeFile;
use App\Models\NoticeVisitor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Notice (Base)
 * 
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property \Illuminate\Database\Eloquent\Collection|NoticeFile[] $notice_files
 * @property \Illuminate\Database\Eloquent\Collection|NoticeVisitor[] $notice_visitors
 */
class Notice extends Model
{
	use SoftDeletes;

	protected $table = 'notices';

	protected $casts = [
		'user_id' => 'int',
	];

	/**
	 * 投稿者（ユーザー）
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * 添付ファイル群
	 */
	public function notice_files(): HasMany
	{
		return $this->hasMany(NoticeFile::class);
	}

	/**
	 * 閲覧履歴（訪問者）
	 */
	public function notice_visitors(): HasMany
	{
		return $this->hasMany(NoticeVisitor::class);
	}
}
