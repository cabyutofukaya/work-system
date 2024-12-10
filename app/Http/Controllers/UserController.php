<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ShowMeeting;
use App\Models\Product;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(Request $request): Response|ResponseFactory
    {
        // $users = User::orderBy('name_kana', 'ASC')->get();

        $users = $this->search($request);

        $products = Product::orderBy('id','ASC')->get(['name']);
        $productList = [];
        foreach ($products as $k => $product) {
            $productList[$k] = $product->name;
        }


        return inertia('Users', [
            // 'clients' => $clients->paginate()->withQueryString(),
            'users' => $users->paginate()->withQueryString(),

            'user' => Auth::user(),

            'productList' => $productList,

            // 検索フォームの初期値
            'form_params' => [
                'word' => $request->input('word'),
                'product' => $request->input('product'),
            ],
        ]);
    }


    /**
     * 
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Illuminate\Validation\ValidationException
     */
    public function search(Request $request): Builder
    {
        // GETメソッドではInertiaによるバリデーションエラー処理が行われないため
        // リダイレクトを行わずエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new ShowMeeting())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $users = User::orderBy('name_kana', 'ASC')->with(['products:id,name']);

        // ワード検索
        if ($request->filled('word')) {
            foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validator->validated()["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $users->where(function ($query) use ($word) {

                    $query->orWhereLike('name', $word);
                    $query->orWhereLike('email', $word);
                    $query->orWhereLike('department', $word);
                });
            }
        }

        // ワード検索
        if ($request->filled('product')) {
            $users->whereHas('products', function ($query) use ($request) {
                $query->where('name', $request['product']);
            });
        }


        return $users;
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

        // dd($user->toSql(), $user->getBindings());

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

        $sales_todo_list = DB::table('sales_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();


        if ($sales_todo_list) {
            foreach ($sales_todo_list as $sales_todo) {

                $tmp_clients = DB::table('clients')->where('id', $sales_todo->client_id)->first();


                $tmp = [];
                $tmp['id'] = $sales_todo->id;
                // $tmp['title'] =  '[営業]';
                $tmp['title'] = '[営業] ' . $tmp_clients->name;
                $tmp['title'] = mb_substr($tmp['title'], 0, 14);
                $tmp['color'] = '#fa3c3c';
                $tmp['start'] = date('Y-m-d G:i', strtotime($sales_todo->scheduled_at));
                $tmp['content'] = $sales_todo->description;

                $tmp['pops_tile'] = '[営業] ' . $tmp_clients->name;
                $tmp['pops_time'] = date('G:i', strtotime($sales_todo->scheduled_at));

                // $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';
                $tmp['class'] = 'sales-todos';

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
                $tmp['start'] = date('Y-m-d G:i', strtotime($office_todo->scheduled_at));
                $tmp['content'] = $office_todo->description;

                $tmp['pops_tile'] = ' (' .  '社内' . ') ' . $office_todo->title;
                $tmp['pops_time'] = date('G:i', strtotime($office_todo->scheduled_at));

                // $tmp['url'] = '/office-todos/' . $office_todo->id  . '/edit';

                $tmp['class'] = 'office-todos';

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
