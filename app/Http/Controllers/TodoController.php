<?php

namespace App\Http\Controllers;

use App\Models\OfficeTodo;
use App\Models\SalesTodo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TodoController extends Controller
{

    /**
     * ToDoリスト
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function is_read(Request $request)
    {

        //TODO既読
        OfficeTodo::where([
            'user_id' => Auth::id(),
            'is_completed' => 0,
        ])
            ->update([
                'is_readed' => date('Y-m-d H:i'),
            ]);

        //TODO既読
        SalesTodo::where([
            'user_id' => Auth::id(),
            'is_completed' => 0,
        ])
            ->update([
                'is_readed' => date('Y-m-d H:i'),
            ]);

        return back();
    }
}
