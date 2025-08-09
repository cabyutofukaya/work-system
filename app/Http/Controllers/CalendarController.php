<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Calendar;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = Calendar::query();

        // ユーザーIDで絞る（指定があれば）
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return inertia('Calendar', [
            'month' => date('M'),
            'loginUser' => Auth::user(),
            'publicList' => config('calendar.is_public'),
            'notificationList' => config('calendar.notification_list'),
            'userList' => User::get(),
            'categoryList' => array_keys(config('calendar.category')),
        ]);
    }
}
