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

class ScheduleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response|ResponseFactory
    {
        $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', Auth::user()->id)->get();

        $user = Auth::user();

        $schedule = [];
        $k = 0;
        if ($schedule_list) {
            foreach ($schedule_list as $data) {

                $tmp = [];
                $tmp['id'] = $data->id;
                $tmp['title'] = $data->title;
            

                if($data->title_type != ''){
                   
                    $tmp['title'] .= '[' . $data->title_type . ']';
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
        

        return inertia('Schedule', [
            'schedule' => $schedule,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        dd('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchedule $request)
    {
        $param = $request->all();
        $param['user_id'] = Auth::id();

        if(!$request->rangeEnabled){

            $schedule = new Schedule();
            $schedule->store_data($param);

        }else{

        
            for($i=date('Y-m-d',strtotime($request->start_date));$i <= date('Y-m-d',strtotime($request->end_date));$i = date('Y-m-d', strtotime($i . '+1 day'))){
                
                $week = date('w',strtotime($i));
                if(($week == 0 && $request->sunday) || ($week == 1 && $request->monday) || ($week == 2 && $request->tuesday) || ($week == 3 && $request->wednesday) || ($week == 4 && $request->thursday) || ($week == 5 && $request->friday) || ($week == 6 && $request->saturday)){
                    $param['date'] = $i;

                    $schedule = new Schedule();
                    $schedule->store_data($param);
                }

               
            }
        }


        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('users.show', ['user' => $request->user_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
        dd($schedule);
        return json_encode($schedule);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(int $scheduleId)
    {

        $schedule = DB::table('schedules')->where('id', $scheduleId)->first();
        $user = User::where('id', $schedule->user_id)->first();

        return inertia('ScheduleEdit', [
            'user' => $user,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateSchedule $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchedule $request,User $user)
    {
        $schedule = new Schedule;

        $param = $request->all();
        if ($request->enabled) {
            $param['start_time'] = null;
            $param['end_time'] = null;
        }
        $schedule->update_data($param);

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('users.show', ['user' => Auth::user()->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        dd($schedule);
    }

    public function getData(int $scheduleId)
    {

        $schedule = DB::table('schedules')->where('id', $scheduleId)->first();
        $user = User::where('id', $schedule->user_id)->first();
        $schedule->userName = $user->name;

        return json_encode($schedule);
    }

    public function delete(Request $request)
    {
        $schedule = new Schedule;
        $schedule->delete_data($request->id);
     
        return redirect()->route('users.show', ['user' => Auth::user()->id]);
    }
}
