<?php

namespace App\Models;

use App\Models\Base\Todo as BaseTodo;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property User $user
 */
class Todo extends BaseTodo implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'date',
        'title',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $hidden = ['updated_at'];

    protected array $hiddenAdmin = [];

    protected $perPage = 10;

    /**
     * 投稿者（削除済も含む）
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * グローバルスコープ（最新順）
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('date'); // ← created_at ではなく日付カラムを基準に
        });
    }

    /**
     * JSON出力時の日付フォーマット
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format("Y-m-d H:i");
    }

    public function getDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
}
