<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\Client as BaseClient;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 会社
 */
class Client extends BaseClient implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_type_id',
        'image',
        'name',
        'name_kana',
        'postcode',
        'prefecture',
        'address',
        'lat',
        'lng',
        'url',
        'email',
        'representative',
        'tel',
        'fax',
        'business_hours',
        'description',
        'contact',
        'representative_position',
        'representative_kana',
        'representative_tel',

        'name_position',
        'type_name',

        'business_name',
        'business_name_kana'
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
        'lat' => 'float',
        'lng' => 'float',
    ];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = [
        "client_type_name",
        "client_type_icon",
        "image_url",
        "icon_image_url",
    ];

    /**
     * 1ページあたりの表示件数
     *
     * @var int
     */
    protected $perPage = 100;

    /**
     * コンストラクタ
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // 管理ページへのアクセスであれば
        if (Auth('admin')->check() && request()->routeIs('admin.*')) {
            // hiddenを変更
            $this->setHidden($this->hiddenAdmin);
        }
    }

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // ID逆順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('id');
        });
    }

    /**
     * アクセサ 会社タイプ名
     *
     * @noinspection PhpUnused
     */
    public function getClientTypeNameAttribute()
    {
        return Collect(config("const.client_types." . $this->client_type_id))->get("name");
    }

    /**
     * アクセサ 会社タイプアイコン
     *
     * @noinspection PhpUnused
     */
    public function getClientTypeIconAttribute()
    {
        return Collect(config("const.client_types." . $this->client_type_id))->get("icon");
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
     * クエリスコープ 特定の会社タイプ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $client_type_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfClientType(Builder $query, mixed $client_type_id): Builder
    {
        return $query->where('client_type_id', $client_type_id);
    }

    /**
     * 相手先担当者リレーション
     *
     * 自動生成されるリレーションは contact_people となるため別名を定義
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contact_persons(): HasMany
    {
        return $this->hasMany(ContactPerson::class);
    }

    /**
     * タクシーのみを取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles_taxi(): HasMany
    {
        return $this->vehicles()->ofType('taxi');
    }

    /**
     * バスのみを取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles_bus(): HasMany
    {
        return $this->vehicles()->ofType('bus');
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


    public function client_report_user(): HasMany
    {
        return $this->hasMany(ClientReportUser::class);
    }
}
