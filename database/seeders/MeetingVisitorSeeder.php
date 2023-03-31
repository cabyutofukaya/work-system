<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingVisitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class MeetingVisitorSeeder extends Seeder
{
    /**
     * 議事録閲覧者情報シーダー
     *
     * @return void
     */
    public function run()
    {
        // ユーザIDリストを生成
        $user_ids = User::get()->map(function ($user) {
            return ["user_id" => $user->id];
        })->toArray();

        foreach (Meeting::orderBy("id")->get() as $meeting) {
            // ユーザリストをシャッフル
            $user_ids_shuffled = $user_ids;
            shuffle($user_ids_shuffled);

            try {
                // 各議事録に0-5人の閲覧者を設定する
                MeetingVisitor::factory()
                    ->count(rand(0, 5))
                    ->state(new Sequence(...$user_ids_shuffled))
                    ->create(["meeting_id" => $meeting->id]);
            } catch (Throwable $e) {
                // 既にレコードが存在している場合はエラーとなるためスキップ
                Log::warning("MeetingVisitor::factory->create: " . $e->getMessage());
            }
        }
    }
}