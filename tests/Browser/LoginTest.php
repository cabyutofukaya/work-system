<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testLogin()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            // ログイン
            $browser->visit('/')
                ->waitForLocation("/login")
                ->waitFor("input[name='username']")
                ->type('username', $user->username)
                ->waitFor("input[name='password']")
                ->type('password', 'password')
                ->press('ログイン')
                ->waitUntilMissing('#nprogress')
                ->waitForRoute('home')
                ->assertRouteIs('home')
                ->assertSee($user->name);

            // ログアウト
            $browser
                ->click("@logout")
                ->waitUntilMissing('#nprogress')
                ->waitForRoute('login')
                ->assertRouteIs('login');
        });
    }
}
