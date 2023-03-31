<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavTest extends DuskTestCase
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
     * グローバルナビ
     *
     * @return void
     * @throws \Throwable
     */
    public function testHome()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("home")
                ->waitForRoute("home")
                ->assertRouteIs("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@salesTodosIndex")
                ->waitForRoute("sales-todos.index")
                ->assertRouteIs("sales-todos.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@officeTodosIndex")
                ->waitForRoute("office-todos.index")
                ->assertRouteIs("office-todos.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@clientTypeTaxibusIndex")
                ->waitForRoute("client-types.clients.index", ["client_type" => "taxibus"])
                ->assertRouteIs("client-types.clients.index", ["client_type" => "taxibus"])
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@clientTypeTruckIndex")
                ->waitForRoute("client-types.clients.index", ["client_type" => "truck"])
                ->assertRouteIs("client-types.clients.index", ["client_type" => "truck"])
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@clientTypeRestaurantIndex")
                ->waitForRoute("client-types.clients.index", ["client_type" => "restaurant"])
                ->assertRouteIs("client-types.clients.index", ["client_type" => "restaurant"])
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@clientTypeTravelIndex")
                ->waitForRoute("client-types.clients.index", ["client_type" => "travel"])
                ->assertRouteIs("client-types.clients.index", ["client_type" => "travel"])
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@productEvaluation")
                ->waitForRoute("product-evaluations")
                ->assertRouteIs("product-evaluations")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@reportIndex")
                ->waitForRoute("reports.index")
                ->assertRouteIs("reports.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@meetingIndex")
                ->waitForRoute("meetings.index")
                ->assertRouteIs("meetings.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@NoticeIndex")
                ->waitForRoute("notices.index")
                ->assertRouteIs("notices.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@documents")
                ->waitForRoute("documents")
                ->assertRouteIs("documents")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@userIndex")
                ->waitForRoute("users.index")
                ->assertRouteIs("users.index")
                ->back()
                ->waitForRoute("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@home")
                ->waitForRoute("home")
                ->assertRouteIs("home")
                ->waitUntilMissing('#nprogress');

            $browser
                ->click("@logout")
                ->waitForRoute("login")
                ->assertRouteIs("login");
        });
    }
}
