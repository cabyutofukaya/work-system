<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBooking;
use App\Http\Requests\UpdateBooking;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Support\Facades\Log;
use DateTime;

class BookingController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // ポリシーによる認可
        // $this->authorizeResource(Booking::class);
    }

    /**
     * 議事録一覧
     *
     * @param \App\Http\Requests\ShowBooking $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index()
    {

        $room_id = 0;

        if (isset($_GET['room'])) {
            //会議種類選択の場合

            $room_id = (int) $_GET['room'];

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

            // $started_at = $booking->started_at . ' ' . $booking->started_time;
            // $ended_at = date('Y-m-d H:i', strtotime($started_at . '+' . $booking->time . 'minutes'));


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

        // fn() => collect(config("const.client_types"))->values(),
        return inertia('Bookings', [
            'bookings' => $bookings,
            'rooms' => Room::get(),
            'room_id' => $room_id,
            'user' => Auth::user(),
            'roomType' => Room::get(['id', 'name']),
            'timeList' => fn() => collect(config("const.timeList"))->values(),
            // config('const.timeList'),
        ]);
        // $rooms = Room::first();

        // return redirect('/booking/' . $rooms->id);
    }


    public function show(Booking $booking)
    {
        $booking->load([
            'user'
        ]);

        return $booking;
    }

    /**
     * 施設予約作成
     *
     * @param \App\Http\Requests\StoreBooking $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */

    public function store(StoreBooking $request): RedirectResponse
    {

        $started_at = $request->started_at . ' ' . $request->started_time;
        $ended_at = date('Y-m-d H:i', strtotime($started_at . '+' . $request->time . 'minutes'));

        $ended_date = date('Y-m-d', strtotime($started_at . '+' . $request->time . 'minutes'));

        $booking_list = Booking::where([
            'started_at' => $request->started_at,
            'room_id' => $request->room_id,
        ])->get();

        $flg = true;
        if ($booking_list) {
            foreach ($booking_list as $data) {

                $tmp_started_at = date('Y-m-d H:i', strtotime($data->started_at . ' ' . $data->started_time));
                $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $data->time . 'minutes'));

                if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                    //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                    if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                        $flg = false;
                    }
                } else {


                    //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                    if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                        $flg = false;
                    }
                }
            }
        }

        //予約被りチェック
        if (!$flg) {
            $request->validate([
                'started_at' => function ($attribute, $value, $fail) {
                    $fail('予約がかぶっています。');
                },
            ]);
        }


        if ($request->started_at != $ended_date) {
            $request->validate([
                'time' => function ($attribute, $value, $fail) {
                    $fail('日を跨ぐ予約はできません。');
                },
            ]);
        }




        $booking = new Booking();

        // Log::debug($request->all());

        DB::transaction(function () use ($request, $booking) {
            // 施設予約情報を保存
            $booking->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();
        });


        return redirect()->back();
    }



    /**
     * 施設予約作成
     *
     * @param \App\Http\Requests\StoreBooking $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */

    public function update(UpdateBooking $request, Booking $booking): RedirectResponse
    {

        $started_at = $request->started_at . ' ' . $request->started_time;
        $ended_at = date('Y-m-d H:i', strtotime($started_at . '+' . $request->time . 'minutes'));

        $ended_date = date('Y-m-d', strtotime($started_at . '+' . $request->time . 'minutes'));

        $booking_list = Booking::where([
            'started_at' => $request->started_at,
            'room_id' => $request->room_id,
        ])->get();

        $flg = true;
        if ($booking_list) {
            foreach ($booking_list as $data) {

                if ($data->id == $request->id) {
                    continue;
                }

                $tmp_started_at = date('Y-m-d H:i', strtotime($data->started_at . ' ' . $data->started_time));
                $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $data->time . 'minutes'));

                if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                    //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                    if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                        $flg = false;
                    }
                } else {


                    //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                    if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                        $flg = false;
                    }
                }
            }
        }

        //予約被りチェック
        if (!$flg) {
            $request->validate([
                'started_at' => function ($attribute, $value, $fail) {
                    $fail('予約がかぶっています。');
                },
            ]);
        }


        if ($request->started_at != $ended_date) {
            $request->validate([
                'time' => function ($attribute, $value, $fail) {
                    $fail('日を跨ぐ予約はできません。');
                },
            ]);
        }



        DB::transaction(function () use ($request, $booking) {
            // 施設予約情報を更新
            $booking->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();
        });


        return redirect()->back();
    }

    /**
     * 削除
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->back();
    }

    public function select(Booking $booking)
    {
        //1ヶ月前以降のデータ取得
        $bookings = Booking::find($booking);

        return $bookings;
    }

    public function update_time(Request $request)
    {
        $started_at = date('Y-m-d H:i', strtotime($request->start_time));
        $ended_at = date('Y-m-d H:i', strtotime($request->end_time));
        $booking_id = $request->booking_id;

        $booking_data = Booking::find($booking_id);

        $started_date = date('Y-m-d', strtotime($started_at));
        $started_time = date('H:i', strtotime($started_at));
        $time = (strtotime($ended_at) - strtotime($started_at)) / 60;

        $ended_date = date('Y-m-d', strtotime($started_at . '+' . $time . 'minutes'));


        $booking_list = Booking::where([
            'started_at' => $started_date,
            'room_id' => $booking_data->room_id,
        ])->get();

        $flg = true;
        if ($booking_list) {
            foreach ($booking_list as $data) {

                if ($data->id == $booking_id) {
                    continue;
                }

                $tmp_started_at = date('Y-m-d H:i', strtotime($data->started_at . ' ' . $data->started_time));
                $tmp_ended_at = date('Y-m-d H:i', strtotime($tmp_started_at . '+' . $data->time . 'minutes'));

          
                if (strtotime($started_at) >= strtotime($tmp_started_at)) {

                    //開始時間が予約開始時間より前の場合、終了時間より開始時間が先
                    if (strtotime($tmp_ended_at) >  strtotime($started_at)) {
                        $flg = false;
                    }
                } else {


                    //開始時間が予約開始時間より後の場合、終了時間より開始時間が先
                    if (strtotime($ended_at) >  strtotime($tmp_started_at)) {
                        $flg = false;
                    }
                }


                // if((strtotime($tmp_started_at) <= strtotime($started_at)) && (strtotime($tmp_ended_at) >= strtotime($ended_at))){
                //     $flg = false;
                // }
            }
        }

        //予約被りチェック
        if (!$flg) {
            return response()->json(false, 200);
        }


        if($flg){
            Booking::where('id', $booking_id)->update([
                'started_at' => $started_date,
                'started_time' => $started_time,
                'time' => $time,
            ]);
        }
     

        return response()->json(true, 200);
    }
}
