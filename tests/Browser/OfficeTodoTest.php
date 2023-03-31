<?php

namespace Tests\Browser;

use App\Models\OfficeTodo;
use App\Models\OfficeTodoParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OfficeTodoTest extends DuskTestCase
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
    }

    /**
     * 社内ToDoリスト画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testOfficeTodo()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\OfficeTodo $office_todo */
            $office_todo = OfficeTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
            $office_todo->office_todo_participants()->save(new OfficeTodoParticipant(["user_id" => $this->user2->getKey()]));

            $browser
                ->loginAs($this->user)
                ->visitRoute("office-todos.index")
                ->waitForRoute("office-todos.index");

            // 直近の社内ToDoリスト
            $browser
                // 新規作成ボタン
                ->click("@officeTodoCreate")
                ->waitForRoute("office-todos.create")
                ->assertRouteIs("office-todos.create")
                ->back()
                ->waitForRoute("office-todos.index")
                // 作成した情報の出現を確認
                ->assertSee($office_todo->toArray()["scheduled_at"])
                ->assertSee($office_todo->title)
                ->assertSee($office_todo->description)
                ->assertSee($office_todo->office_todo_participants()->first()->user->name)
                // 社内担当者のメンバーページへの遷移
                ->click("@userShowParticipant")
                ->waitForRoute("users.show", ["user" => $this->user2->getKey()])
                ->assertRouteIs("users.show", ["user" => $this->user2->getKey()])
                ->back()
                ->waitForRoute("office-todos.index")
                ->waitUntilMissing('#nprogress')
                // 編集ボタン
                ->click("@officeTodoEdit")
                ->waitForRoute("office-todos.edit", ["office_todo" => $office_todo->getKey()])
                ->assertRouteIs("office-todos.edit", ["office_todo" => $office_todo->getKey()])
                ->back()
                ->waitForRoute("office-todos.index")
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
                ->waitUntilMissingText($office_todo->description)
                ->assertDontSee($office_todo->description);
        });
    }

    /**
     * 社内ToDo作成画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testOfficeTodoCreate()
    {
        $this->browse(function (Browser $browser) {
            // 書き換える情報を生成
            /** @var \App\Models\OfficeTodo $office_todo */
            $office_todo = OfficeTodo::factory()->make();

            $browser
                ->loginAs($this->user)
                ->visitRoute("office-todos.create")
                ->waitForRoute("office-todos.create");

            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            // v-select multiの社内担当者は煩雑となるため省略
            $browser
                ->type("title", $office_todo->title)
                ->type("description", $office_todo->description)
                ->press('この内容で作成する')
                ->waitForRoute("office-todos.index")
                ->assertSee($office_todo->description);
        });
    }

    /**
     * 社内ToDo編集画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testOfficeTodoEdit()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\OfficeTodo $office_todo */
            $office_todo = OfficeTodo::factory()->create(["user_id" => $this->user->getKey(), "is_completed" => false]);
            $office_todo->office_todo_participants()->save(new OfficeTodoParticipant(["user_id" => $this->user2->getKey()]));

            $browser
                ->loginAs($this->user)
                ->visitRoute("office-todos.edit", ["office_todo" => $office_todo->getKey()])
                ->waitForRoute("office-todos.edit", ["office_todo" => $office_todo->getKey()]);

            // 設定した情報の出現を確認
            $browser
                ->assertInputValue("scheduled_at", $office_todo->scheduled_at->format('Y-m-d\TH:i'))
                ->assertInputValue("title", $office_todo->title)
                ->assertInputValue("description", $office_todo->description)
                ->assertSeeIn(".v-select__selection", $office_todo->office_todo_participants()->first()->user->name);

            // 書き換える情報を生成
            /** @var \App\Models\OfficeTodo $office_todo_new */
            $office_todo_new = OfficeTodo::factory()->make();

            // v-select等のvuetify固有要素は煩雑となるため省略
            // datetime-localのscheduled_atはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略
            $browser
                ->vuetifyClear("[name='title']")
                ->type("title", $office_todo_new->title)
                ->vuetifyClear("[name='description']")
                ->type("description", $office_todo_new->description)
                ->press('この内容で更新する')
                ->waitForRoute("office-todos.index")
                ->assertSee($office_todo_new->title)
                ->assertSee($office_todo_new->description);
        });
    }
}