<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\ClientReportUser as BaseClientReportUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会社
 */
class ClientReportUser extends BaseClientReportUser implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'client_id',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['created_at', 'updated_at'];

    // 管理者
    protected array $hiddenAdmin = [];

    /**
     * キャストする必要のある属性
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'client_id' => 'int',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
