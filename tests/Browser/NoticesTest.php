<?php

namespace Tests\Browser;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NoticesTest extends DuskTestCase
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
     * お知らせ一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testNoticesIndex()
    {
        $this->browse(function (Browser $browser) {
            // お知らせを作成
            /** @var \App\Models\Notice $notice */
            $notice = Notice::factory()->create();

            $browser
                ->loginAs($this->user)
                ->visitRoute("notices.index")
                ->waitForRoute("notices.index")
                ->assertRouteIs("notices.index")
                ->waitUntilMissing('#nprogress');

            $browser
                // 作成した情報の出現を確認
                ->assertSee($notice->toArray()["created_at"])
                ->assertSee($notice->title)
                ->assertSee($notice->user->name)
                // お知らせ個別ページへの遷移
                ->click("@noticeShow")
                ->waitForRoute("notices.show", ["notice" => $notice->getKey()])
                ->assertRouteIs("notices.show", ["notice" => $notice->getKey()])
                ->back()
                ->waitForRoute("notices.index");

            // お知らせの情報を生成
            /** @var \App\Models\Notice $notice_new */
            $notice_new = Notice::factory()->make();

            // お知らせの作成
            $browser
                ->press("お知らせを作成する")
                ->waitFor("input[name='title']")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($notice_new, $browser) {
                    $browser->type("title", $notice_new->title);
                    return $browser->assertInputValue("title", $notice_new->title);
                }, 'Waited for input value title')
                ->type("description", $notice_new->description)
                ->press("この内容で追加する")
                ->waitForText($notice_new->title)
                ->assertSee($notice_new->title);
        });
    }

    /**
     * お知らせ詳細画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testNoticesShow()
    {
        $this->browse(function (Browser $browser) {
            // すべてのお知らせを削除
            Notice::all()->each(function ($notice) {
                $notice->delete();
            });

            // お知らせ情報を生成
            /** @var \App\Models\Notice $notice */
            $notice = Notice::factory()->create(["user_id" => $this->user->getKey()]);

            $browser
                ->loginAs($this->user)
                ->visitRoute("notices.show", ["notice" => $notice->id])
                ->waitForRoute("notices.show", ["notice" => $notice->id])
                ->assertRouteIs("notices.show", ["notice" => $notice->id])
                ->waitUntilMissing('#nprogress');

            // 情報の出現を確認
            $browser
                ->assertSee($notice->toArray()["created_at"])
                ->assertSee($notice->title)
                ->assertSee($notice->user->name)
                ->assertSee($notice->description);

            // お知らせの情報を生成
            /** @var \App\Models\Notice $notice_new */
            $notice_new = Notice::factory()->make();

            // お知らせの編集
            $browser
                ->press("編集")
                ->waitFor("input[name='title']")
                // inputが成功するまで待機
                ->waitUsing(null, 100, function () use ($notice_new, $browser) {
                    $browser
                        ->vuetifyClear("[name='title']")
                        ->type("title", $notice_new->title);
                    return $browser->assertInputValue("title", $notice_new->title);
                }, 'Waited for input value title')
                ->vuetifyClear("[name='description']")
                ->type("description", $notice_new->description)
                ->press("この内容で保存する")
                ->waitForText($notice_new->title)
                ->assertSee($notice_new->title)
                ->assertSee($notice_new->description);

            // 削除ボタン
            $browser
                ->press("削除")
                ->waitForTextIn('.v-dialog', 'OK')
                ->press('OK')
                ->waitUntilMissing('#nprogress')
                ->waitUntilMissingText($notice_new->title)
                ->assertDontSee($notice_new->title)
                ->assertDontSee($notice->toArray()["created_at"])
                ->assertDontSee($notice->user->name);
        });
    }
}
