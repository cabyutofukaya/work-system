<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社-商材
 */
class ScheduleGuest extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'schedule_guests';

    protected $fillable = [
        'schedule_id',
        'user_id',
    ];

    protected $casts = [
        'schedule_id' => 'int',
        'user_id' => 'int',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
