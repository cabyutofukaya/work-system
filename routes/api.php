<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CalendarController;

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

Route::prefix('calendar')->group(function () {
    // スケジュール情報取得
    Route::get('/', [CalendarController::class, "index"]);
    // スケジュール情報登録
    Route::post('/', [CalendarController::class, 'store']);
    // スケジュール情報更新
    Route::put('/{id}', [CalendarController::class, 'update']);
    // スケジュール情報削除
    Route::delete('/{id}', [CalendarController::class, 'destroy']);
});
