<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests\StoreSchedule;
use App\Http\Requests\UpdateSchedule;
use App\Models\ScheduleCategory;
use App\Models\User;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {

        return inertia('Schedule', [
            'month' => date('M'),
            'loginUser' => Auth::user(),
            'publicList' => config('schedule.is_public'),
            'notificationList' => config('schedule.notification_list'),
            'userList' => User::where('id', '<>', Auth::id())->get(['id', 'name', 'email']),
            'timeList' => config('schedule.timeList'),
            'hourList' => config('schedule.hourList'),
            'categoryList' => array_keys(config('schedule.category')),
        ]);
    }
}
