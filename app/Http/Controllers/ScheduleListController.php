<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchedule;
use App\Http\Requests\UpdateSchedule;
use App\Models\ScheduleGuest;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Encore\Admin\Grid\Filter\Where;

class ScheduleListController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $select_department = [];
        if ($request->input('department')) {
            $select_department = $request->department;
        }

        $select_department = explode(',', $select_department);
        $select_department = array_filter($select_department);

        // $select_department = ['CCD','ICTS'];

        $users = User::whereIn('department', $select_department)->orderBy('department')->get();

        $department_list = User::select('department')->whereNotNull('department')->groupBy('department')->get();

        $usersData = [];
        $scheduleData = [];

        $backgroudcolor = config('schedule.backgroudColor');
        $department_groupid = config('user.department_groupid');

        $n = 0;
        $t = 0;

        foreach ($users as $user) {


            $tmp = [];
            $tmp['id'] = $user->id;
            $tmp['groupId'] = $user->department;
            $tmp['title'] = $user->name;
            $tmp['type2'] = $user->id;

            $tmp['type1'] = $department_groupid[$user->department] ?? 10;

            $usersData[$n] = $tmp;
            $n++;


            $schedule_list = Schedule::with(['guests'])->where('date', '>=', date('Y-m-d', strtotime('-1 months')))
                ->where([
                    'user_id' => $user->id,
                    'type' => 1,
                    'is_public' => 0,
                ])
                ->get();

            foreach ($schedule_list as $schedule) {

                if ($schedule->is_public == 1 && Auth::id() != $schedule->user_id) {
                    continue;
                }

                $foo = [];
                $foo['id'] = $schedule->id;
                $foo['resourceId'] = $user->id;
                $foo['title'] = $schedule->category ?? '';
                if ($schedule->start_time != '') {
                    $foo['start'] = $schedule->date . ' ' . $schedule->start_time;
                    $foo['end'] = $schedule->date . ' ' . $schedule->end_time;
                    $foo['pops_time'] = $schedule->start_time . '~' . $schedule->end_time;
                } else {
                    $foo['start'] = $schedule->date;
                    $foo['pops_time'] = '[終日]';
                }

                $foo['color'] = '#a1dce3';
                $foo['borderColor'] = 'a1dce3';

                if ($schedule->category != '') {
                    $foo['color'] = $backgroudcolor[$schedule->category];
                    $foo['borderColor'] = $backgroudcolor[$schedule->category];
                    $foo['title'] .= ' ';
                }

                if ($schedule->title != '') {
                    $foo['title'] .= $schedule->title;
                }

                $foo['content'] = $schedule->content ?? '';

                $foo['groupdId'] = $user->department;

                $scheduleData[$t] = $foo;
                $t++;


                //ゲストがいる場合
                if(!$schedule->guests->isEmpty()){
                    foreach($schedule->guests as $guest){

                        $foo['resourceId'] = $guest->user_id;

                        $scheduleData[$t] = $foo;
                        $t++;
                    }
                }

            }


            
        }

        $tmp = [];
        $tmp['id'] = '';
        $tmp['groupId'] = 1000;
        $tmp['title'] = '';
        $tmp['type1'] = 1000000;
        $tmp['type2'] = 1000000;
        $usersData[$n] = $tmp;


        return inertia('ScheduleList', [
            'scheduleData' => $scheduleData,
            'users' => $usersData,
            'departmentList' => $department_list,
            'selectDepartment' => $select_department,
        ]);
    }

    public function redirect_schedule($department)
    {

        $user = User::where('department', $department)->orderBy('id')->first();


        return redirect('/list/schedule/' . $department);
    }
}
