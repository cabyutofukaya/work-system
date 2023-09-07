<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Models\Base\Report as BaseReport;
use App\Models\Traits\WhereLike;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * 日報
 */
class Report extends BaseReport implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use WhereLike;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'date',
        'user_id',
        'is_private',
        'comment_updated_at',
        'created_at',
        'draft_flg',
    ];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    // メンバー
    protected $hidden = ['updated_at', "fiscal_year"];
    // 管理者
    protected array $hiddenAdmin = ["fiscal_year"];

    /**
     * キャストする属性
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'is_private' => 'bool',
        // 親クラスの値に追加
        'date' => 'date:Y-m-d',
        'time' => 'string',
    ];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = [
        "date_string",
        "fiscal_year",
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

        // 更新日時逆に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            // $builder->orderByDesc('updated_at')->orderByDesc('id');
            $builder->orderByDesc('created_at')->orderByDesc('id');
        });

        // 最新の評価の更新 日付・非公開情報が変更された場合に再生成する
        static::saved(function ($report) {
            $client_ids = Report::find($report["id"])->report_contents->where("type", "sales")->pluck("client_id")->unique();

            foreach ($client_ids as $client_id) {
                AppHelper::latestEvaluationsUpdate($client_id);
            }
        });
        static::deleted(function ($report) {
            $client_ids = Report::withTrashed()->find($report["id"])->report_contents->where("type", "sales")->pluck("client_id")->unique();

            foreach ($client_ids as $client_id) {
                AppHelper::latestEvaluationsUpdate($client_id);
            }
        });
    }

    /**
     * アクセサ 文字列としての日付
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getDateStringAttribute(): string
    {
        return $this->date->toDateString();
    }

    /**
     * アクセサ 7月始まりの年度
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getFiscalYearAttribute(): string
    {
        return $this->date->startOfMonth()->subMonths(6)->year;
    }

    /**
     * クエリスコープ 非公開の項目を除外する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExceptPrivate(Builder $query): Builder
    {
        return $query->where('is_private', false);
    }

     /**
     * クエリスコープ 下書きの項目を除外する
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExceptDraftFlg(Builder $query): Builder
    {
        return $query->where('draft_flg', 0);
    }


    /**
     * 削除済みのメンバーを含む会社情報を得する会社するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function report_content(): BelongsTo
    {
        return $this->belongsTo(ReportContent::class)->withTrashed();
    }


    /**
     * 営業日報のみを取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function report_contents_sales(): HasMany
    {
        return $this->report_contents()->ofType('sales');
    }

    /**
     * 業務日報のみを取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function report_contents_work(): HasMany
    {
        return $this->report_contents()->ofType('work');
    }

    /**
     * いいね情報を取得するリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function report_content_likes(): HasManyThrough
    {
        return $this->hasManyThrough(ReportContentLike::class, ReportContent::class);
    }

    /**
     * 最新コメントの取得
     */
    public function latest_comment(): HasOne
    {
        // GROUP BY clause and contains non aggregated column エラーを回避するためグローバルスコープを削除
        return $this->hasOne(ReportComment::class)->withoutGlobalScopes()->latestOfMany();
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


    //既読をつける
    protected function is_readed(int $report,int $user)
    {
        
    }
}
