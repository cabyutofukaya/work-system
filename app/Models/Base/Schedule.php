<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Schedule extends Model
{
    use SoftDeletes;

    protected $table = 'schedules';

    protected $fillable = [
        'title',
        'category',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'all_day',
        'content',
        'is_public',
        'notification',
    ];

    protected $casts = [
        'type' => 'integer',
        'user_id' => 'integer',
        'all_day' => 'boolean',
        'is_public' => 'boolean',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
