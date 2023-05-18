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
                $tmp['title'] = '';
                $tmp['pops_tile'] = '';
                $tmp['pops_time'] = '';

                $tmp['id'] = $data->id;
                if (!($data->start_time)) {
                    // $tmp['title'] = '(終日) ';
                    $tmp['title'] = '';
                    $tmp['pops_time'] = '(終日) ';
                }else{
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
                    $tmp['title'] = mb_substr($tmp['title'] , 0 ,14);
                }

                $tmp['title'] .= $data->title;
                $tmp['pops_tile'] .= $data->title;
               
                $tmp['content'] = $data->content ?? '';

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
                // $tmp['title'] =  '[営業]';
                $tmp['title'] = '[営業] ' . $tmp_clients->name;
                $tmp['title'] = mb_substr($tmp['title'] , 0 ,14);
                $tmp['color'] = '#fa3c3c'; 
                $tmp['start'] = date('Y-m-d G:i',strtotime($sales_todo->scheduled_at));
                $tmp['content'] = $sales_todo->description;

                $tmp['pops_tile'] = '[営業] ' . $tmp_clients->name;
                $tmp['pops_time'] = date('G:i',strtotime($sales_todo->scheduled_at));

                $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';

                $schedule[$k] = $tmp;
                $k++;
            }
        }

        $office_todo_list = DB::table('office_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        if ($office_todo_list) {
            foreach ($office_todo_list as $office_todo) {

            
                $tmp = [];
                $tmp['id'] = $office_todo->id;
                $tmp['title'] = $office_todo->title;
                $tmp['color'] = '#0c44fa';
                $tmp['start'] = date('Y-m-d G:i',strtotime($office_todo->scheduled_at));
                $tmp['content'] = $office_todo->description;

                $tmp['pops_tile'] = ' (' .  '社内' . ') ' . $office_todo->title;
                $tmp['pops_time'] = date('G:i',strtotime($office_todo->scheduled_at));

                $tmp['url'] = '/office-todos/' . $office_todo->id  . '/edit';

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
