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
use App\Mail\ScheduleMail;
use App\Mail\TodoAlertMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSchedule  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $data = $request->all();

        $schedule = Schedule::find($data['schedule_id']);

        $param = [];
  
        $param['title'] = $data['title'];
        $param['message_text'] = $data['message'];
        $param['schedule'] = $schedule;
        $param['booking'] = [];

        $email = [];
        foreach($data['users'] as $user_id){
            $user = User::find($user_id);
            if($user->email){
                array_push($email,$user->email);
            }
        }

        $param['email'] = $email;

        $user = User::find($schedule->user_id);
        $param['my_email'] = $user->email;


        $schedule_booking = ScheduleBooking::where('schedule_id',$data['schedule_id'])->first();
        if($schedule_booking){
            $bookings = Booking::with(['room']);
            $param['booking'] = $bookings->find($schedule_booking->booking_id);
        }

  

        Mail::send(new ScheduleMail($param));

    }

}
