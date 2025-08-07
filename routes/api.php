<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NoticeController;

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
Route::prefix('/notices-file')->group(function () {

    // 社内連絡事項の添付ファイル追加
    Route::post('/{notice}/file', [NoticeController::class, 'updateFile'])->name('api.notices.update_file');
    // 社内連絡事項の添付ファイル削除
    Route::post('/delete', [NoticeController::class, "destroy"])->name('notices-file.delete');
});



Route::prefix('schedules')->group(function () {
    // スケジュール情報取得
    Route::get('/', [ScheduleController::class, "index"]);
    // スケジュール情報登録
    Route::post('/', [ScheduleController::class, 'store']);
    // スケジュール情報更新
    Route::put('/{id}', [ScheduleController::class, 'update']);
   // スケジュール情報削除
   Route::delete('/{id}', [ScheduleController::class, 'destroy']);
});
