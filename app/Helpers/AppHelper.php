<?php

namespace App\Helpers;

use App\Models\LatestEvaluation;
use App\Models\ReportContent;
use Illuminate\Support\Facades\DB;

class AppHelper
{
    /**
     * vagrant share等でのテストのためローカル環境ではURLをフォワードプロキシのホスト名で書き換える
     *
     * @param String $url
     * @return String
     */
    public static function replaceUrlWithForwardedHost(string $url): string
    {
        $forwarded_proto = $_SERVER["HTTP_X_FORWARDED_PROTO"] ?? null;
        $forwarded_host = $_SERVER["HTTP_X_FORWARDED_HOST"] ?? $_SERVER["HTTP_X_ORIGINAL_HOST"] ?? null;
        if (app()->environment('local') && $forwarded_proto && $forwarded_host) {
            $url = preg_replace("/^https?/", $forwarded_proto, $url);
            $url = preg_replace("/(?<=:\/\/)[^\/]+/", $forwarded_host, $url);
        }

        return $url;
    }

    /**
     * 最新の評価の更新
     * モデルイベントから呼び出される
     *
     * @param $client_id
     * @return void
     * @throws \Throwable
     */
    public static function latestEvaluationsUpdate($client_id)
    {
        // 指定された会社IDが含まれる営業日報を新しい順に取得
        $report_contents = ReportContent
            ::with(["report", "products"])
            ->where("type", "sales")
            ->where("client_id", $client_id)
            // 非公開の日報に含まれる評価を除外
            ->exceptPrivate()
            ->get();

        // 日報の日付でソート
        $report_contents = collect($report_contents)->sortBy(
            [
                // 日報日付の逆順
                fn($a, $b) => $b["report"]["date"] <=> $a["report"]["date"],
                // 同日であれば後に書かれたものを優先
                fn($a, $b) => $b['id'] <=> $a['id'],
            ])->toArray();

        // 新しいものからループして最新の評価を取得
        $product_evaluation = [];
        foreach ($report_contents as $report_content) {
            foreach ($report_content["products"] as $product) {
                // 既に取得済みの商材はスキップ
                if (!array_key_exists($product["id"], $product_evaluation)) {
                    $product_evaluation[$product["id"]] = [
                        "client_id" => $client_id,
                        "product_id" => $product["id"],
                        "evaluation_id" => $product["pivot"]["evaluation_id"],
                        "report_content_id" => $report_content["id"],
                    ];
                }
            }
        }

        DB::transaction(function () use ($client_id, $product_evaluation) {
            // 既存の評価を削除
            LatestEvaluation::where("client_id", $client_id)->delete();

            // 最新の評価を保存
            foreach ($product_evaluation as $value) {
                LatestEvaluation::create($value);
            }
        });
    }
}
