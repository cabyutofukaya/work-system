<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\SalesTodo;
use App\Models\SalesTodoParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SalesTodoTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user2;

    public function setUp(): void
    {
        parent::setUp();

        // メンバー
        $this->user = User::factory()->create();

        // 社内担当者
        $this->user2 = User::factory()->create();

        // 会社
        Client::factory()->create();
    }

    /**
     * 営業ToDoリスト画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testSalesTodo()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\SalesTodo $sales_todo */
            $sales_todo = SalesTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
            $sales_todo->sales_todo_participants()->save(new SalesTodoParticipant(["user_id" => $this->user2->getKey()]));

            $browser
                ->loginAs($this->user)
                ->visitRoute("sales-todos.index")
                ->waitForRoute("sales-todos.index");

            // 直近の営業ToDoリスト
            $browser
                // 新規作成ボタン
                ->click("@salesTodoCreate")
                ->waitForRoute("sales-todos.create")
                ->assertRouteIs("sales-todos.create")
                ->back()
                ->waitForRoute("sales-todos.index")
                // 作成した情報の出現を確認
                ->assertSee($sales_todo->toArray()["scheduled_at"])
                ->assertSee($sales_todo->client->name)
                ->assertSee($sales_todo->contact_person)
                ->assertSee($sales_todo->description)
                ->assertSee($sales_todo->sales_todo_participants()->first()->user->name)
                // 会社ページへの遷移
                ->click("@clientShow")
                ->waitForRoute("clients.show", ["client" => $sales_todo->client->getKey()])
                ->assertRouteIs("clients.show", ["client" => $sales_todo->client->getKey()])
                ->back()
                ->waitForRoute("sales-todos.index")
                // 社内担当者のメンバーページへの遷移
                ->click("@userShowParticipant")
                ->waitForRoute("users.show", ["user" => $this->user2->getKey()])
                ->assertRouteIs("users.show", ["user" => $this->user2->getKey()])
                ->back()
                ->waitForRoute("sales-todos.index")
                ->waitUntilMissing('#nprogress')
                // 編集ボタン
                ->click("@salesTodoEdit")
                ->waitForRoute("sales-todos.edit", ["sales_todo" => $sales_todo->getKey()])
                ->assertRouteIs("sales-todos.edit", ["sales_todo" => $sales_todo->getKey()])
                ->back()
                ->waitForRoute("sales-todos.index")
                // 完了ボタン
                ->click("@salesTodoComplete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->assertVisible(".grey")
                // 削除ボタン
                ->click("@salesTodoDelete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->waitUntilMissingText($sales_todo->description)
                ->assertDontSee($sales_todo->description);
        });
    }

    /**
     * 営業ToDo作成画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testSalesTodoCreate()
    {
        $this->browse(function (Browser $browser) {
            // 書き換える情報を生成
            /** @var \App\Models\SalesTodo $sales_todo */
            $sales_todo = SalesTodo::factory()->make();

            $browser
                ->loginAs($this->user)
                ->visitRoute("sales-todos.create")
                ->waitForRoute("sales-todos.create");

            // v-selectの会社リストのうち先頭の項目を選択
            $browser
                ->clickAtXPath('//*[@name = "client_id"]/../div[contains(@class, "v-select")]')
                ->waitFor('.v-menu__content')
                ->click('.v-menu__content');

            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            // v-select multiの社内担当者は煩雑となるため省略
            $browser
                ->type("contact_person", $sales_todo->contact_person)
                ->type("description", $sales_todo->description)
                ->press('この内容で作成する')
                ->waitForRoute("sales-todos.index")
                ->assertSee($sales_todo->contact_person)
                ->assertSee($sales_todo->description);
        });
    }

    /**
     * 営業ToDo編集画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testSalesTodoEdit()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\SalesTodo $sales_todo */
            $sales_todo = SalesTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
            $sales_todo->sales_todo_participants()->save(new SalesTodoParticipant(["user_id" => $this->user2->getKey()]));

            $browser
                ->loginAs($this->user)
                ->visitRoute("sales-todos.edit", ["sales_todo" => $sales_todo->getKey()])
                ->waitForRoute("sales-todos.edit", ["sales_todo" => $sales_todo->getKey()]);

            // 設定した情報の出現を確認
            $browser
                ->assertInputValue("scheduled_at", $sales_todo->scheduled_at->format('Y-m-d\TH:i'))
                ->assertSeeIn(".v-select__selections", $sales_todo->client->name)
                ->assertInputValue("contact_person", $sales_todo->contact_person)
                ->assertInputValue("description", $sales_todo->description)
                ->assertSeeIn(".v-select__selection", $sales_todo->sales_todo_participants()->first()->user->name);

            // 書き換える情報を生成
            /** @var \App\Models\SalesTodo $sales_todo_new */
            $sales_todo_new = SalesTodo::factory()->make();

            // v-select等のvuetify固有要素は煩雑となるため省略
            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            $browser
                ->vuetifyClear("[name='contact_person']")
                ->type("contact_person", $sales_todo_new->contact_person)
                ->vuetifyClear("[name='description']")
                ->type("description", $sales_todo_new->description)
                ->press('この内容で更新する')
                ->waitForRoute("sales-todos.index")
                ->assertSee($sales_todo_new->contact_person)
                ->assertSee($sales_todo_new->description);
        });
    }
}
