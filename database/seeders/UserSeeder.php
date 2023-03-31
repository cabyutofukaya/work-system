<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $products = Product::get();

        // テスト用にメールアドレス/パスワードを指定してユーザを作成
        User::factory()
            ->create([
                'username' => 'user',
                'email' => 'example@example.com',
                'password' => Hash::make('secret'),
            ])
            ->each(function (User $user) use ($products) {
                // 商材
                $user->products()->sync($products->pluck("id"));
            });

        User::factory()
            ->count(24)
            ->create()
            // hasメソッドではランダムな個数のリレーションを生成できないためeachで代替
            ->each(function (User $user) use ($products) {
                // 商材
                $products
                    ->random(rand(1, $products->count()))
                    ->each(function ($product) use ($user) {
                        $user->products()->attach($product["id"]);
                    });
            });
    }
}
