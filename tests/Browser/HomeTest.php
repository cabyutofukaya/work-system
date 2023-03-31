<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Notice;
use App\Models\OfficeTodo;
use App\Models\OfficeTodoParticipant;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportContent;
use App\Models\SalesTodo;
use App\Models\SalesTodoParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\User */
    private Collection|Model|User $user2;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\Notice */
    private Collection|Model|Notice $notice;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\SalesTodo */
    private Collection|Model|SalesTodo $sales_todo;

    /**  @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\OfficeTodo */
    private Collection|Model|OfficeTodo $office_todo;

    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\App\Models\Report */
    private Collection|Model|Report $report;

    public function setUp(): void
    {
        parent::setUp();

        // メンバー
        $this->user = User::factory()->create();

        // 社内担当者
        $this->user2 = User::factory()->create();

        // 会社
        Client::factory()->create();

        // お知らせ
        $this->notice = Notice::factory()->create(["user_id" => $this->user->getKey()]);

        // 営業ToDo
        $this->sales_todo = SalesTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
        $this->sales_todo->sales_todo_participants()->save(new SalesTodoParticipant(["user_id" => $this->user2->getKey()]));

        // 社内ToDo
        $this->office_todo = OfficeTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
        $this->office_todo->office_todo_participants()->save(new OfficeTodoParticipant(["user_id" => $this->user2->getKey()]));

        // 日報
        $this->report = Report::factory()->create(["user_id" => $this->user->getKey()]);
        $this->report->report_contents()->save(ReportContent::factory()->make());
        $this->report->report_comments()->save(ReportComment::factory()->make(["user_id" => $this->user2->getKey()]));
    }

    /**
     * ホーム画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testHome()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home")
                ->assertRouteIs("home");
        });
    }

    /**
     * ホーム画面 お知らせ
     *
     * @return void
     * @throws \Throwable
     */
    public function testHomeNotice()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home");

            // お知らせ
            $browser
                ->scrollIntoView('@notices')
                ->assertSeeIn("@notices", "新着のお知らせ")
                ->assertSeeIn("@notices", $this->notice->toArray()["created_at"])
                ->assertSeeIn("@notices", $this->notice->title)
                ->assertSeeIn("@notices", $this->notice->user->name)
                // リストページへの遷移
                ->click("@noticeIndex")
                ->waitForRoute("notices.index")
                ->assertRouteIs("notices.index")
                ->back()
                ->waitForRoute("home")
                // 個別ページへの遷移
                ->click("@noticeShow")
                ->waitForRoute("notices.show", ["notice" => $this->notice->getKey()])
                ->assertRouteIs("notices.show", ["notice" => $this->notice->getKey()])
                ->back()
                ->waitForRoute("home");
        });
    }

    /**
     * ホーム画面 直近の営業ToDoリスト
     *
     * @return void
     * @throws \Throwable
     */
    public function testHomeSalesTodo()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home");

            // 直近の営業ToDoリスト
            $browser
                ->scrollIntoView('@sales_todos')
                ->assertSeeIn("@sales_todos", "直近の営業ToDoリスト")
                ->assertSeeIn("@sales_todos", $this->sales_todo->toArray()["scheduled_at"])
                ->assertSeeIn("@sales_todos", $this->sales_todo->client->name)
                ->assertSeeIn("@sales_todos", $this->sales_todo->contact_person)
                ->assertSeeIn("@sales_todos", $this->sales_todo->description)
                ->assertSeeIn("@sales_todos", $this->sales_todo->sales_todo_participants()->first()->user->name)
                // リストページへの遷移
                ->click("@salesTodoIndex")
                ->waitForRoute("sales-todos.index")
                ->assertRouteIs("sales-todos.index")
                ->back()
                ->waitForRoute("home")
                // 会社ページへの遷移
                ->click("@clientShowSalesTodo")
                ->waitForRoute("clients.show", ["client" => $this->sales_todo->client->getKey()])
                ->assertRouteIs("clients.show", ["client" => $this->sales_todo->client->getKey()])
                ->back()
                ->waitForRoute("home")
                // 社内担当者のメンバーページへの遷移
                ->click("@userShowSalesTodoParticipant")
                ->waitForRoute("users.show", ["user" => $this->user2->getKey()])
                ->assertRouteIs("users.show", ["user" => $this->user2->getKey()])
                ->back()
                ->waitForRoute("home")
                // 編集ボタン
                ->click("@salesTodoEdit")
                ->waitForRoute("sales-todos.edit", ["sales_todo" => $this->sales_todo->getKey()])
                ->assertRouteIs("sales-todos.edit", ["sales_todo" => $this->sales_todo->getKey()])
                ->back()
                ->waitForRoute("home")
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
                ->waitUntilMissingText($this->sales_todo->description)
                ->assertDontSeeIn("@sales_todos", $this->sales_todo->description);
        });
    }

    /**
     * ホーム画面 直近の社内ToDoリスト
     *
     * @return void
     * @throws \Throwable
     */
    public function testHomeOfficeTodo()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home");

            // 直近の社内ToDoリスト
            $browser
                ->scrollIntoView('@office_todos')
                ->assertSeeIn("@office_todos", "直近の社内ToDoリスト")
                ->assertSeeIn("@office_todos", $this->office_todo->toArray()["scheduled_at"])
                ->assertSeeIn("@office_todos", $this->office_todo->title)
                ->assertSeeIn("@office_todos", $this->office_todo->description)
                ->assertSeeIn("@office_todos", $this->office_todo->office_todo_participants()->first()->user->name)
                // リストページへの遷移
                ->click("@officeTodoIndex")
                ->waitForRoute("office-todos.index")
                ->assertRouteIs("office-todos.index")
                ->back()
                ->waitForRoute("home")
                // 社内担当者のメンバーページへの遷移
                ->click("@userShowOfficeTodoParticipant")
                ->waitForRoute("users.show", ["user" => $this->user2->getKey()])
                ->assertRouteIs("users.show", ["user" => $this->user2->getKey()])
                ->back()
                ->waitForRoute("home")
                // 編集ボタン
                ->click("@officeTodoEdit")
                ->waitForRoute("office-todos.edit", ["office_todo" => $this->office_todo->getKey()])
                ->assertRouteIs("office-todos.edit", ["office_todo" => $this->office_todo->getKey()])
                ->back()
                ->waitForRoute("home")
                // 完了ボタン
                ->click("@officeTodoComplete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->assertVisible(".grey")
                // 削除ボタン
                ->click("@officeTodoDelete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->waitUntilMissingText($this->office_todo->description)
                ->assertDontSeeIn("@office_todos", $this->office_todo->description);
        });
    }

    /**
     * ホーム画面 最新の日報
     *
     * @return void
     * @throws \Throwable
     */
    public function testHomeReport()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home");

            // 最新の日報
            $browser
                ->scrollIntoView('@reports')
                ->assertSeeIn("@reports", "最新の日報")
                ->assertSeeIn("@reports", $this->report->toArray()["date"])
                ->assertSeeIn("@reports", $this->report->user->name);
        });
    }

    /**
     * ホーム画面 コメントがついた日報
     *
     * @return void
     * @throws \Throwable
     */
    public function testHomeReportsLatestComment()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit("/")
                ->visitRoute("home")
                ->waitForRoute("home");

            // コメントがついた日報
            $browser
                ->scrollIntoView('@reports_latest_comment')
                ->assertSeeIn("@reports_latest_comment", "コメントがついた日報")
                ->assertSeeIn("@reports_latest_comment", $this->report->toArray()["date"])
                ->assertSeeIn("@reports_latest_comment", $this->report->user->name);
        });
    }
}

