<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingComment;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Meeting::factory()
            ->count(30)
            ->create()
            // hasメソッドではランダムな個数のリレーションを生成できないためeachで代替
            ->each(function (Meeting $meeting) {
                // 日報コメント 0-2件設定
                $meeting->meeting_comments()->saveMany(
                    MeetingComment::factory()->times(random_int(0, 2))->make()
                );
            });
    }
}
