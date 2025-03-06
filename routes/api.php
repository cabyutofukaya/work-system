<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('schedule/store', [ScheduleController::class, "store"])->name('api.schedule.store');

Route::put('schedule/update', [ScheduleController::class, "update"])->name('api.schedule.update');


// スケジュールイベント取得
Route::get('schedule/event/{user_id}', [ScheduleController::class, "event_data"]);

// スケジュールイベント一件取得
Route::get('schedule/select_data/{schedule}', [ScheduleController::class, "select_data"]);


// 施設予約状況確認
Route::post('bookings/check', [BookingController::class, "check"]);

Route::post('bookings/store', [BookingController::class, "store"]);

Route::post('bookings/update', [BookingController::class, "update"]);

Route::get('bookings/events/{room_id}', [BookingController::class, "get_events"]);

Route::post('schedule/booking/delete', [ScheduleController::class, "delete_booking"]);

Route::post('schedule/create_zoom_url', [ScheduleController::class, "create_zoom_url"]);


Route::post('schedule/delete_zoom_url', [ScheduleController::class, "delete_zoom_url"]);

Route::post('schedule/delete', [ScheduleController::class, "delete"]);

Route::post('schedule/finish', [ScheduleController::class, "finish"]);

Route::post('mail/send', [MailController::class, "send"]);
