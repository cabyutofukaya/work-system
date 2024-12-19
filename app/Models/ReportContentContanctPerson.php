<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会社-商材
 */
class ReportContentContanctPerson extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // use SoftDeletes;

    protected $table = 'report_content_contact_person';

    protected $fillable = [
        'report_content_id',
        'contact_person_id',
    ];

    protected $casts = [
        'report_content_id' => 'int',
        'contact_person_id' => 'int',
    ];

    public function contant_person(): BelongsTo
    {
        return $this->belongsTo(ContactPerson::class);
    }

    public function report_content(): BelongsTo
    {
        return $this->belongsTo(ReportContent::class);
    }
}
