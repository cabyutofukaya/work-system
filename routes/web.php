<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NoticeFileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StaticController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// ログイン
Route::get('/login', LoginController::class)->name("login");

Route::group(['middleware' => 'auth'], function () {
    // ホーム
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // ToDoリスト
    Route::resource('todos', TodoController::class)->except(["create", "edit"]);;

    Route::patch('/todos/{todo}/toggle-done', [TodoController::class, 'toggleDone'])->name('todos.toggleDone');

    // 日報
    Route::resource('reports', ReportController::class)->except(["create", "edit"]);

    // 会議記録
    Route::resource('meetings', MeetingController::class);

    // 会議記録の日報
    Route::get('meetings/mine', [MeetingController::class, "mine"])->name('meetings.mine');

    // 社内連絡事項
    Route::resource('notices', NoticeController::class);

    // メンバー
    Route::resource('users', UserController::class)->only(["index", "show", "edit"]);

    // スケジュール
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
});
