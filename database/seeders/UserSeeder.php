<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // テストユーザーを10件作成
        for ($i = 1; $i <= 10; $i++) {
            $username = 'user' . str_pad($i, 2, '0', STR_PAD_LEFT);

            User::create([
                'username' => $username,
                'name' => 'テストユーザー' . $i,
                'email' => $username . '@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'tel' => '090-1234-56' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'department' => '部署' . $i,
                'remember_token' => Str::random(10),
                'login_at' => now()->subDays(rand(0, 30)),
                'created_at' => now()->subDays(rand(30, 60)),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
