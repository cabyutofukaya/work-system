<?php

namespace App\Console\Commands;

use App\Imports\ClientsImport;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '会社情報インポート(storage/import/clients/[CLIENT_TYPE].csv)';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $failure_messages = [];

        foreach (array_keys(config("const.client_types")) as $type) {
            $csv = storage_path("import/clients/$type.csv");
            $this->line("load: " . $csv);

            try {
                // 会社データをインポート
                Excel::import(new ClientsImport, $csv);
            } catch (ValidationException $e) {
                // すべてのエラーを収集する
                $failures = $e->failures();

                foreach ($failures as $failure) {
                    $failure_messages[] = sprintf(
                        "type:%s row:%s column:%s %s [%s]",
                        $type,
                        $failure->row(),
                        $failure->attribute(),
                        implode("\n", $failure->errors()),
                        $failure->values()[$failure->attribute()]
                    );
                }
            }
        }

        if (count($failure_messages)) {
            // メッセージを設定して例外をスロー
            throw new Exception(implode("\n", $failure_messages));
        }

        return 0;
    }
}
