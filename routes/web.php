<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;
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
    // スケジュール
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
});

Route::fallback(function () {
    return redirect()->route('login');
});
