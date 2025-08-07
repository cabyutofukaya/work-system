<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        // 1人目のユーザーに10件のTodoを作成
        $user = User::first(); // または factoryで複数ユーザー作ってもOK

        if ($user) {
            for ($i = 1; $i <= 4; $i++) {
                Todo::create([
                    'user_id' => $user->id,
                    'title' => "テストTODO {$i}",
                    'date' => Carbon::today()->addDays($i),
                    'is_done' => $i % 2 === 0,
                ]);
            }
        }
    }
}
