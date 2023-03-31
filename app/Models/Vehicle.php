<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\Vehicle as BaseVehicle;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 車両
 */
class Vehicle extends BaseVehicle implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_id',
        'image',
        'type',
        'name',
        'description'
    ];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = [
        "type_name",
        "image_url",
        "icon_image_url",
    ];


    /**
     * アクセサ 車両タイプ名
     *
     * @noinspection PhpUnused
     */
    public function getTypeNameAttribute()
    {
        return config("const.vehicle_types." . $this->type);
    }


    /**
     * アクセサ 画像URL
     *
     * @return String|null
     * @noinspection PhpUnused
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return AppHelper::replaceUrlWithForwardedHost(route("img.cache", ["template" => "common", "filename" => $this->image]));
    }

    /**
     * アクセサ 画像アイコンURL
     *
     * @return String|null
     * @noinspection PhpUnused
     */
    public function getIconImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return AppHelper::replaceUrlWithForwardedHost(route("img.cache", ["template" => "icon", "filename" => $this->image]));
    }

    /**
     * クエリスコープ 特定のタイプ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType(Builder $query, mixed $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * 配列/JSONシリアル化の日付の準備
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format("Y-m-d H:i");
    }
}
