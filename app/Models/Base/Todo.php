<?php

namespace App\Models\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Todo (Base)
 * 
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 */
class Todo extends Model
{
	use SoftDeletes;

	protected $table = 'todos';

	protected $casts = [
		'user_id' => 'int',
		'date' => 'date', // ← Carbonインスタンスとして扱いたい場合
		'is_done' => 'int',
	];

	protected $fillable = [
		'user_id',
		'title',
		'date',
		'is_done'
	];

	/**
	 * 投稿者（ユーザー）
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
