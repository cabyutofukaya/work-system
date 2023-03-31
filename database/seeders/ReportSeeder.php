<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportContent;
use App\Models\ReportContentLike;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $products = Product::get();
        $evaluations = Evaluation::get();

        Report::factory()
            ->count(100)
            ->create()
            // hasメソッドではランダムな個数のリレーションを生成できないためeachで代替
            ->each(function (Report $report) use ($products, $evaluations) {
                // 日報コンテンツ 3階層のリレーションを生成
                ReportContent::factory()
                    ->times(random_int(1, 3))
                    ->create(['report_id' => $report->id])
                    ->each(function ($report_content) use ($products, $evaluations) {
                        // 日報コンテンツいいね
                        $report_content->report_content_likes()->saveMany(
                            ReportContentLike::factory()->times(random_int(0, 2))->make()
                        );

                        // 商材評価
                        if ($report_content->type === "sales") {
                            $report_content->products()->detach();
                            $products->random(rand(1, $products->count()))->each(function ($product) use ($report_content, $evaluations) {
                                $report_content->products()->attach(
                                    $product["id"],
                                    ['evaluation_id' => $evaluations->random()["id"]]
                                );
                            });
                        }
                    });

                // 日報コメント 0-2件設定
                $report->report_comments()->saveMany(
                    ReportComment::factory()->times(random_int(0, 2))->make()
                );
            });
    }
}
