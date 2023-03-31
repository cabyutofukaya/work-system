<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Report;
use App\Models\ReportContent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReportsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user;

    public function setUp(): void
    {
        parent::setUp();

        // メンバー
        $this->user = User::factory()->create();

        // 会社
        Client::factory()->create();
    }

    /**
     * 日報一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testReportsIndex()
    {
        $this->browse(function (Browser $browser) {
            // 日報を作成
            /** @var \App\Models\Report $report */
            $report = Report::factory()->create();

            // 営業日報を生成
            /** @var \App\Models\ReportContent $report_content_sales */
            ReportContent::factory()->create(["report_id" => $report->id, "type" => "sales"]);

            // 業務日報を生成
            /** @var \App\Models\ReportContent $report_content_work */
            ReportContent::factory()->create(["report_id" => $report->id, "type" => "work"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("reports.index")
                ->waitForRoute("reports.index")
                ->assertRouteIs("reports.index")
                ->waitUntilMissing('#nprogress');

            // 直近の日報一覧
            $browser
                // 作成した情報の出現を確認
                ->assertSee($report->toArray()["date"])
                ->assertSee($report->user->name)
                ->assertSee("営業日報")
                ->assertSee("業務日報")
                // 日報個別ページへの遷移
                ->click("@reportShow")
                ->waitForRoute("reports.show", ["report" => $report->getKey()])
                ->assertRouteIs("reports.show", ["report" => $report->getKey()])
                ->back()
                ->waitForRoute("reports.index")
                // 作成ページへの遷移
                ->press("日報を作成する")
                ->waitForRoute("reports.create")
                ->assertRouteIs("reports.create")
                ->back()
                ->waitForRoute("reports.index")
                // 自分の日報ページへの遷移
                ->press("自分の日報を管理")
                ->waitForRoute("reports.mine")
                ->assertRouteIs("reports.mine")
                ->back()
                ->waitForRoute("reports.index");

            // 絞り込み検索ダイアログのテストを省略
            // いいね・閲覧者のテストを省略
        });
    }

    /**
     * 自分の日報一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testReportsMine()
    {
        $this->browse(function (Browser $browser) {
            // すべての日報を削除
            Report::all()->each(function ($report) {
                $report->delete();
            });

            // 日報を作成
            /** @var \App\Models\Report $report */
            $report = Report::factory()->create(["user_id" => $this->user->getKey()]);

            // 営業日報を生成
            ReportContent::factory()->create(["report_id" => $report->id, "type" => "sales"]);

            // 業務日報を生成
            ReportContent::factory()->create(["report_id" => $report->id, "type" => "work"]);

            // 他ユーザの日報を作成 遠い未来の日付で生成
            /** @var \App\Models\Report $user_other */
            $user_other = User::factory()->create();

            /** @var \App\Models\Report $report_other */
            $report_other = Report::factory()->create(["user_id" => $user_other->getKey(), "date" => "2999-12-31"]);

            // 他ユーザの日報に営業日報を生成
            ReportContent::factory()->create(["report_id" => $report_other->getKey(), "type" => "sales"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("reports.mine")
                ->waitForRoute("reports.mine")
                ->assertRouteIs("reports.mine")
                ->waitUntilMissing('#nprogress');

            // 直近の日報一覧
            $browser
                // 作成した情報の出現を確認
                ->assertSee($report->toArray()["date"])
                ->assertSee("営業日報")
                ->assertSee("業務日報")
                // 他ユーザの日報が出現しないことを確認
                ->assertDontSee($report_other->toArray()["date"])
                // 作成ページへの遷移
                ->press("日報を作成する")
                ->waitForRoute("reports.create")
                ->assertRouteIs("reports.create")
                ->back()
                ->waitForRoute("reports.mine")
                // 編集ページへの遷移
                ->click("@reportEdit")
                ->waitForRoute("reports.edit", ["report" => $report->getKey()])
                ->assertRouteIs("reports.edit", ["report" => $report->getKey()])
                ->back()
                ->waitForRoute("reports.mine")
                // 削除ボタン
                ->click("@reportDelete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->waitUntilMissingText($report->toArray()["date"])
                ->assertDontSee($report->toArray()["date"]);

            // 絞り込み検索ダイアログのテストを省略
        });
    }

    /**
     * 日報作成画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testReportsCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("reports.create")
                ->waitForRoute("reports.create")
                ->assertRouteIs("reports.create")
                ->waitUntilMissing('#nprogress');

            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            // v-switch のテストは省略

            // 営業日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_sales */
            $report_content_sales = ReportContent::factory()->make(["report_id" => 1, "type" => "sales"]);

            $browser
                ->press("営業日報を追加する")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_sales) {
                    $browser->type("participants", $report_content_sales->participants);
                    return $browser->assertInputValue("participants", $report_content_sales->participants);
                }, 'Waited for input value participants')
                ->type("description", $report_content_sales->description);

            // v-selectの会社リストのうち先頭の項目を選択
            $browser
                ->clickAtXPath('//*[@name = "client_id"]/../div[contains(@class, "v-select")]')
                ->waitFor('.v-menu__content')
                ->click('.v-menu__content');

            $browser
                ->press("この内容を追加する")
                ->waitUntilMissing(".v-dialog");

            // 業務日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_work */
            $report_content_work = ReportContent::factory()->make(["report_id" => 1, "type" => "work"]);

            $browser
                ->press("業務日報を追加する")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_work) {
                    $browser->type("title", $report_content_work->title);
                    return $browser->assertInputValue("title", $report_content_work->title);
                }, 'Waited for input value participants')
                ->type("description", $report_content_work->description);

            $browser
                ->press("この内容を追加する")
                ->waitUntilMissing(".v-dialog");

            // 日報を作成
            $browser
                ->press("この内容で日報を作成する")
                ->waitForText('日報を作成しました');

            // 作成された日報の情報を取得
            $report_new = Report::first();

            $browser
                ->waitForRoute("reports.show", ["report" => $report_new->id])
                ->waitForRoute("reports.show", ["report" => $report_new->id])
                ->assertRouteIs("reports.show", ["report" => $report_new->id])
                ->waitUntilMissing('#nprogress')
                ->assertSee($report_content_sales->participants)
                ->assertSee($report_content_sales->description)
                ->assertSee($report_content_work->title)
                ->assertSee($report_content_work->description);
        });
    }

    /**
     * 日報編集画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testReportsEdit()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Report $report */
            $report = Report::factory()->create(["user_id" => $this->user->getKey()]);

            /** @var \App\Models\ReportContent $report_content_sales */
            $report_content_sales = ReportContent::factory()->create(["report_id" => $report->id, "type" => "sales"]);

            /** @var \App\Models\ReportContent $report_content_work */
            $report_content_work = ReportContent::factory()->create(["report_id" => $report->id, "type" => "work"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("reports.edit", ["report" => $report->getKey()])
                ->waitForRoute("reports.edit", ["report" => $report->getKey()]);

            // 設定した情報の出現を確認
            $browser
                ->assertSee($report_content_sales->client->name)
                ->assertSee($report_content_sales->client->client_type_name)
                ->assertSee($report_content_sales->participants)
                ->assertSee($report_content_sales->description)
                ->assertSee($report_content_work->title)
                ->assertSee($report_content_work->description);

            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            // v-switch のテストは省略

            /** 営業日報の編集ダイアログ */

            // 営業日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_sales */
            $report_content_sales = ReportContent::factory()->make(["report_id" => 1, "type" => "sales"]);

            $browser
                ->click("@reportContentSalesDialog")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_sales) {
                    $browser
                        ->vuetifyClear("[name='participants']")
                        ->type("participants", $report_content_sales->participants);
                    return $browser->assertInputValue("participants", $report_content_sales->participants);
                }, 'Waited for input value participants')
                ->vuetifyClear("[name='description']")
                ->type("description", $report_content_sales->description);

            // v-selectの会社リストのうち先頭の項目を選択
            $browser
                ->clickAtXPath('//*[@name = "client_id"]/../div[contains(@class, "v-select")]')
                ->waitFor('.v-menu__content')
                ->click('.v-menu__content');

            $browser
                ->press("この内容を反映する")
                ->waitUntilMissing(".v-dialog");

            /** 業務日報の編集ダイアログ */

            // 業務日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_work */
            $report_content_work = ReportContent::factory()->make(["report_id" => 1, "type" => "work"]);

            $browser
                ->click("@reportContentWorkDialog")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_work) {
                    $browser
                        ->vuetifyClear("[name='title']")
                        ->type("title", $report_content_work->title);
                    return $browser->assertInputValue("title", $report_content_work->title);
                }, 'Waited for input value title')
                ->vuetifyClear("[name='description']")
                ->type("description", $report_content_work->description);

            $browser
                ->press("この内容を反映する")
                ->waitUntilMissing(".v-dialog");

            /** 営業日報の追加ダイアログ */

            // 営業日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_sales_add */
            $report_content_sales_add = ReportContent::factory()->make(["report_id" => 1, "type" => "sales"]);

            $browser
                ->press("営業日報を追加する")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_sales_add) {
                    $browser->type("participants", $report_content_sales_add->participants);
                    return $browser->assertInputValue("participants", $report_content_sales_add->participants);
                }, 'Waited for input value participants')
                ->type("description", $report_content_sales_add->description);

            // v-selectの会社リストのうち先頭の項目を選択
            $browser
                ->clickAtXPath('//*[@name = "client_id"]/../div[contains(@class, "v-select")]')
                ->waitFor('.v-menu__content')
                ->click('.v-menu__content');

            $browser
                ->press("この内容を追加する")
                ->waitUntilMissing(".v-dialog");

            // 業務日報の情報を生成
            /** @var \App\Models\ReportContent $report_content_work_add */
            $report_content_work_add = ReportContent::factory()->make(["report_id" => 1, "type" => "work"]);

            $browser
                ->press("業務日報を追加する")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($browser, $report_content_work_add) {
                    $browser->type("title", $report_content_work_add->title);
                    return $browser->assertInputValue("title", $report_content_work_add->title);
                }, 'Waited for input value participants')
                ->type("description", $report_content_work_add->description);

            $browser
                ->press("この内容を追加する")
                ->waitUntilMissing(".v-dialog");

            // 日報を作成
            $browser
                ->press("この内容で日報を更新する")
                ->waitForText('日報の変更を保存しました');

            $browser
                ->waitForRoute("reports.show", ["report" => $report->id])
                ->waitForRoute("reports.show", ["report" => $report->id])
                ->assertRouteIs("reports.show", ["report" => $report->id])
                ->waitUntilMissing('#nprogress')
                ->assertSee($report_content_sales->participants)
                ->assertSee($report_content_sales->description)
                ->assertSee($report_content_work->title)
                ->assertSee($report_content_work->description)
                ->assertSee($report_content_sales_add->participants)
                ->assertSee($report_content_sales_add->description)
                ->assertSee($report_content_work_add->title)
                ->assertSee($report_content_work_add->description);
        });
    }
}
