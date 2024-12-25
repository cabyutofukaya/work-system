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

    public function index($department, $user)
    {
        $user = User::find($user);

        $member_list = User::where('department', $department)->get();

        $schedule_list_data = [];

        foreach ($member_list as $member) {
            $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $member->id)->whereNull('deleted_at')->get();

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
                    $tmp['title'] =  '[営業]' . $tmp_clients->name;
                    $tmp['title'] = mb_substr($tmp['title'], 0, 14);
                    $tmp['color'] = '#fc1814';
                    $tmp['start'] = date('Y-m-d H:i', strtotime($sales_todo->scheduled_at));
                    $tmp['content'] = $sales_todo->description;

                    $tmp['pops_tile'] = '[営業]' . $tmp_clients->name;
                    $tmp['pops_time'] = date('G:i', strtotime($sales_todo->scheduled_at));

                    // $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';
                    $tmp['class'] = 'office-todos';
                    $schedule[$k] = $tmp;
                    $k++;
                }
            }

            $office_todo_list = DB::table('office_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

            if ($office_todo_list) {
                foreach ($office_todo_list as $office_todo) {

                    $tmp = [];
                    $tmp['id'] = $office_todo->id;
                    $tmp['title'] = '[' .  'ToDo' . ']' . $office_todo->title;
                    $tmp['color'] = '#d9d618';
                    $tmp['start'] = date('Y-m-d H:i', strtotime($office_todo->scheduled_at));
                    $tmp['content'] = $office_todo->description;

                    $tmp['pops_tile'] = '[' .  'ToDo' . ']' . "\n" . $office_todo->title;
                    $tmp['pops_time'] = date('G:i', strtotime($office_todo->scheduled_at));
                    $tmp['class'] = 'sales-todos';
                    // $tmp['url'] = '/office-todos/' . $office_todo->id  . '/edit';

                    $schedule[$k] = $tmp;
                    $k++;
                }
            }

            $schedule_list_data[$member->id] = $schedule;
        }


        $department_list = User::select('department')->whereNotNull('department')->groupBy('department')->get();


        return inertia('ScheduleList', [
            'user' => $user,
            'schedule' => $schedule,
            'loginUser' => Auth::user(),
            'member_list' => $member_list,
            'department' => $department,
            'department_list' => $department_list,
            'schedule_list' => $schedule_list_data,

        ]);
    }

    public function redirect_schedule($department)
    {

        $user = User::where('department', $department)->orderBy('id')->first();


        return redirect('/schedule/' . $department . '/' . $user->id);
    }

    public function change()
    {
        $dataList = DB::table('demo_form')->get();

        $kenList = [
            '12' => '千葉県',
            '13' => '東京都',
            '14' => '神奈川県',
            '15' => '新潟県',
            '16' => '富山県',
            '17' => '石川県',
            '18' => '福井県',
            '19' => '山梨県',
            '20' => '長野県',
            '21' => '岐阜県',

            '22' => '静岡県',
            '23' => '愛知県',
            '24' => '三重県',
            '25' => '滋賀県',

            '26' => '京都府',
            '27' => '大阪府',
            '28' => '兵庫県',
            '29' => '奈良県',
            '30' => '和歌山県',
            '31' => '鳥取県',
            '32' => '島根県',
            '33' => '岡山県',
            '34' => '広島県',
            '35' => '山口県',
            '36' => '徳島県',
            '37' => '香川県',
            '38' => '愛媛県',
            '39' => '高知県',
            '40' => '福岡県',
            '41' => '佐賀県',
            '42' => '長崎県',
            '43' => '熊本県',
            '44' => '大分県',
            '42' => '長崎県',
            '43' => '熊本県',
            '44' => '大分県',
            '45' => '宮崎県',
            '46' => '鹿児島県',
            '47' => '沖縄県',

        ];

        foreach ($dataList as $data) {

            $name = $data->name;
            $name = mb_convert_kana($name, 'K', 'utf-8');

            $name = str_replace('ハ゛', 'バ', $name);
            $name = str_replace('ヒ゛', 'ビ', $name);
            $name = str_replace('フ゛', 'ブ', $name);
            $name = str_replace('ヘ゛', 'ベ', $name);
            $name = str_replace('ホ゛', 'ボ', $name);

            $name = str_replace('カ゛', 'ガ', $name);
            $name = str_replace('キ゛', 'ギ', $name);
            $name = str_replace('ク゛', 'グ', $name);
            $name = str_replace('ケ゛', 'ゲ', $name);
            $name = str_replace('コ゛', 'ゴ', $name);

            $name = str_replace('サ゛', 'ザ', $name);
            $name = str_replace('シ゛', 'ジ', $name);
            $name = str_replace('ス゛', 'ズ', $name);
            $name = str_replace('セ゛', 'ゼ', $name);
            $name = str_replace('ソ゛', 'ゾ', $name);

            $name = str_replace('タ゛', 'ダ', $name);
            $name = str_replace('チ゛', 'ヂ', $name);
            $name = str_replace('ツ゛', 'ヅ', $name);
            $name = str_replace('テ゛', 'デ', $name);
            $name = str_replace('ト゛', 'ド', $name);

            $name = str_replace('ハ゜', 'パ', $name);
            $name = str_replace('ヒ゜', 'ピ', $name);
            $name = str_replace('フ゜', 'プ', $name);
            $name = str_replace('ヘ゜', 'ペ', $name);
            $name = str_replace('ホ゜', 'ポ', $name);

            $kana = $data->kana_han;
            $kana = mb_convert_kana($kana, 'K', 'utf-8');

            $kana = str_replace('ハ゛', 'バ', $kana);
            $kana = str_replace('ヒ゛', 'ビ', $kana);
            $kana = str_replace('フ゛', 'ブ', $kana);
            $kana = str_replace('ヘ゛', 'ベ', $kana);
            $kana = str_replace('ホ゛', 'ボ', $kana);

            $kana = str_replace('カ゛', 'ガ', $kana);
            $kana = str_replace('キ゛', 'ギ', $kana);
            $kana = str_replace('ク゛', 'グ', $kana);
            $kana = str_replace('ケ゛', 'ゲ', $kana);
            $kana = str_replace('コ゛', 'ゴ', $kana);

            $kana = str_replace('サ゛', 'ザ', $kana);
            $kana = str_replace('シ゛', 'ジ', $kana);
            $kana = str_replace('ス゛', 'ズ', $kana);
            $kana = str_replace('セ゛', 'ゼ', $kana);
            $kana = str_replace('ソ゛', 'ゾ', $kana);

            $kana = str_replace('タ゛', 'ダ', $kana);
            $kana = str_replace('チ゛', 'ヂ', $kana);
            $kana = str_replace('ツ゛', 'ヅ', $kana);
            $kana = str_replace('テ゛', 'デ', $kana);
            $kana = str_replace('ト゛', 'ド', $kana);

            $kana = str_replace('ハ゜', 'パ', $kana);
            $kana = str_replace('ヒ゜', 'ピ', $kana);
            $kana = str_replace('フ゜', 'プ', $kana);
            $kana = str_replace('ヘ゜', 'ペ', $kana);
            $kana = str_replace('ホ゜', 'ポ', $kana);


            $zip = $data->zip;
            $zip = str_replace('-', '', $zip);


            $ken = $kenList[$data->ken_id];

            $address = $data->address1;
            $address = str_replace($ken, '', $address);
            $address .= $data->address2;


            DB::table('demo_form')->where('id', $data->id)->update([
                'name_h' => $name,
                'kana' => $kana,
                'zip_format' => $zip,
                'ken' => $ken,
                'address' => $address,
            ]);
        }
    }


    public function store()
    {
        $dataList = DB::table('demo_form')->get();

        foreach ($dataList as $data) {

            $duplication = DB::table('clients')->where([
                // 'client_type_id' => 'taxibus',
                'name' => $data->name_h,
            ])->get();



            if (!($duplication->isEmpty())) {
                DB::table('duplication')->insertGetId([
                    'name' => $data->name,
                ]);
                continue;
            }

            $client_id = DB::table('clients')->insertGetId([
                'client_type_id' => 'taxibus',
                'name' => $data->name_h,
                'postcode' => $data->zip_format,
                'prefecture' => $data->ken,
                'lat' => NULL,
                'lng' => NULL,
                'tel' => $data->tel,
                'fax' => $data->fax,
                'address' => $data->address,
                'name_kana' => $data->kana,
                'created_at' => date('Y-m-d G:i:s'),
                'updated_at' => date('Y-m-d G:i:s'),
            ]);

            $type_id = DB::table('client_type_taxibus')->insertGetId([
                'client_id' => $client_id,
                'category' => 'taxibus',
                'fee_taxi_cab' => $data->rate,
                'fee_bus_cab' => $data->rate,
                'created_at' => date('Y-m-d G:i:s'),
                'updated_at' => date('Y-m-d G:i:s'),

            ]);

            // dd($client_id);
        }
    }
}
