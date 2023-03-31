<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowProductEvaluationRequest;
use App\Models\Evaluation;
use App\Models\ReportContent;
use App\Models\SalesMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class ProductEvaluationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ShowProductEvaluationRequest $request
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function __invoke(ShowProductEvaluationRequest $request): Response|ResponseFactory
    {
        $product_evaluations = [];

        // 評価
        $evaluations = Evaluation::get();

        // 営業手段
        $sales_methods = SalesMethod::get();

        // ログイン中ユーザの会社・商材ごとの最新の評価を取得
        $user_latest_evaluations = [];

        // 自分の日報を取得
        $report_contents = ReportContent
            ::with([
                "report",
                "products.pivot.evaluation",
                "sales_method:id,name",
                "client:id,name",
            ])
            ->whereHas('report', function ($query) {
                $query->where("user_id", Auth::id());
            })
            ->where("type", "sales")
            // 更新日時逆に並べる グローバルスコープで指定済みだが明示する
            ->orderByDesc('updated_at');

        // 営業日報をループ
        foreach ($report_contents->get() as $report_content) {
            // 商材の評価をループ
            foreach ($report_content->product_evaluation as $product_id => $evaluation_id) {
                // まだ評価が保存されていなければ最新の評価として保存
                if (empty($evaluation_exist_check[$report_content->client_id][$product_id])) {
                    $user_latest_evaluations[] = [
                        "client_id" => $report_content->client_id,
                        "product_id" => $product_id,
                        "evaluation_id" => $evaluation_id,
                        "report_content" => $report_content->toArray(),
                    ];

                    $evaluation_exist_check[$report_content->client_id][$product_id] = true;
                }
            }
        }

        // 担当商材ごとにループ
        foreach (auth()->user()->products()->get() as $product) {
            // この担当商材の自分の最新の評価を取得
            $latest_evaluations = collect($user_latest_evaluations)->where("product_id", $product->id);

            // 日付範囲絞り込み
            if ($request->validated('date_start')) {
                $latest_evaluations = $latest_evaluations->filter(function ($collection) use ($request) {
                    $date_report = new Carbon($collection["report_content"]["report"]["date"]);
                    $date_start = new Carbon($request->validated('date_start'));

                    return $date_report->gte($date_start);
                });
            }

            if ($request->validated('date_end')) {
                $latest_evaluations = $latest_evaluations->filter(function ($collection) use ($request) {
                    $date_report = new Carbon($collection["report_content"]["report"]["date"]);
                    $date_end = new Carbon($request->validated('date_end'));

                    return $date_report->lte($date_end);
                });
            }

            $evaluation_clients_count = [];
            $evaluation_clients = [];
            foreach ($evaluations as $evaluation) {
                $latest_evaluations_filtered = $latest_evaluations->where("evaluation_id", $evaluation->id);

                // 評価ごとの会社数 グラフデータセット用
                $evaluation_clients_count[$evaluation->id] = $latest_evaluations_filtered->count();

                // この評価の営業手段ごとの内訳件数
                $sales_methods_count = [];
                foreach ($sales_methods as $sales_method) {
                    $count = $latest_evaluations_filtered->pluck("report_content.sales_method")->where('id', $sales_method->id)->count();

                    if (!$count) {
                        continue;
                    }

                    $sales_methods_count[] = [
                        "id" => $sales_method->id,
                        "name" => $sales_method->name,
                        "count" => $count,
                    ];
                }

                // 評価ごとの会社リスト
                $evaluation_clients[] = [
                    "evaluation" => $evaluation,
                    "latest_evaluations" => $latest_evaluations_filtered,
                    // 会社数 リスト用
                    "latest_evaluations_count" => $latest_evaluations_filtered->count(),
                    // 営業手段ごとの内訳件数
                    "sales_methods_count" => $sales_methods_count,
                ];
            }

            $product_evaluations[] = [
                // 商材情報
                "product" => $product,
                // 全件数
                "count" => $latest_evaluations->count(),
                // 評価ごとの件数
                "evaluation_clients_count" => $evaluation_clients_count,
                // 評価ごとの会社リスト
                "evaluation_clients" => $evaluation_clients,
            ];
        }

        return inertia('ProductEvaluations', [
            'product_evaluations' => $product_evaluations,
            'evaluations' => $evaluations,
            'sales_methods' => $sales_methods,

            // 検索フォームの初期値
            'form_params' => [
                'date_start' => $request->validated('date_start'),
                'date_end' => $request->validated('date_end'),
            ],
        ]);
    }
}
