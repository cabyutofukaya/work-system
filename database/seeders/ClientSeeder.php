<?php

namespace Database\Seeders;

use App\Models\BusinessDistrict;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientTypeRestaurant;
use App\Models\ClientTypeTaxibus;
use App\Models\ClientTypeTravel;
use App\Models\ClientTypeTruck;
use App\Models\ContactPerson;
use App\Models\Genre;
use App\Models\Product;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::get();
        $genres = Genre::get();

        Client::factory()
            ->count(50)
            ->create()
            // hasメソッドではランダムな個数のリレーションを生成できないためeachで代替
            ->each(function (Client $client) use ($products, $genres) {
                // 営業所
                Branch::factory()
                    ->times(random_int(1, 3))
                    ->create(['client_id' => $client->id]);

                // 相手先担当者
                ContactPerson::factory()
                    ->times(random_int(1, 3))
                    ->create(['client_id' => $client->id]);

                // 商材
                $client->products()->detach();
                $products
                    ->random(rand(1, $products->count()))
                    ->each(function ($product) use ($client) {
                        $client->products()->attach($product["id"]);
                    });

                // ジャンル
                $genres_this_client_type = $genres->collect()->where("client_type_id", $client->client_type_id);
                $client->genres()->detach();
                $genres_this_client_type
                    ->random(rand(1, $genres_this_client_type->count()))
                    ->each(function ($genre) use ($client) {
                        $client->genres()->attach($genre["id"]);
                    });

                // 営業エリア
                if ($client->client_type_id === "taxibus") {
                    BusinessDistrict::factory()
                        ->times(random_int(1, 3))
                        ->create(['client_id' => $client->id]);
                }

                // 保有車両
                if ($client->client_type_id === "taxibus") {
                    Vehicle::factory()
                        ->times(random_int(5, 15))
                        ->create(['client_id' => $client->id]);
                }

                // タクシー・バス会社固有情報
                if ($client->client_type_id === "taxibus") {
                    try {
                        ClientTypeTaxibus::factory()
                            ->create(['client_id' => $client->id]);
                    } catch (Throwable $e) {
                        // 既にレコードが存在している場合はエラーとなるためスキップ
                        Log::warning("ClientTypeTaxibus::factory->create: " . $e->getMessage());
                    }
                }

                // トラック会社固有情報
                if ($client->client_type_id === "truck") {
                    try {
                        ClientTypeTruck::factory()
                            ->create(['client_id' => $client->id]);
                    } catch (Throwable $e) {
                        // 既にレコードが存在している場合はエラーとなるためスキップ
                        Log::warning("ClientTypeTruck::factory->create: " . $e->getMessage());
                    }
                }

                // 飲食店固有情報
                if ($client->client_type_id === "restaurant") {
                    try {
                        ClientTypeRestaurant::factory()
                            ->create(['client_id' => $client->id]);
                    } catch (Throwable $e) {
                        // 既にレコードが存在している場合はエラーとなるためスキップ
                        Log::warning("ClientTypeRestaurant::factory->create: " . $e->getMessage());
                    }
                }

                // 旅行業者など
                if ($client->client_type_id === "travel") {
                    try {
                        ClientTypeTravel::factory()
                            ->create(['client_id' => $client->id]);
                    } catch (Throwable $e) {
                        // 既にレコードが存在している場合はエラーとなるためスキップ
                        Log::warning("ClientTypeTravel::factory->create: " . $e->getMessage());
                    }
                }
            });
    }
}
