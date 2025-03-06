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
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        $data = $request->all();

        Log::debug($data);

        $flg = false;

        try {

            $bookings = Booking::where([
                'room_id' => $data['room_id'],
                'started_at' => $data['started_at'],
            ])->get();


            if ($data['all_day']) {
                //終日の場合

                if (count($bookings) > 0) {
                    $flg = true;
                }

            } else {
                //時間の場合

                $started_at = $data['started_at'] . ' ' . $data['started_time'];
                $ended_at = $data['started_at'] . ' ' . $data['end_time'];


                foreach ($bookings as $booking) {

                    $tmp_started_at = date('Y-m-d H:i', strtotime($booking->started_at . ' ' . $booking->started_time));
                    $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $booking->time . 'minutes'));

                    if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                        //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                        if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                            $flg = true;
                        }
                    } else {


                        //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                        if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                            $flg = true;
                        }
                    }
                }
            }

            Log::debug($flg);

            if($flg){
                return response(false);
            }else{
                return response(true);
            }

      
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
    public function store(Request $request)
    {
        $data = $request->all();
        Log::debug($data);

        $flg = false;
        $err_message = "";

        if (!$data['all_day']) {
            $started_at = $data['started_at'] . ' ' . $data['started_time'];
            $ended_at = date('Y-m-d H:i', strtotime($started_at . '+' . $data['time'] . 'minutes'));

            $ended_date = date('Y-m-d', strtotime($ended_at));
        }

        try {

            $bookings = Booking::where([
                'room_id' => $data['room_id'],
                'started_at' => $data['started_at'],
            ])->get();


            if ($data['all_day']) {
                //終日の場合

                if (count($bookings) > 0) {
                    $flg = true;
                    $err_message = "予約が被っているため、登録できません";
                }

            } else {
                //時間の場合

                foreach ($bookings as $booking) {

                    $tmp_started_at = date('Y-m-d H:i', strtotime($booking->started_at . ' ' . $booking->started_time));
                    $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $booking->time . 'minutes'));

                    if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                        //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                        if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                            $flg = true;
                            $err_message = "予約が被っているため、登録できません";
                        }
                    } else {


                        //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                        if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                            $flg = true;
                            $err_message = "予約が被っているため、登録できません";
                        }
                    }
                }
            }

            //予約被りチェック
            if ($flg) {
                return response()->json([
                    'flg' => false,
                    'errMessage' => $err_message,
                ], 200);
            }


            if ($data['all_day']) {
                $data['started_time'] = '07:00';
                $data['time'] = 1040;
            }

            // Log::debug($data);
            Log::debug($data);

            Booking::create([
                'user_id' => $data['user']['id'],
                'started_at' => $data['started_at'],
                'started_time' => $data['started_time'],
                'time' => $data['time'],
                'room_id' => $data['room_id'],
                'title' => $data['title'] ?? null,
                'all_day' => $data['all_day'] ? 1 : 0,
            ]);


            return response()->json([
                'flg' => true,
                'errMessage' => $err_message,
            ], 200);
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
    public function update(Request $request)
    {
        $data = $request->all();
        Log::debug($data);

        $flg = false;
        $err_message = "";

        if (!$data['all_day']) {
            $started_at = $data['started_at'] . ' ' . $data['started_time'];
            $ended_at = date('Y-m-d H:i', strtotime($started_at . '+' . $data['time'] . 'minutes'));

            $ended_date = date('Y-m-d', strtotime($ended_at));
        }

        try {

            $bookings = Booking::where([
                'room_id' => $data['room_id'],
                'started_at' => $data['started_at'],
            ])->get();


            if ($data['all_day']) {
                //終日の場合

                foreach ($bookings as $booking) {
                    if ($booking->id == $data['id']) {
                        continue;
                    }
                    $flg = true;
                    $err_message = "予約が被っているため、登録できません";
                }
            } else {
                //時間の場合

                foreach ($bookings as $booking) {

                    if ($booking->id == $data['id']) {
                        continue;
                    }

                    $tmp_started_at = date('Y-m-d H:i', strtotime($booking->started_at . ' ' . $booking->started_time));
                    $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $booking->time . 'minutes'));

                    if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                        //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                        if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                            $flg = true;
                            $err_message = "予約が被っているため、登録できません";
                        }
                    } else {


                        //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                        if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                            $flg = true;
                            $err_message = "予約が被っているため、登録できません";
                        }
                    }
                }
            }

            //予約被りチェック
            if ($flg) {
                return response()->json([
                    'flg' => false,
                    'errMessage' => $err_message,
                ], 200);
            }


            if ($data['all_day']) {
                $data['started_time'] = '07:00';
                $data['time'] = 1040;
            }

            // Log::debug($data);
            Log::debug($data);

            Booking::where('id', $data['id'])
                ->update([
                    'user_id' => $data['user']['id'],
                    'started_at' => $data['started_at'],
                    'started_time' => $data['started_time'],
                    'time' => $data['time'],
                    'room_id' => $data['room_id'],
                    'title' => $data['title'] ?? null,
                    'all_day' => $data['all_day'] ? 1 : 0,
                ]);


            return response()->json([
                'flg' => true,
                'errMessage' => $err_message,
            ], 200);
        } catch (\Exception $e) {
            return response($e);
        }
    }

    public function get_events(int $room_id)
    {
        if ($room_id != 0) {
            //会議種類選択の場合

            $last_month = date('Y-m-d', strtotime('-1 month'));
            $bookings = Booking::with(['user:id,name,deleted_at', 'room']);
            $bookings_list = $bookings
                ->where('started_at', '>=', $last_month)
                ->where('room_id', '=', $room_id)
                ->get();
        } else {
            //全体の場合
            $last_month = date('Y-m-d', strtotime('-1 month'));
            $bookings = Booking::with(['user:id,name,deleted_at', 'room']);
            $bookings_list = $bookings
                ->where('started_at', '>=', $last_month)
                ->get();
        }


        $bookings = [];
        $n = 0;
        foreach ($bookings_list as $booking) {

            $tmp = [];
            $tmp['id'] = $booking->id;
            $tmp['user_id'] = $booking->user->id;

            $tmp['title'] = $booking->room->name;
            $tmp['title'] .= ' [' . $booking->user->name . ']';
            if ($booking->title != '') {
                $tmp['title'] .= ' / ' . $booking->title;
            }
            $tmp['color'] = $booking->room->color;
            $tmp['start'] = $booking->started_at . ' ' . $booking->started_time;
            // $tmp['start'] = $booking->started_at;

            $end_time = date('Y-m-d H:i', strtotime($tmp['start'] . '+' . $booking->time . 'minutes'));


            $tmp['end'] = $end_time;

            $bookings[$n] = $tmp;
            $n++;
        }

        return response($bookings);
    }
}
