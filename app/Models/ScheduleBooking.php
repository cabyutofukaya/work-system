<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社-商材
 */
class ScheduleBooking extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'schedule_booking';

    protected $fillable = [
        'schedule_id',
        'booking_id',
    ];

    protected $casts = [
        'schedule_id' => 'int',
        'booking_id' => 'int',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
