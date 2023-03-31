<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportVisitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportVisitorSeeder extends Seeder
{
    /**
     * 日報閲覧者情報シーダー
     *
     * @return void
     */
    public function run()
    {
        // ユーザIDリストを生成
        $user_ids = User::get()->map(function ($user) {
            return ["user_id" => $user->id];
        })->toArray();

        foreach (Report::orderBy("id")->get() as $report) {
            // ユーザリストをシャッフル
            $user_ids_shuffled = $user_ids;
            shuffle($user_ids_shuffled);

            try {
                // 各日報に0-5人の閲覧者を設定する
                ReportVisitor::factory()
                    ->count(rand(0, 5))
                    ->state(new Sequence(...$user_ids_shuffled))
                    ->create(["report_id" => $report->id]);
            } catch (Throwable $e) {
                // 既にレコードが存在している場合はエラーとなるためスキップ
                Log::warning("ReportVisitor::factory->create: " . $e->getMessage());
            }
        }
    }
}
