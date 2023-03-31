<?php

namespace Tests\Browser;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MeetingsTest extends DuskTestCase
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
     * 議事録一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testMeetingsIndex()
    {
        $this->browse(function (Browser $browser) {
            // 議事録を作成
            /** @var \App\Models\Meeting $meeting */
            $meeting = Meeting::factory()->create();

            $browser
                ->loginAs($this->user)
                ->visitRoute("meetings.index")
                ->waitForRoute("meetings.index")
                ->assertRouteIs("meetings.index")
                ->waitUntilMissing('#nprogress');

            $browser
                // 作成した情報の出現を確認
                ->assertSee($meeting->toArray()["started_at"])
                ->assertSee($meeting->title)
                ->assertSee($meeting->user->name)
                // 議事録個別ページへの遷移
                ->click("@meetingShow")
                ->waitForRoute("meetings.show", ["meeting" => $meeting->getKey()])
                ->assertRouteIs("meetings.show", ["meeting" => $meeting->getKey()])
                ->back()
                ->waitForRoute("meetings.index")
                // 作成ページへの遷移
                ->press("議事録を作成する")
                ->waitForRoute("meetings.create")
                ->assertRouteIs("meetings.create")
                ->back()
                ->waitForRoute("meetings.index")
                // 自分の議事録ページへの遷移
                ->press("自分の議事録を管理")
                ->waitForRoute("meetings.mine")
                ->assertRouteIs("meetings.mine")
                ->back()
                ->waitForRoute("meetings.index");

            // 絞り込み検索ダイアログのテストを省略
        });
    }

    /**
     * 自分の議事録一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testMeetingsMine()
    {
        $this->browse(function (Browser $browser) {
            // すべての議事録を削除
            Meeting::all()->each(function ($meeting) {
                $meeting->delete();
            });

            // 議事録を作成
            /** @var \App\Models\Meeting $meeting */
            $meeting = Meeting::factory()->create(["user_id" => $this->user->getKey()]);

            // 他ユーザの議事録を作成 遠い未来の日付で生成
            /** @var \App\Models\Meeting $user_other */
            $user_other = User::factory()->create();

            /** @var \App\Models\Meeting $meeting_other */
            $meeting_other = Meeting::factory()->create(["user_id" => $user_other->getKey(), "started_at" => "2999-12-31 00:00:00"]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("meetings.mine")
                ->waitForRoute("meetings.mine")
                ->assertRouteIs("meetings.mine")
                ->waitUntilMissing('#nprogress');

            $browser
                // 作成した情報の出現を確認
                ->assertSee($meeting->toArray()["started_at"])
                ->assertSee($meeting->title)
                // 他ユーザの議事録が出現しないことを確認
                ->assertDontSee($meeting_other->toArray()["started_at"])
                ->assertDontSee($meeting_other->title)
                // 作成ページへの遷移
                ->press("議事録を作成する")
                ->waitForRoute("meetings.create")
                ->assertRouteIs("meetings.create")
                ->back()
                ->waitForRoute("meetings.mine")
                // 編集ページへの遷移
                ->click("@meetingEdit")
                ->waitForRoute("meetings.edit", ["meeting" => $meeting->getKey()])
                ->assertRouteIs("meetings.edit", ["meeting" => $meeting->getKey()])
                ->back()
                ->waitForRoute("meetings.mine")
                // 削除ボタン
                ->click("@meetingDelete")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->waitUntilMissingText($meeting_other->toArray()["started_at"])
                ->assertDontSee($meeting_other->toArray()["started_at"])
                ->assertDontSee($meeting_other->title);

            // 絞り込み検索ダイアログのテストを省略
        });
    }

    /**
     * 議事録作成画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testMeetingsCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("meetings.create")
                ->waitForRoute("meetings.create")
                ->assertRouteIs("meetings.create")
                ->waitUntilMissing('#nprogress');

            // datetime-localはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略

            // 議事録の情報を生成
            /** @var \App\Models\Meeting $meeting */
            $meeting = Meeting::factory()->make();

            // 情報を入力
            $browser
                ->type("title", $meeting->title)
                ->type("participants", $meeting->participants)
                ->type("content", $meeting->content);

            // 議事録を作成
            $browser
                ->press("この内容で議事録を作成する")
                ->waitForText('議事録を作成しました');

            // 作成された議事録の情報を取得
            $meeting_new = Meeting::first();

            $browser
                ->waitForRoute("meetings.show", ["meeting" => $meeting_new->id])
                ->waitForRoute("meetings.show", ["meeting" => $meeting_new->id])
                ->assertRouteIs("meetings.show", ["meeting" => $meeting_new->id])
                ->waitUntilMissing('#nprogress')
                ->assertSee($meeting->title)
                ->assertSee($meeting->participants)
                ->assertSee($meeting->content);
        });
    }

    /**
     * 議事録編集画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testMeetingsEdit()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Meeting $meeting */
            $meeting = Meeting::factory()->create(["user_id" => $this->user->getKey()]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("meetings.edit", ["meeting" => $meeting->getKey()])
                ->waitForRoute("meetings.edit", ["meeting" => $meeting->getKey()]);

            // 設定した情報の出現を確認
            $browser
                ->assertInputValue("title", $meeting->title)
                ->assertInputValue("participants", $meeting->participants)
                ->assertInputValue("content", $meeting->content);

            // datetime-localはchromedriverの言語設定が有効とならずen-usフォーマットとなり正しく入力できないため省略

            /** 営業議事録の編集ダイアログ */

            // 議事録の情報を生成
            /** @var \App\Models\Meeting $meeting */
            $meeting_new = Meeting::factory()->make();

            // 情報を入力
            $browser
                ->waitUsing(null, 100, function () use ($meeting_new, $browser) {
                    $browser
                        ->vuetifyClear("[name='title']")
                        ->type("title", $meeting_new->title);
                    return $browser->assertInputValue("title", $meeting_new->title);
                }, 'Waited for input value title')
                ->type("participants", $meeting_new->participants)
                ->type("content", $meeting_new->content);

            // 議事録を作成
            $browser
                ->press("この内容で議事録を更新する")
                ->waitForText('議事録の変更を保存しました');

            $browser
                ->waitForRoute("meetings.show", ["meeting" => $meeting->id])
                ->waitForRoute("meetings.show", ["meeting" => $meeting->id])
                ->assertRouteIs("meetings.show", ["meeting" => $meeting->id])
                ->waitUntilMissing('#nprogress')
                ->assertSee($meeting_new->title)
                ->assertSee($meeting_new->participants)
                ->assertSee($meeting_new->content);
        });
    }
}
