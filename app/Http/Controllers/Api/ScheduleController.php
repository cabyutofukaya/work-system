<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchedule;
use App\Http\Requests\UpdateSchedule;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\OfficeTodo;
use App\Models\SalesTodo;
use App\Models\ScheduleBooking;
use App\Models\ScheduleGuest;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchedule $request)
    {
        $data = $request->all();

        Log::debug($data);

        //終日の場合
        if ($data['all_day']) {
            $start_date = date('Y-m-d', strtotime($data['date']));
            $end_date = date('Y-m-d', strtotime($data['end_date']));

            for ($i = $start_date; $i <= $end_date; $i = date('Y-m-d', strtotime($i . '+1 day'))) {
                $schedule = Schedule::create([
                    'date' => $i,
                    'all_day' => 1,
                    'content' => $data['content'] ?? null,
                    'is_public' => $data['is_public'] ?? 0,
                    'user_id' => $data['user']['id'],
                    'category' => $data['category'] ?? null,
                    'title' => $data['title'] ?? null,
                    'type' => $data['type'] ?? 1,
                    'zoom_url' => $data['zoomUrl'] ?? null,
                    'zoom_id' => $data['zoomUrlId'] ?? null,
                ]);
            }
        } else {

            Log::debug($data);

            $schedule = Schedule::create([
                'date' => $data['date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'all_day' => $data['all_day'] ?? 0,
                'content' => $data['content'] ?? null,
                'is_public' => $data['is_public'] ?? 0,
                'user_id' => $data['user']['id'],
                'category' => $data['category'] ?? null,
                'title' => $data['title'] ?? null,
                'type' => $data['type'] ?? 1,
                'zoom_url' => $data['zoomUrl'] ?? null,
                'zoom_id' => $data['zoomUrlId'] ?? null,
            ]);
        }

        try {

            // $schedule = Schedule::create([
            //     'date' => $data['date'],
            //     'start_time' => $data['start_time'] ?? null,
            //     'end_time' => $data['end_time'] ?? null,
            //     'all_day' => $data['all_day'] ?? 0,
            //     'content' => $data['content'] ?? null,
            //     'is_public' => $data['is_public'] ?? 0,
            //     'user_id' => $data['user']['id'],
            //     'category' => $data['category'] ?? null,
            //     'title' => $data['title'] ?? null,
            // ]);



            //施設予約がある場合
            if (isset($data['room']) && $data['room'] != 0) {

                if ($data['all_day']) {
                    $time = 1080;
                    $start_time = '07:00';
                } else {
                    // $time = $data['start_time'];
                    $start_time = $data['start_time'];

                    $started_at = $data['date'] . ' ' . $data['start_time'];
                    $ended_date = $data['date'] . ' ' . $data['end_time'];

                    $time = (strtotime($ended_date) - strtotime($started_at)) / 60;
                }


                $booking = Booking::create([
                    'room_id' => $data['room'],
                    'user_id' => $data['user']['id'],
                    'started_at' => $data['date'],
                    'title' => $data['title'] ?? null,
                    'time' => $time,
                    'started_time' => $start_time,
                ]);

                Log::debug($schedule);
                Log::debug($booking);

                ScheduleBooking::create([
                    'schedule_id' => $schedule->id,
                    'booking_id' => $booking->id,
                ]);
            }


            // ゲストがいる場合
            if ($data['guest']) {
                foreach ($data['guest'] as $geust) {
                    ScheduleGuest::create([
                        'schedule_id' => $schedule->id,
                        'user_id' => $geust,
                    ]);
                }
            }

            return response(true);
        } catch (\Exception $e) {
            return response($e);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSchedule $request)
    {
        $data = $request->all();

        Log::debug($data);


        try {
            $schedule = Schedule::where('id', $data['id'])->update([
                'date' => $data['date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'all_day' => $data['all_day'] ?? 0,
                'content' => $data['content'] ?? null,
                'is_public' => $data['is_public'] ?? 0,
                'user_id' => $data['user']['id'],
                'category' => $data['category'] ?? null,
                'title' => $data['title'] ?? null,
            ]);


            //施設予約がある場合
            if (isset($data['room']) && $data['room'] != 0) {

                if ($data['all_day']) {
                    $time = 1080;
                    $start_time = '07:00';
                } else {
                    // $time = $data['start_time'];
                    $start_time = $data['start_time'];

                    $started_at = $data['date'] . ' ' . $data['start_time'];
                    $ended_date = $data['date'] . ' ' . $data['end_time'];

                    $time = (strtotime($ended_date) - strtotime($started_at)) / 60;
                }


                $booking = Booking::create([
                    'room_id' => $data['room'],
                    'user_id' => $data['user']['id'],
                    'started_at' => $data['date'],
                    'title' => $data['title'] ?? null,
                    'time' => $time,
                    'started_time' => $start_time,
                ]);

                ScheduleBooking::create([
                    'schedule_id' => $data['id'],
                    'booking_id' => $booking->id,
                ]);
            }

            //一度ゲストリセット
            ScheduleGuest::where([
                'schedule_id' => $data['id'],
            ])->delete();

            // ゲストがいる場合
            if ($data['guest']) {
                foreach ($data['guest'] as $geust) {
                    ScheduleGuest::create([
                        'schedule_id' => $data['id'],
                        'user_id' => $geust,
                    ]);
                }
            }

            return response(true);
        } catch (\Exception $e) {
            return response($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function event_data($user_id)
    {
        // $user = Auth::user();
        // $schedule_list = DB::table('schedules')->where('date', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $schedule_list = Schedule::where('date', '>=', date('Y-m-d', strtotime('-1 months')))
            ->where('user_id', $user_id)
            ->whereNull('deleted_at')->get();

        // dd(auth()->id());

        $schedule = [];
        $k = 0;
        if ($schedule_list) {
            $backgroudcolor = config('schedule.backgroudColor');

            foreach ($schedule_list as $data) {

                $tmp = [];
                $tmp['title'] = '';
                $tmp['id'] = $data->id;

                if ($data->all_day == 1) {
                    $tmp['title'] = '';
                    $tmp['pops_time'] = '[終日]';
                } else {
                    $tmp['pops_time'] = $data->start_time . ' ~ ' . $data->end_time . "\n";
                }



                $foo['color'] = '#a1dce3';
                $foo['borderColor'] = '#a1dce3';
                if ($data->type == 1) {
                    if ($data->category != '') {
                        $tmp['title'] .= '[' . $data->category . '] ';
                        $tmp['color'] = $backgroudcolor[$data->category];
                        $tmp['borderColor'] = $backgroudcolor[$data->category];
                    }
                } else {
                    //タスクの場合
                    if ($data->is_finish == 1) {
                        $tmp['color'] = "#808080";
                        $tmp['borderColor'] = "#808080";
                    } else {
                        $tmp['color'] = "#64a5f5";
                        $tmp['borderColor'] = "#64a5f5";
                    }
                }

                $tmp['start'] = $data->date;

                if ($data->all_day == 0) {
                    $tmp['start'] .= ' ' . $data->start_time;
                    $tmp['end'] = $data->date . ' ' . $data->end_time;
                    $tmp['title'] = mb_substr($tmp['title'], 0, 14);
                }

                $tmp['title'] .= $data->title;

                $tmp['content'] = $data->content ?? '';

                $schedule[$k] = $tmp;
                $k++;
            }
        }


        // $sales_todo_list = DB::table('sales_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $sales_todo_list = SalesTodo::where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))
            ->where('user_id', $user_id)
            ->whereNull('deleted_at')->get();


        if ($sales_todo_list) {
            foreach ($sales_todo_list as $sales_todo) {

                // $tmp_clients = DB::table('clients')->where('id', $sales_todo->client_id)->first();
                $client_data = Client::find($sales_todo->client_id);

                $tmp = [];
                $tmp['id'] = $sales_todo->id;
                // $tmp['title'] =  '[営業]';
                $tmp['title'] =  '[営業]' . $client_data->name ?? '';
                $tmp['title'] = mb_substr($tmp['title'], 0, 14);
                $tmp['color'] = '#fc1814';
                $tmp['start'] = date('Y-m-d H:i', strtotime($sales_todo->scheduled_at));
                $tmp['content'] = $sales_todo->description;

                $tmp['pops_tile'] = '[営業]' . $client_data->name ?? '';
                $tmp['pops_time'] = date('G:i', strtotime($sales_todo->scheduled_at));

                // $tmp['url'] = '/sales-todos/' . $sales_todo->id  . '/edit';
                $tmp['class'] = 'office-todos';
                $schedule[$k] = $tmp;
                $k++;
            }
        }

        // $office_todo_list = DB::table('office_todos')->where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $office_todo_list = OfficeTodo::where('scheduled_at', '>=', date('Y-m-d', strtotime('-1 months')))
            ->where('user_id', $user_id)
            ->whereNull('deleted_at')->get();

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

        return response($schedule);
    }


    public function delete_booking(Request $request)
    {
        $data = $request->all();

        $booking = Booking::find($data['booking']['id']);
        $booking->delete();

        return response(true);
    }


    public function select_data(Schedule $schedule)
    {

        $schedule->load([
            'user',
        ]);

        $schedule_booking = ScheduleBooking::where('schedule_id', $schedule->id)->first();
        if ($schedule_booking) {
            $bookings = Booking::with(['room']);
            $booking = $bookings->find($schedule_booking->booking_id);
        }

        $guestList = [];
        $n = 0;
        $schedule_guests = ScheduleGuest::where('schedule_id', $schedule->id)->get();
        foreach ($schedule_guests as $guest) {
            $guestList[$n] = $guest->user_id;
            $n++;
        }


        $schedule_guests = ScheduleGuest::with(['user:id,name']);
        $users = $schedule_guests->where('schedule_id', $schedule->id)->get();


        return response()->json([
            'booking' => $booking ?? null,
            'schedule' => $schedule,
            'guest' => $guestList,
            'users' => $users,
        ], 200);
    }



    public function create_zoom_url(Request $request)
    {
        $data = $request->all();

        $access_token = $this->get_access_token();

        $start_date = date('Y-m-d', strtotime($data['start_date']));
        $start_time = date('H:i:s', strtotime($data['start_time'] ?? '09:00:00' . '-9 hour'));

        $start = $start_date . 'T' . $start_time . 'Z';

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.zoom.us/v2/users/QGQ2RYHPSIS14aUa_sA5PA/meetings', [
            'body' => '{
          "agenda": "オンラインミーティング",
          "duration": 60,
          "pre_schedule": false,
          "default_password":false,
          "start_time": "' . $start . '",
          "timezone": "Asia/Tokyo",
          "topic": "オンラインミーティング",
          "type": 2
        }',
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $responseParam = json_decode($response->getBody(), true);

        Log::debug($responseParam);

        return response()->json([
            'url' => $responseParam['join_url'],
            'id' => $responseParam['id'],
        ], 200);
    }


    public function delete_zoom_url(Request $request)
    {
        $data = $request->all();

        $access_token = $this->get_access_token();

        $zoom_id = $data['zoom_id'];
        Log::debug($data);

        $client = new \GuzzleHttp\Client();

        $response = $client->request('DELETE', 'https://api.zoom.us/v2/meetings/' . $zoom_id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
        ]);

        $responseParam = json_decode($response->getBody(), true);
        Log::debug($responseParam);

        return response(true);
    }

    public function delete(Request $request)
    {
        $data = $request->all();

        $schedule = Schedule::find($data['id']);

        $schedule_booking = ScheduleBooking::where('schedule_id', $schedule->id)->first();

        if ($schedule_booking) {
            $booking = Booking::find($schedule_booking->booking_id);
            if ($booking) {
                $booking->delete();
            }
            $schedule_booking->delete();
        }


        $schedule_guest = ScheduleGuest::where('schedule_id', $schedule->id);
        $schedule_guest->delete();

        //zoomが設定されている場合
        if($schedule->zoom_id != ''){
            $access_token = $this->get_access_token();

            $zoom_id = $schedule->zoom_id;
    
            $client = new \GuzzleHttp\Client();
    
            $response = $client->request('DELETE', 'https://api.zoom.us/v2/meetings/' . $zoom_id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                ],
            ]);

            $responseParam = json_decode($response->getBody(), true);
        }


        $schedule->delete();

        return response(true);
    }


    public function finish(Request $request)
    {
        $data = $request->all();

        // Log::debug($data['id']);
        // Log::debug($data['is_finish']);

        Schedule::where('id', $data['id'])->update([
            'is_finish' =>  $data['is_finish'] == 1 ? 0 : 1,
        ]);


        return response(true);
    }

    public function get_access_token()
    {
        $account_id = config('zoom.account_id');
        $client_id = config('zoom.client_id');
        $client_secret = config('zoom.client_secret');


        $base = base64_encode($client_id . ':' . $client_secret);


        $client = new \GuzzleHttp\Client();


        $response = $client->request('POST', 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . $account_id, [
            'headers' => [
                'Authorization' => 'Basic ' . $base,
            ],
        ]);

        $responseParam = json_decode($response->getBody(), true);
        $access_token = $responseParam['access_token'];

        return $access_token;
    }
}
