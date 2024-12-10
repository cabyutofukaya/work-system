<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TodoAlertMail;
use App\Models\OfficeTodo;
use App\Models\SalesTodo;
use App\Models\User;

class TodoMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:todomail';

    protected $description = 'TODOのメールを送信する';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $param = [];

        $users = User::get();

        foreach ($users as $user) {

            // if($user->id != 9){
            //     continue;
            // }

            $today_start = date('Y-m-d') . ' 00:00:00';
            $today_end = date('Y-m-d') . ' 24:00:00';
    
            $officeTodo = OfficeTodo::where([
                'user_id' => $user->id,
                'is_completed' => 0,
            ])
            // ->where('scheduled_at','>=',$today_start)
            ->where('scheduled_at','<=',$today_end)
            ->get();
    
            //TODO件数取得(sales)
            $salesTodo = SalesTodo
            ::with(['client:id,name'])
            ->where([
                'user_id' => $user->id,
                'is_completed' => 0,
            ])
            // ->where('scheduled_at','>=',$today_start)
            ->where('scheduled_at','<=',$today_end)
            ->get();

            $param = [];
            $param['email'] = $user->email;
            $param['office_todo'] = $officeTodo;
            $param['sales_todo'] = $salesTodo;
            $param['count'] = count($officeTodo) + count($salesTodo);


            if($param['count'] > 0){
                Mail::send(new TodoAlertMail($param));
            }
    
        }
    }
}
