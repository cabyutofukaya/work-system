<?php

namespace App\Models;

use App\Models\Base\Genre as BaseGenre;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * ジャンル
 */
class Genre extends BaseGenre implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'client_type_id',
        'name'
    ];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // 日付順に並べる
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('genres.id');
        });
    }

    /**
     * クエリスコープ 特定の会社タイプ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $client_type_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfClientType(\Illuminate\Database\Eloquent\Builder $query, mixed $client_type_id): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('client_type_id', $client_type_id);
    }
}
