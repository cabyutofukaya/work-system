<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchedule;
use App\Http\Requests\UpdateSchedule;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserScheduleController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $department = $user->department;

        $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $schedule = [];
        $k = 0;
        if ($schedule_list) {
            $backgroudcolor = config('schedule.backgroudColor');
            foreach ($schedule_list as $data) {

                $tmp = [];
                $tmp['title'] = '';
                $tmp['pops_tile'] = '';
                $tmp['pops_time'] = '';

                $tmp['id'] = $data->id;
                if (!($data->start_time)) {
                    // $tmp['title'] = '(終日) ';
                    $tmp['title'] = '';
                    $tmp['pops_time'] = '(終日) ';
                } else {
                    $tmp['pops_time'] = $data->start_time . ' ~ ' . $data->end_time . "\n";
                }

                $tmp['color'] = '#747876';
                $tmp['borderColor'] = '#747876';
                if ($data->title_type != '') {
                    $tmp['title'] .= '[' . $data->title_type . '] ';
                    $tmp['color'] = $backgroudcolor[$data->title_type];
                    $tmp['borderColor'] = $backgroudcolor[$data->title_type];
                    $tmp['pops_tile'] .= '[' . $data->title_type . '] ';
                }
                $tmp['start'] = $data->date;
                if ($data->start_time) {
                    $tmp['start'] .= ' ' . $data->start_time;
                    $tmp['end'] = $data->date . ' ' . $data->end_time;
                    $tmp['title'] = mb_substr($tmp['title'], 0, 14);
                }
                $tmp['title'] .= $data->title;
                $tmp['pops_tile'] .= $data->title;

                $tmp['content'] = $data->content ?? '';

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        return inertia('ScheduleList', [
            'user' => $user,
            'schedule' => $schedule,
        ]);
    }
}
