<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\ReportContent as BaseReportContent;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 日報コンテンツ
 */
class ReportContent extends BaseReportContent implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'report_id',
        'type',
        'title',
        'client_id',
        'branch_id',
        'participants',
        'sales_method_id',
        'description',
        'is_complaint',
        'is_zaitaku',
        'product_description',
        'file',
        'file_name',
        'required_time',
        'departments',
        'position',
        'hidden',
        'product_bikou',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'product_evaluation'];
    // 管理者
    protected array $hiddenAdmin = [];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = [
        "type_name",
        "product_evaluation",
    ];

    /**
     * 更新日時を更新すべき全リレーション
     *
     * @var array
     */
    protected $touches = ['report'];

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

        // 日報の最後の報告が削除される場合は例外を起こす
        static::deleting(function ($report_content) {
            $report_id = ReportContent::find($report_content["id"])->report_id;

            if (ReportContent::where("report_id", $report_id)->count() === 1) {
                throw new \Exception("日報からすべての報告を削除することはできません");
            }
        });

        // 最新の評価の更新 日報コンテンツ情報が変更された場合に再生成する
        static::saved(function ($report_content) {
            if ($report_content["client_id"]) {
                AppHelper::latestEvaluationsUpdate($report_content["client_id"]);
            }
        });
        static::deleted(function ($report_content) {
            if ($report_content["client_id"]) {
                AppHelper::latestEvaluationsUpdate($report_content["client_id"]);
            }
        });
    }

    /**
     * アクセサ 日報タイプ名
     *
     * @noinspection PhpUnused
     */
    public function getTypeNameAttribute()
    {
        return config("const.report_content_type." . $this->type . ".name");
    }

    /**
     * アクセサ 商材に対する評価
     *
     * @noinspection PhpUnused
     */
    public function getProductEvaluationAttribute(): object
    {
        $product_evaluation = [];

        foreach ($this->products as $product) {
            $product_evaluation[$product->id] = $product->pivot->evaluation_id;
        }
        return (object)$product_evaluation;
    }

    /**
     * クエリスコープ 特定の報告タイプ
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
     * クエリスコープ 非公開の項目を除外する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExceptPrivate(Builder $query): Builder
    {
        return $query->whereHas('report', function ($query) {
            $query->where('is_private', false);
        });
    }

    /**
     * 削除済みを含む会社情報を得する会社するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    /**
     * 削除済みを含む営業所情報を取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * @return mixed
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * 商材情報リレーション
     *
     * カスタム中間テーブルモデルの定義するためusingを追加した内容にBaseのメソッドを上書き
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'report_content_product')
            ->using(ReportContentProduct::class)
            ->withPivot('id', 'evaluation_id')
            ->withTimestamps();
    }

    public function report_content_contact_person(): HasMany
    {
        return $this->hasMany(ReportContentContanctPerson::class);
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
