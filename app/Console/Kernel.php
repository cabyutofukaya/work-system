<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // telescopeデータ刈り込み 48日分保存
        $schedule->command('telescope:prune --hours=1152')->daily();

        // バックアップ
        $schedule->command('backup:run --only-db')
            ->daily()->at(env('BACKUP_RUN_DAILY_AT', '03:00'))
            ->withoutOverlapping(12)
            ->before(function () {
                // チェック前にclean実行
                Artisan::call('backup:clean');
            })
            ->environments(['production']); // 本番環境のみ

        // バックアップ状況をモニタしてメール送信
        $schedule->command('backup:monitor')
            ->monthlyOn(1, '14:00')
            ->before(function () {
                // チェック前にclean実行
                Artisan::call('backup:clean');
            })
            ->environments(['production']); // 本番環境のみ;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
