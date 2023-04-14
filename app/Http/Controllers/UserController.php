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

        $sales_todo_list = DB::table('sales_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        if ($sales_todo_list) {
            foreach ($sales_todo_list as $sales_todo) {

                $tmp = [];
                $tmp['id'] = $sales_todo->id;
                $tmp['title'] = '営業TODO';
                $tmp['color'] = '#2e8583';
                $tmp['start'] = $sales_todo->scheduled_at;
                $tmp['content'] = $sales_todo->description;

                $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        $userNum = 1;

        $sales_part_todo_list = DB::table('sales_todos')
        ->leftJoin('sales_todo_participants', 'sales_todos.id', '=', 'sales_todo_participants.sales_todo_id')
        ->where('sales_todos.scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))
        ->where('sales_todo_participants.user_id', $userNum)->whereNull('sales_todos.deleted_at')
        ->select('sales_todos.*')
        ->get();

        
        if ($sales_part_todo_list) {
            foreach ($sales_part_todo_list as $sales_part_todo) {

                $tmp_user = DB::table('users')->where('id',$sales_part_todo->user_id)->first();

                $tmp = [];
                $tmp['id'] = $sales_part_todo->id;
                $tmp['title'] = '営業TODO' . ' (担当者:' . $tmp_user->name . 'さん)';
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
                $tmp['title'] = '社内TODO' . '(' . $office_todo->title . ')';
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
        ->where('office_todo_participants.user_id', $userNum)->whereNull('office_todos.deleted_at')
        ->select('office_todos.*')
        ->get();

        
        if ($office_part_todo_list) {
            foreach ($office_part_todo_list as $office_part_todo) {

                $tmp_user = DB::table('users')->where('id',$office_part_todo->user_id)->first();

                $tmp = [];
                $tmp['id'] = $office_part_todo->id;
                $tmp['title'] = '社内TODO' . ' (担当者:' . $tmp_user->name . 'さん)';
                $tmp['color'] = '#2e8583';
                $tmp['start'] = $office_part_todo->scheduled_at;
                $tmp['content'] = $office_part_todo->description;

                $tmp['classNames'] = 'office_part_todo_list';

                // $tmp['url'] = '/sales-todos/' . $sales_part_todo->id  . '/edit';

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
