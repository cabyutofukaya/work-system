<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchedule;
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
        $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', Auth::user()->id)->where('deleted_at', 0)->get();

        $user = Auth::user();

        $schedule = [];
        $k = 0;
        if ($schedule_list) {
            foreach ($schedule_list as $data) {

                $tmp = [];
                $tmp['id'] = $data->id;
                $tmp['title'] = $data->title;
                $tmp['start'] = $data->date;
                if ($data->start_time) {
                    $tmp['start'] .= ' ' . $data->start_time;
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

        dd($request->all());

        if(!$request->rangeEnabled){
            (new Schedule())
            ->fill($request->safe()->merge(['user_id' => Auth::id()])->all())
            ->save();
        }else{

            $param = $request->all();
            if ($request->enabled) {
                $param['start_time'] = null;
                $param['end_time'] = null;
            }
           
            for($i=date('Y-m-d',strtotime($request->start_date));$i <= date('Y-m-d',strtotime($request->end_date));$i = date('Y-m-d', strtotime($i . '+1 day'))){
                
                $param['date'] = $i;

                $schedule = new Schedule();
                $schedule->store_data($param);
            }
        }


       

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('schedule.index');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSchedule $request)
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

        return redirect()->route('schedule.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }

    public function getData(int $scheduleId)
    {

        $schedule = DB::table('schedules')->where('id', $scheduleId)->first();
        $user = User::where('id', $schedule->user_id)->first();
        $schedule->userName = $user->name;

        return json_encode($schedule);
    }
}
