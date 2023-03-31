<?php

namespace App\Console\Commands;

use App\Imports\UsersImport;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ユーザ情報インポート(storage/import/users.csv)';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(): int
    {
        try {
            // ユーザデータをインポート
            Excel::import(new UsersImport, storage_path("import/users.csv"));
        } catch (ValidationException $e) {
            // すべてのエラーを収集する
            $failures = $e->failures();
            $failure_messages = [];

            foreach ($failures as $failure) {
                $failure_messages[] = sprintf(
                    "row:%s column:%s %s [%s]",
                    $failure->row(),
                    $failure->attribute(),
                    implode("\n", $failure->errors()),
                    $failure->values()[$failure->attribute()]
                );
            }

            // メッセージを設定して例外をスロー
            throw new Exception(implode("\n", $failure_messages));
        }

        return 0;
    }
}
