<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;

class CalendarSeeder extends Seeder
{
    public function run(): void
    {

        // カテゴリ一覧（config/calendar.php に定義されている前提）
        $categories = array_keys(config('calendar.category'));

        foreach (range(1, 10) as $i) {
            Calendar::create([
                'user_id' => 1,
                'title' => "サンプル予定 {$i}",
                'content' => "これはサンプルの詳細内容です。",
                'category' => $categories[array_rand($categories)],
                'date' => Carbon::now()->addDays(rand(-5, 5))->toDateString(),
                'all_day' => true,
                'is_public' => rand(0, 1),
            ]);


             Calendar::create([
                'user_id' => 2,
                'title' => "サンプル予定 {$i}",
                'content' => "これはサンプルの詳細内容です。",
                'category' => $categories[array_rand($categories)],
                'date' => Carbon::now()->addDays(rand(-5, 5))->toDateString(),
                'all_day' => true,
                'is_public' => rand(0, 1),
            ]);
        }
    }
}
