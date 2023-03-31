<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UsersTest extends DuskTestCase
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
     * メンバー一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testUsersIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("users.index")
                ->waitForRoute("users.index")
                ->assertRouteIs("users.index")
                ->waitUntilMissing('#nprogress');

            // 作成した情報の出現を確認
            $browser
                ->assertSee($this->user->name)
                ->assertSee($this->user->username);

            // 詳細画面への遷移
            $browser
                ->click("@userShow")
                ->waitForRoute("users.show", ["user" => $this->user->getKey()])
                ->assertRouteIs("users.show", ["user" => $this->user->getKey()])
                ->back()
                ->waitForRoute("users.index");

            // 編集画面への遷移
            $browser
                ->press("自分のメンバー情報を編集する")
                ->waitForRoute("user-profile-information.edit")
                ->assertRouteIs("user-profile-information.edit")
                ->back()
                ->waitForRoute("users.index");
        });
    }

    /**
     * メンバー詳細画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testUsersShow()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = User::factory()->create();

            $browser
                ->loginAs($this->user)
                ->visitRoute("users.show", ["user" => $user->getKey()])
                ->waitForRoute("users.show", ["user" => $user->getKey()])
                ->assertRouteIs("users.show", ["user" => $user->getKey()])
                ->waitUntilMissing('#nprogress');

            // 作成した情報の出現を確認
            $browser
                ->assertSee($user->name)
                ->assertSee($user->username)
                ->assertSee($user->name)
                ->assertSee($user->email)
                ->assertSee($user->tel)
                ->assertSee($user->department);
        });
    }

    /**
     * メンバー編集画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testUsersEdit()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("user-profile-information.edit")
                ->waitForRoute("user-profile-information.edit");

            // 設定した情報の出現を確認
            $browser
                ->assertInputValue("email", $this->user->email)
                ->assertInputValue("tel", $this->user->tel)
                ->assertInputValue("department", $this->user->department);

            // パスワード変更のテストは省略

            // メンバーの情報を生成
            /** @var \App\Models\User $user_new */
            $user_new = User::factory()->make();

            // 情報を入力
            $browser
                ->waitUsing(null, 100, function () use ($user_new, $browser) {
                    $browser
                        ->vuetifyClear("[name='email']")
                        ->type("email", $user_new->email);
                    return $browser->assertInputValue("email", $user_new->email);
                }, 'Waited for input value email')
                ->vuetifyClear("[name='tel']")
                ->type("tel", $user_new->tel)
                ->vuetifyClear("[name='department']")
                ->type("department", $user_new->department);

            // メンバー情報を更新
            $browser
                ->press("この内容でメンバー情報を更新する")
                ->waitForText('メンバー情報の変更を保存しました');

            $browser
                ->waitForRoute("user-profile-information.edit")
                ->assertRouteIs("user-profile-information.edit")
                ->waitUntilMissing('#nprogress')
                ->assertInputValue("email", $user_new->email)
                ->assertInputValue("tel", $user_new->tel)
                ->assertInputValue("department", $user_new->department);
        });
    }
}
