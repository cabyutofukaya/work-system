<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        $users = User::get();

        return inertia('Users', [
            'users' => $users,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(User $user): Response|ResponseFactory
    {
        // dd($user);

        $user->load("products:id,name");

        $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $schedule = [];
        $k = 0;
        if ($schedule_list) {
            $backgroudcolor = config('schedule.backgroudColor');
            foreach ($schedule_list as $data) {

                $tmp = [];
                $tmp['id'] = $data->id;
                $tmp['title'] = $data->title;
                $tmp['color'] = '#2e8583';
                if($data->title_type != ''){
                    $tmp['title'] .= '[' . $data->title_type . ']';
                    $tmp['color'] = $backgroudcolor[$data->title_type];
                }
                $tmp['start'] = $data->date;
                if ($data->start_time) {
                    $tmp['start'] .= ' ' . $data->start_time;
                    $tmp['end'] = $data->date . ' ' . $data->end_time;
                }
                $tmp['content'] = $data->content;

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        return inertia('UsersShow', [
            'user' => $user,
            'schedule' => $schedule,
            'loginUser' => Auth::user(),
        ]);
    }
}
