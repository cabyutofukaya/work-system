<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\ClientTypeTruck;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * トラック会社固有の情報のみテスト
 */
class ClientsTruckTest extends DuskTestCase
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
        $this->client = Client::factory()->create(["client_type_id" => "truck"]);

        // 固有情報
        ClientTypeTruck::factory()->create(['client_id' => $this->client->id]);
    }

    /**
     * 詳細
     *
     * @return void
     * @throws \Throwable
     */
    public function testClientsTruckShow()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visitRoute("clients.show", ["client" => $this->client->id])
                ->waitForRoute("clients.show", ["client" => $this->client->id])
                ->waitUntilMissing('#nprogress');

            // 生成した情報の出現を確認
            $browser
                ->assertSee($this->client->client_type_truck->drivers_count);
        });
    }
}
