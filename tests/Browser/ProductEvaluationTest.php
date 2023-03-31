<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportContent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductEvaluationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user;

    public function setUp(): void
    {
        parent::setUp();

        // メンバー
        $this->user = User::factory()->create();
    }

    /**
     * 商材の評価
     *
     * @return void
     * @throws \Throwable
     */
    public function testProductEvaluation()
    {
        $this->browse(function (Browser $browser) {
            // 会社を生成
            /** @var \App\models\Client $client */
            $client = Client::factory()->create();

            // 日報を生成
            /** @var \App\Models\Report $report */
            $report = Report::factory()->create();

            // 営業日報を生成
            /** @var \App\Models\ReportContent $report_content */
            $report_content = ReportContent::factory()->create(["report_id" => $report, "type" => "sales", 'client_id' => $client->id]);

            // 商材の評価を生成
            /** @var \App\Models\Product $products */
            $products = Product::get();

            /** @var \Illuminate\Database\Eloquent\Collection $evaluations */
            $evaluations = Evaluation::get();

            foreach ($products as $product) {
                $report_content->products()->attach(
                    $product["id"],
                    ['evaluation_id' => $evaluations->random()["id"]]
                );
            }

            $browser
                ->loginAs($this->user)
                ->visitRoute("product-evaluations")
                ->waitForRoute("product-evaluations")
                ->waitUntilMissing('#nprogress');

            // 商材名と評価ラベルの出現を確認
            foreach ($products as $product) {
                /** @var \App\Models\Product $product */
                $browser->assertSee($product->name);
            }
            foreach ($evaluations as $evaluation) {
                /** @var \App\Models\Evaluation $evaluation */
                $browser->assertSee($evaluation->label);
            }

            // グラフ自体のテストは省略
        });
    }
}
