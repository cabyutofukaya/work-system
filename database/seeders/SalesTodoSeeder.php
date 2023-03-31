<?php

namespace Database\Seeders;

use App\Models\SalesTodo;
use App\Models\SalesTodoParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Log;
use Throwable;

class SalesTodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();

        // 全ユーザに複数のToDoを作成
        foreach ($users as $user) {
            SalesTodo::factory()
                ->count(30)
                ->create(["user_id" => $user->id])
                // hasメソッドではランダムな個数のリレーションを生成できないためeachで代替
                ->each(function (SalesTodo $sales_todo) use ($users, $user) {
                    // 日報作成者以外のユーザ
                    $users_without_owner = $users->where("id", "!=", $user->id)
                        ->map(function ($user) {
                            return ["user_id" => $user->id];
                        })
                        ->toArray();
                    shuffle($users_without_owner);

                    try {
                        SalesTodoParticipant::factory()
                            ->count(rand(0, 2))
                            ->state(new Sequence(...$users_without_owner))
                            ->create(['sales_todo_id' => $sales_todo->id]);
                    } catch (Throwable $e) {
                        // 既にレコードが存在している場合はエラーとなるためスキップ
                        Log::warning("ClientTypeTaxibus::factory->create: " . $e->getMessage());
                    }
                });
        }
    }
}
