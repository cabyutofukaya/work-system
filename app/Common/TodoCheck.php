<?php

namespace app\Common;

use App\Models\OfficeTodo;
use App\Models\SalesTodo;
use Illuminate\Support\Facades\Auth;

class TodoCheck
{
    public static function check()
    {
        $flag_array = [];
        $flag_array['sales'] = false;
        $flag_array['offices'] = false;

        $today = date('Y-m-d H:i');

        //TODO件数取得(office)
        $officeTodo = OfficeTodo::where([
            'user_id' => Auth::id(),
            'is_completed' => 0,
        ])
            ->whereNull('is_readed')
            ->where('scheduled_at', '<=', date('Y-m-d H:i',strtotime('+3 hours')))
            ->count();

        //TODO件数取得(sales)
        $salesTodo = SalesTodo::where([
            'user_id' => Auth::id(),
            'is_completed' => 0,
        ])
            ->where('scheduled_at', '<=', date('Y-m-d H:i', strtotime('+3 hours')))
            ->whereNull('is_readed')
            ->count();

        // 存在する場合、モーダルtrue
        if ($officeTodo) {
            $flag_array['offices'] = true;
        }

        if ($salesTodo) {
            $flag_array['sales'] = true;
        }

        return $flag_array;
    }
}
