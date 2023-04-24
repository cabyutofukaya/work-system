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
        $user = Auth::user();
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
                if ($data->title_type != '') {
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

        $sales_todo_list = DB::table('sales_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();


        if ($sales_todo_list) {
            foreach ($sales_todo_list as $sales_todo) {

                $tmp_clients = DB::table('clients')->where('id', $sales_todo->client_id)->first();

                $tmp = [];
                $tmp['id'] = $sales_todo->id;
                $tmp['title'] =  '[営業]' . $tmp_clients->name;
                $tmp['color'] = '#2e8583';
                $tmp['start'] = $sales_todo->scheduled_at;
                $tmp['content'] = $sales_todo->description;

                $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }


        $sales_part_todo_list = DB::table('sales_todos')
            ->leftJoin('sales_todo_participants', 'sales_todos.id', '=', 'sales_todo_participants.sales_todo_id')
            ->where('sales_todos.scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))
            ->where('sales_todo_participants.user_id', $user->id)->whereNull('sales_todos.deleted_at')
            ->select('sales_todos.*')
            ->get();


        if ($sales_part_todo_list) {
            foreach ($sales_part_todo_list as $sales_part_todo) {

                $tmp_user = DB::table('users')->where('id', $sales_part_todo->user_id)->first();
                $tmp_clients = DB::table('clients')->where('id', $sales_part_todo->client_id)->first();

                $tmp = [];
                $tmp['id'] = $sales_part_todo->id;
                $tmp['title'] =  '[営業]' . $tmp_clients->name . ' /担当者:' . $tmp_user->name . 'さん';
                $tmp['color'] = '#2e8583';
                $tmp['start'] = $sales_part_todo->scheduled_at;
                $tmp['content'] = $sales_part_todo->description;

                $tmp['classNames'] = 'sales_part_todo_list';

                // $tmp['url'] = '/sales-todos/' . $sales_part_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        $office_todo_list = DB::table('office_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        if ($office_todo_list) {
            foreach ($office_todo_list as $office_todo) {

                $tmp = [];
                $tmp['id'] = $office_todo->id;
                $tmp['title'] = $office_todo->title . '(' .  '社内' . ')';
                $tmp['color'] = '#2a5791';
                $tmp['start'] = $office_todo->scheduled_at;
                $tmp['content'] = $office_todo->description;

                $tmp['url'] = '/office-todos/' . $office_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }


        $office_part_todo_list = DB::table('office_todos')
            ->leftJoin('office_todo_participants', 'office_todos.id', '=', 'office_todo_participants.office_todo_id')
            ->where('office_todos.scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))
            ->where('office_todo_participants.user_id', $user->id)->whereNull('office_todos.deleted_at')
            ->select('office_todos.*')
            ->get();


        if ($office_part_todo_list) {
            foreach ($office_part_todo_list as $office_part_todo) {

                $tmp_user = DB::table('users')->where('id', $office_part_todo->user_id)->first();

                $tmp = [];
                $tmp['id'] = $office_part_todo->id;
                $tmp['title'] =  $office_part_todo->title . '(社内) /'  . $tmp_user->name;
                $tmp['color'] = '#2e8583';
                $tmp['start'] = $office_part_todo->scheduled_at;
                $tmp['content'] = $office_part_todo->description;

                $tmp['classNames'] = 'office_part_todo_list';

                // $tmp['url'] = '/sales-todos/' . $sales_part_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        return inertia('Schedule', [
            'user' => $user,
            'schedule' => $schedule,
            'loginUser' => Auth::user(),
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

        if (!$request->rangeEnabled) {

            $schedule = new Schedule();
            $schedule->store_data($param);
        } else {


            for ($i = date('Y-m-d', strtotime($request->start_date)); $i <= date('Y-m-d', strtotime($request->end_date)); $i = date('Y-m-d', strtotime($i . '+1 day'))) {

                $week = date('w', strtotime($i));
                if (($week == 0 && $request->sunday) || ($week == 1 && $request->monday) || ($week == 2 && $request->tuesday) || ($week == 3 && $request->wednesday) || ($week == 4 && $request->thursday) || ($week == 5 && $request->friday) || ($week == 6 && $request->saturday)) {
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

        if($request->route == 1){
            return redirect()->route('schedule.index');
        }

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
    public function update(UpdateSchedule $request, User $user)
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
