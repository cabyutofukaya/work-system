<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DocumentsTest extends DuskTestCase
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
     * 書類一覧画面
     *
     * @return void
     * @throws \Throwable
     */
    public function testDocuments()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("documents")
                ->waitForRoute("documents")
                ->assertRouteIs("documents")
                ->waitUntilMissing('#nprogress');

            $browser
                ->assertSee("書類");
        });
    }
}
