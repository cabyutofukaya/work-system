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
use Encore\Admin\Grid\Filter\Where;

class ScheduleListController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($department)
    {

        $users = User::where('department', $department)->get();

        $department_list = User::select('department')->whereNotNull('department')->groupBy('department')->get();


        $usersData = [];
        $scheduleData = [];

        $backgroudcolor = config('schedule.backgroudColor');


        $n = 0;
        $t = 0;

        foreach ($users as $user) {


            $tmp = [];
            $tmp['id'] = $user->id;
            $tmp['groupId'] = 100;
            $tmp['title'] = $user->name;
            $tmp['type1'] = $user->id;
            $tmp['type2'] = $user->id;

            //ICTS独自の並び
            if($user->department == 'ICTS'){
                if($user->id == 89 || $user->id == 55 || $user->id == 12 || $user->id == 90 || $user->id == 65){
                    $tmp['groupId'] = 2;
                    if($user->id == 89){
                        $tmp['type1'] = 101;
                    }
                    if($user->id == 55){
                        $tmp['type1'] = 102;
                    }
                    if($user->id == 12){
                        $tmp['type1'] = 103;
                    }
                    if($user->id == 90){
                        $tmp['type1'] = 104;
                    }
                    if($user->id == 65){
                        $tmp['type1'] = 105;
                    }

                }else{
                    $tmp['groupId'] = 1;

                    if($user->id == 27){
                        $tmp['type1'] = 1;
                    }
                    if($user->id == 63){
                        $tmp['type1'] = 2;
                    }
                    if($user->id == 68){
                        $tmp['type1'] = 3;
                    }
                    if($user->id == 71){
                        $tmp['type1'] = 4;
                    }
                    if($user->id == 20){
                        $tmp['type1'] = 5;
                    }
                    if($user->id == 47){
                        $tmp['type1'] = 6;
                    }
                   
                    if($user->id == 10){
                        $tmp['type1'] = 8;
                    }
                }
            }

            $usersData[$n] = $tmp;
            $n++;


            $schedule_list = Schedule::where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->get();

            foreach ($schedule_list as $schedule) {

                if($schedule->is_public == 1 && Auth::id() != $schedule->user_id){
                    continue;
                }

                $foo = [];
                $foo['id'] = $schedule->id;
                $foo['resourceId'] = $user->id;
                $foo['title'] = $schedule->title_type ?? '';
                if ($schedule->start_time != '') {
                    $foo['start'] = $schedule->date . ' ' . $schedule->start_time;
                    $foo['end'] = $schedule->date . ' ' . $schedule->end_time;
                    $foo['pops_time'] = $schedule->start_time . '~' . $schedule->end_time;
                } else {
                    $foo['start'] = $schedule->date;
                    $foo['pops_time'] = '[終日]';
                }

                $foo['color'] = '#747876';
                $foo['borderColor'] = '#747876';

                if ($schedule->title_type != '') {
                    $foo['color'] = $backgroudcolor[$schedule->title_type];
                    $foo['borderColor'] = $backgroudcolor[$schedule->title_type];
                    $foo['title'] .= ' ';
                }

                if ($schedule->title != '') {
                    $foo['title'] .= $schedule->title;
                }

                $foo['content'] = $schedule->content ?? '';
               

                $scheduleData[$t] = $foo;
                $t++;
            }
        }




        $tmp = [];
        $tmp['id'] = '';
        $tmp['groupId'] = 1000;
        $tmp['title'] = '';
        $tmp['type1'] = 1000000;
        $tmp['type2'] = 1000000;
        $usersData[$n] = $tmp;

    

        return inertia('ScheduleList20240507', [
            'scheduleData' => $scheduleData,
            'users' => $usersData,
            'department_list' => $department_list,
            'department' => $department,
        ]);
    }

    public function redirect_schedule($department)
    {

        $user = User::where('department', $department)->orderBy('id')->first();


        return redirect('/list/schedule/' . $department);
    }

  
}
