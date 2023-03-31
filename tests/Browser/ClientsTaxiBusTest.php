<?php

namespace Tests\Browser;

use App\Models\Branch;
use App\Models\BusinessDistrict;
use App\Models\Client;
use App\Models\ClientTypeTaxibus;
use App\Models\ContactPerson;
use App\Models\Report;
use App\Models\ReportContent;
use App\Models\SalesTodo;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ClientsTaxiBusTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\Client */
    private Collection|Model|Client $client;

    public function setUp(): void
    {
        parent::setUp();

        // メンバー
        $this->user = User::factory()->create();

        // 会社
        $this->client = Client::factory()->create(["client_type_id" => "taxibus"]);

        // 固有情報
        ClientTypeTaxibus::factory()->create(['client_id' => $this->client->id]);
    }

    /**
     * 一覧
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTaxiBusIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);

            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name)
                ->assertSee($this->client->description);

            // "新規作成" ボタン
            $browser
                ->press("会社を作成する")
                ->waitForRoute("client-types.clients.create", ["client_type" => "taxibus"])
                ->assertRouteIs("client-types.clients.create", ["client_type" => "taxibus"]);
        });
    }

    /**
     * 検索ダイアログ
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTaxiBusSearch()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);

            // "地図から探す" "新規作成" ボタン
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->press("地図から探す")
                ->waitForRoute("client-types.clients.map", ["client_type" => "taxibus"])
                ->assertRouteIs("client-types.clients.map", ["client_type" => "taxibus"])
                ->back()
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->press("新規作成")
                ->waitForRoute("client-types.clients.create", ["client_type" => "taxibus"])
                ->assertRouteIs("client-types.clients.create", ["client_type" => "taxibus"]);

            // ワード検索 会社名
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"]) // waitForRouteはクエリパラメータを除いたURLを待機する
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='word']")
                ->type("word", $this->client->name)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            // ワード検索 会社名ふりがな
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='word']")
                ->type("word", $this->client->name_kana)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            // ワード検索 電話番号
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='word']")
                ->type("word", $this->client->tel)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            // 所在地検索 会社
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='address']")
                ->type("address", $this->client->prefecture . $this->client->address)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            /** @var \App\Models\Client $branch */
            $branch = Branch::factory()->create(['client_id' => $this->client->id]);

            // 所在地検索 営業所
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='address']")
                ->type("address", $branch->prefecture . $branch->address)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            /** @var \App\Models\BusinessDistrict $business_district */
            $business_district = BusinessDistrict::factory()->create(['client_id' => $this->client->id]);

            // 営業エリア
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='business_district']")
                ->type("business_district", $business_district->prefecture . $business_district->address)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            // カテゴリー・ジャンルはv-selectのテストが煩雑なため省略

            /** @var \App\Models\Vehicle $vehicle */
            $vehicle = Vehicle::factory()->create(['client_id' => $this->client->id]);

            // 保有車両 名前
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='vehicle']")
                ->type("vehicle", $vehicle->name)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);

            // 保有車両 説明
            $browser
                ->visitRoute("client-types.clients.index", ["client_type" => "taxibus", "initSearch" => 1])
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->waitUntilMissing('#nprogress')
                ->waitFor("input[name='vehicle']")
                ->type("vehicle", $vehicle->description)
                ->press('この条件で検索')
                ->waitUntilMissing('#nprogress')
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);
        });
    }

    /**
     * 新規作成
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTaxiBusCreate()
    {
        $this->browse(function (Browser $browser) {
            // 書き換える情報を生成
            /** @var \App\Models\Client $client */
            $client = Client::factory()->make(["client_type_id" => "taxibus"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("client-types.clients.create", ["client_type" => "taxibus"])
                ->waitForRoute("client-types.clients.create", ["client_type" => "taxibus"]);

            $browser
                ->waitFor("input[name='name']")
                ->type("name", $client->name)
                ->type("name_kana", $client->name_kana);

            // 画像アップロードのテストは省略

            // 郵便番号にfaker生成値ではなく実在する値を設定
            $browser
                ->type("postcode", "1530063")
                ->press('郵便番号から所在地を取得する')
                ->waitUsing(null, 100, function () use ($browser) {
                    return $browser->assertInputValueIsNot("prefecture", "");
                }, 'Waited for input value is not')
                ->waitUsing(null, 100, function () use ($browser) {
                    return $browser->assertInputValueIsNot("address", "");
                }, 'Waited for input value is not');

            // 位置情報のテストは省略

            $browser
                ->type("url", $client->url)
                ->type("email", $client->email)
                ->type("representative", $client->representative)
                ->type("tel", $client->tel)
                ->type("fax", $client->fax)
                ->type("business_hours", $client->business_hours)
                ->type("description", $client->description);

            // 固有情報を生成
            /** @var \App\Models\ClientTypeTaxibus $client_type_taxibus */
            $client_type_taxibus = ClientTypeTaxibus::factory()->make(["client_id" => $client->id, "category" => "taxibus"]);

            // 固有情報
            // v-radio, v-switch, v-selectのテストは省略
            $browser
                ->type("fee_taxi_cab", $client_type_taxibus->fee_taxi_cab)
                ->type("fee_taxi_tabinoashi", $client_type_taxibus->fee_taxi_tabinoashi)
                ->type("fee_bus_cab", $client_type_taxibus->fee_bus_cab)
                ->type("fee_bus_tabinoashi", $client_type_taxibus->fee_bus_tabinoashi);

            $browser
                ->press('この内容で作成する')
                // 確実に新しいIDを取得するため新規作成ページから移動するまで待機
                ->waitUsing(null, 100, function () use ($browser) {
                    return $browser->assertPathIsNot(route("client-types.clients.create", ["client_type" => "taxibus"], false));
                }, sprintf('Waited for path is not [%s]', route("client-types.clients.create", ["client_type" => "taxibus"], false)));

            // 生成されたIDを取得
            $client_new = Client::first();

            // 生成した情報の出現を確認
            $browser
                ->waitForRoute("clients.show", ["client" => $client_new->id])
                ->assertSee($client->name)
                ->assertSee($client->name_kana)
                ->assertSee("1530063")
                ->assertSee("東京都")
                ->assertSee("目黒区目黒")
                ->assertSee($client->url)
                ->assertSee($client->email)
                ->assertSee($client->representative)
                ->assertSee($client->tel)
                ->assertSee($client->fax)
                ->assertSee($client->business_hours)
                ->assertSee($client->description)
                ->assertSee(number_format($client_type_taxibus->fee_taxi_cab))
                ->assertSee(number_format($client_type_taxibus->fee_taxi_tabinoashi))
                ->assertSee(number_format($client_type_taxibus->fee_bus_cab))
                ->assertSee(number_format($client_type_taxibus->fee_bus_tabinoashi));
        });
    }

    /**
     * 詳細
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTaxiBusShow()
    {
        $this->browse(function (Browser $browser) {
            // 営業エリアを生成
            /** @var \App\Models\BusinessDistrict $business_district */
            $business_district = BusinessDistrict::factory()->create(['client_id' => $this->client->id]);

            // 営業所を生成
            /** @var \App\Models\Branch $branch */
            $branch = Branch::factory()->create(['client_id' => $this->client->id]);

            // 担当者を生成
            /** @var \App\Models\ContactPerson $contact_person */
            $contact_person = ContactPerson::factory()->create(['client_id' => $this->client->id]);

            // 保有車両を生成
            /** @var \App\Models\Vehicle $vehicle_taxi */
            $vehicle_taxi = Vehicle::factory()->create(['client_id' => $this->client->id, "type" => "taxi"]);

            /** @var \App\Models\Vehicle $vehicle_bus */
            $vehicle_bus = Vehicle::factory()->create(['client_id' => $this->client->id, "type" => "bus"]);

            // 営業ToDoを生成
            /** @var \App\Models\SalesTodo $sales_todo */
            $sales_todo = SalesTodo::factory()->create(['client_id' => $this->client->id, 'is_completed' => false]);

            // 営業日報を生成
            /** @var \App\Models\Report $report */
            $report = Report::factory()->create();

            /** @var \App\Models\ReportContent $report_content */
            $report_content = ReportContent::factory()->create(["type" => "sales", 'client_id' => $this->client->id]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("clients.show", ["client" => $this->client->id])
                ->waitForRoute("clients.show", ["client" => $this->client->id])
                ->waitUntilMissing('#nprogress');

            // 生成した情報の出現を確認
            $browser
                ->assertSee($this->client->name)
                ->assertSee($this->client->name_kana)
                ->assertSee($this->client->postcode)
                ->assertSee($this->client->prefecture)
                ->assertSee($this->client->address)
                ->assertSee($this->client->url)
                ->assertSee($this->client->email)
                ->assertSee($this->client->representative)
                ->assertSee($this->client->tel)
                ->assertSee($this->client->fax)
                ->assertSee($this->client->business_hours)
                ->assertSee($this->client->description)
                ->assertSee(number_format($this->client->client_type_taxibus->membership_fee))
                ->assertSee(number_format($this->client->client_type_taxibus->fee_taxi_cab))
                ->assertSee(number_format($this->client->client_type_taxibus->fee_taxi_tabinoashi))
                ->assertSee(number_format($this->client->client_type_taxibus->fee_bus_cab))
                ->assertSee(number_format($this->client->client_type_taxibus->fee_bus_tabinoashi))
                ->assertSee($this->client->client_type_taxibus->category_name);

            // 営業エリア
            $browser
                ->assertSee($business_district->prefecture . $business_district->address);

            $business_district_address_edit_and_delete = "営業エリア削除確認ダミー市区町村";

            // 営業エリア 編集
            $browser
                ->click("@businessDistrictEdit")
                // v-selectの都道府県選択は省略
                ->waitFor("input[name='address']")
                ->vuetifyClear("[name='address']")
                ->type("address", $business_district_address_edit_and_delete)
                ->press("この内容で更新する")
                ->waitForText($business_district_address_edit_and_delete)
                ->assertSee($business_district_address_edit_and_delete);

            // 営業エリア 削除
            $browser
                ->click("@businessDistrictEdit")
                // v-selectの都道府県選択は省略
                ->waitForTextIn('.v-dialog', 'この営業エリアを削除する')
                ->press("この営業エリアを削除する")
                ->press('OK')
                ->waitUsing(null, 100, function () use ($business_district_address_edit_and_delete, $browser) {
                    return $browser->assertDontSee($business_district_address_edit_and_delete);
                }, 'Waited for text dont see')
                ->assertDontSee($business_district_address_edit_and_delete);

            // 営業所エリア 作成
            $business_district_address_create = "営業所エリア作成確認ダミー市区町村";
            $browser
                ->press("営業エリアを追加する")
                ->waitFor("input[name='address']")
                // v-selectの会社リストのうち先頭の項目を選択
                ->clickAtXPath('//*[@name = "prefecture"]/../div[contains(@class, "v-select")]')
                ->waitFor('.v-menu__content')
                ->click('.v-menu__content')
                ->vuetifyClear("[name='address']")
                ->type("address", $business_district_address_create)
                ->press("この内容で作成する")
                ->waitForText($business_district_address_create)
                ->assertSee($business_district_address_create);

            // 営業所
            $browser
                ->assertSee($branch->name)
                ->assertSee($branch->prefecture . $branch->address)
                ->assertSee($branch->tel)
                ->assertSee($branch->fax);

            // 営業所 編集画面への遷移
            $browser
                ->click("@branchEdit")
                ->waitForRoute("branches.edit", ["branch" => $branch->id])
                ->assertRouteIs("branches.edit", ["branch" => $branch->id])
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業所 作成画面への遷移
            $browser
                ->press("営業所を追加する")
                ->waitForRoute("clients.branches.create", ["client" => $this->client->id])
                ->assertRouteIs("clients.branches.create", ["client" => $this->client->id])
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 担当者
            $browser
                ->assertSee($contact_person->name)
                ->assertSee($contact_person->email);

            $contact_person_name_edit_and_delete = "担当者削除確認ダミー担当者名";
            $contact_person_email = "dummy@example.com";

            // 担当者 編集
            $browser
                ->click("@contactPersonEdit")
                ->waitFor("input[name='name']")
                ->vuetifyClear("[name='name']")
                ->type("name", $contact_person_name_edit_and_delete)
                ->vuetifyClear("[name='email']")
                ->type("email", $contact_person_email)
                ->press("この内容で更新する")
                ->waitForText($contact_person_name_edit_and_delete)
                ->assertSee($contact_person_name_edit_and_delete)
                ->assertSee($contact_person_email);

            // 担当者 削除
            $browser
                ->click("@contactPersonEdit")
                // v-selectの都道府県選択は省略
                ->waitForTextIn('.v-dialog', 'この担当者を削除する')
                ->press("この担当者を削除する")
                ->press('OK')
                ->waitUsing(null, 100, function () use ($contact_person_name_edit_and_delete, $browser) {
                    return $browser->assertDontSee($contact_person_name_edit_and_delete);
                }, 'Waited for text dont see')
                ->assertDontSee($contact_person_name_edit_and_delete)
                ->assertDontSee($contact_person_email);

            // 担当者 作成
            $contact_person_name_create = "担当者作成確認ダミー担当者名";

            $browser
                ->press("担当者を追加する")
                ->whenAvailable('.v-dialog', function ($modal) use ($contact_person_email, $contact_person_name_create) {
                    $modal
                        ->waitFor("input[name='name']")
                        // inputが成功するまで待機
                        ->waitUsing(null, 100, function () use ($contact_person_name_create, $modal) {
                            $modal->type("name", $contact_person_name_create);
                            return $modal->assertInputValue("name", $contact_person_name_create);
                        }, 'Waited for input value name')
                        ->type("email", $contact_person_email)
                        ->press("この内容で作成する");
                })
                ->waitForText($contact_person_name_create)
                ->assertSee($contact_person_name_create)
                ->assertSee($contact_person_email);

            // v-chip要素で表示される情報は省略

            // 保有車両
            $browser
                ->assertSee($vehicle_taxi->name)
                ->assertSee($vehicle_bus->name);

            // 保有車両 タクシー詳細画面への遷移
            $browser
                ->click("@vehicleTaxiShow")
                ->waitForRoute("vehicles.show", ["vehicle" => $vehicle_taxi->id])
                ->assertRouteIs("vehicles.show", ["vehicle" => $vehicle_taxi->id])
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 保有車両 タクシー作成画面への遷移
            $browser
                ->press("タクシーを追加する")
                ->waitForRoute("clients.vehicles.create", ["client" => $this->client->id])
                ->assertRouteIs("clients.vehicles.create", ["client" => $this->client->id])
                ->assertQueryStringHas("type", "taxi")
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 保有車両 バス詳細画面への遷移
            $browser
                ->click("@vehicleBusShow")
                ->waitForRoute("vehicles.show", ["vehicle" => $vehicle_bus->id])
                ->assertRouteIs("vehicles.show", ["vehicle" => $vehicle_bus->id])
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 保有車両 バス作成画面への遷移
            $browser
                ->press("バスを追加する")
                ->waitForRoute("clients.vehicles.create", ["client" => $this->client->id])
                ->assertRouteIs("clients.vehicles.create", ["client" => $this->client->id])
                ->assertQueryStringHas("type", "bus")
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業ToDo
            $browser
                ->waitForText($sales_todo->contact_person)
                ->assertSee($sales_todo->contact_person)
                ->assertSee($sales_todo->toArray()["scheduled_at"])
                ->assertSee($sales_todo->description);

            // 営業ToDo 一覧への遷移
            $browser
                ->press("この会社のすべての営業ToDoを見る")
                ->waitForRoute("sales-todos.index")
                ->assertRouteIs("sales-todos.index")
                ->assertQueryStringHas("client_id", $this->client->id)
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業ToDo 作成画面への遷移
            $browser
                ->press("この会社のToDoを追加する")
                ->waitForRoute("sales-todos.create")
                ->assertRouteIs("sales-todos.create")
                ->assertQueryStringHas("client_id", $this->client->id)
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業日報
            $browser
                ->assertSee($report->user->name)
                ->assertSee($report->toArray()["date"])
                ->assertSee($report_content->description);

            // 営業日報 タクシー詳細画面への遷移
            $browser
                ->click("@reportShow")
                ->waitForRoute("reports.show", ["report" => $report->id])
                ->assertRouteIs("reports.show", ["report" => $report->id])
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業日報 一覧への遷移
            $browser
                ->press("この会社のすべての営業日報を見る")
                ->waitForRoute("reports.index")
                ->assertRouteIs("reports.index")
                ->assertQueryStringHas("client_id", $this->client->id)
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 営業日報 作成画面への遷移
            $browser
                ->press("この会社の営業日報を作成する")
                ->waitForRoute("reports.create")
                ->assertRouteIs("reports.create")
                ->assertQueryStringHas("client_id", $this->client->id)
                ->back()
                ->waitForRoute("clients.show", ["client" => $this->client->id]);

            // 商材の評価は省略
        });
    }

    /**
     * 編集
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTaxiBusEdit()
    {
        $this->browse(function (Browser $browser) {
            // 書き換える情報を生成
            /** @var \App\Models\Client $client */
            $client = Client::factory()->make(["client_type_id" => "taxibus"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("clients.edit", ["client" => $this->client->id])
                ->waitForRoute("clients.edit", ["client" => $this->client->id])
                ->waitUntilMissing('#nprogress');

            $browser
                ->waitFor("input[name='name']")
                ->vuetifyClear("[name='name']")
                ->type("name", $client->name)
                ->vuetifyClear("[name='name_kana']")
                ->type("name_kana", $client->name_kana);

            // 画像アップロードのテストは省略

            // 郵便番号にfaker生成値ではなく実在する値を設定
            $browser
                ->vuetifyClear("[name='postcode']")
                ->type("postcode", "1530063")
                ->press('郵便番号から所在地を取得する')
                ->waitUsing(null, 100, function () use ($browser) {
                    return $browser->assertInputValueIsNot("prefecture", "");
                }, 'Waited for input value is not')
                ->waitUsing(null, 100, function () use ($browser) {
                    return $browser->assertInputValueIsNot("address", "");
                }, 'Waited for input value is not');

            // 位置情報のテストは省略

            $browser
                ->vuetifyClear("[name='url']")
                ->type("url", $client->url)
                ->vuetifyClear("[name='email']")
                ->type("email", $client->email)
                ->vuetifyClear("[name='representative']")
                ->type("representative", $client->representative)
                ->vuetifyClear("[name='tel']")
                ->type("tel", $client->tel)
                ->vuetifyClear("[name='fax']")
                ->type("fax", $client->fax)
                ->vuetifyClear("[name='business_hours']")
                ->type("business_hours", $client->business_hours)
                ->vuetifyClear("[name='description']")
                ->type("description", $client->description);

            // 固有情報を生成
            /** @var \App\Models\ClientTypeTaxibus $client_type_taxibus */
            $client_type_taxibus = ClientTypeTaxibus::factory()->make(["client_id" => $this->client->id, "category" => "taxibus"]);

            // 固有情報
            // v-radio, v-switch, v-selectのテストは省略
            $browser
                ->vuetifyClear("[name='fee_taxi_cab']")
                ->type("fee_taxi_cab", $client_type_taxibus->fee_taxi_cab)
                ->vuetifyClear("[name='fee_taxi_tabinoashi']")
                ->type("fee_taxi_tabinoashi", $client_type_taxibus->fee_taxi_tabinoashi)
                ->vuetifyClear("[name='fee_bus_cab']")
                ->type("fee_bus_cab", $client_type_taxibus->fee_bus_cab)
                ->vuetifyClear("[name='fee_bus_tabinoashi']")
                ->type("fee_bus_tabinoashi", $client_type_taxibus->fee_bus_tabinoashi);

            $browser
                ->press('この内容に変更する')
                ->waitUntilMissing('#nprogress')
                ->waitForText('会社情報の変更を完了しました');
                // Github Actionsでの実行で何故か時間がかかるためコメントアウト
                //->waitForRoute("clients.show", ["client" => $this->client->id])
                //->assertRouteIs("clients.show", ["client" => $this->client->id]);

            // 生成した情報の出現を確認
            $browser
                ->waitForText($client->name)
                ->assertSee($client->name)
                ->assertSee($client->name_kana)
                ->assertSee("1530063")
                ->assertSee("東京都")
                ->assertSee("目黒区目黒")
                ->assertSee($client->url)
                ->assertSee($client->email)
                ->assertSee($client->representative)
                ->assertSee($client->tel)
                ->assertSee($client->fax)
                ->assertSee($client->business_hours)
                ->assertSee($client->description)
                ->assertSee(number_format($client_type_taxibus->fee_taxi_cab))
                ->assertSee(number_format($client_type_taxibus->fee_taxi_tabinoashi))
                ->assertSee(number_format($client_type_taxibus->fee_bus_cab))
                ->assertSee(number_format($client_type_taxibus->fee_bus_tabinoashi));
        });
    }
}
