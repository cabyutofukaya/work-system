<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusinessDistrictController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MeetingCommentController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\OfficeTodoController;
use App\Http\Controllers\ProductEvaluationController;
use App\Http\Controllers\ReportCommentController;
use App\Http\Controllers\ReportContentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesTodoController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use Intervention\Image\ImageCacheController;
use App\Http\Controllers\ScheduleController;

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

    // 営業ToDo作成・編集 会社一覧の非同期更新時にpostでアクセスする
    Route::match(['get', 'post'], 'sales-todos/create', [SalesTodoController::class, "create"])->name('sales-todos.create');
    Route::match(['get', 'post'], 'sales-todos/{sales_todo}/edit', [SalesTodoController::class, "edit"])->name('sales-todos.edit');

    // 営業ToDoリスト
    Route::resource('sales-todos', SalesTodoController::class)->except(["create", "edit"]);;

    // 営業ToDo対応済みフラグトグル
    Route::put('sales-todos/{sales_todo}/complete', [SalesTodoController::class, "complete"])->name('sales-todos.complete');

    // 社内ToDoリスト
    Route::resource('office-todos', OfficeTodoController::class);

    // 社内ToDo対応済みフラグトグル
    Route::put('office-todos/{office_todo}/complete', [OfficeTodoController::class, "complete"])->name('office-todos.complete');

    // 会社
    Route::resource('client-types.clients', ClientController::class)->shallow();

    // 会社マップ検索
    Route::get('client-types/{client_type}/clients/map', [ClientController::class, "map"])->name('client-types.clients.map');

    // 担当者
    Route::resource('contact-persons', ContactPersonController::class)->only(["store", "update", "destroy"]);

    // 営業所
    Route::resource('clients.branches', BranchController::class)->only(["create", "store", "edit", "update", "destroy"])->shallow();

    // 保有車両
    Route::resource('clients.vehicles', VehicleController::class)->except(["index"])->shallow();

    // 営業エリア
    Route::resource('business-districts', BusinessDistrictController::class)->only(["store", "update", "destroy"]);

    // 商材の評価
    // 期間指定時にpostでアクセスする
    Route::match(['get', 'post'], 'product-evaluations', ProductEvaluationController::class)->name('product-evaluations');

    // 日報作成・編集 会社一覧の非同期更新時にpostでアクセスする
    Route::match(['get', 'post'], 'reports/create', [ReportController::class, "create"])->name('reports.create');
    Route::match(['get', 'post'], 'reports/{report}/edit', [ReportController::class, "edit"])->name('reports.edit');

    // 自分の日報
    Route::get('reports/mine', [ReportController::class, "mine"])->name('reports.mine');

    // 日報
    Route::resource('reports', ReportController::class)->except(["create", "edit"]);

    // 日報コメント
    Route::resource('report-comments', ReportCommentController::class)->only(["store", "destroy"]);

    // 日報コンテンツいいね
    Route::put('report-contents/{report_content}/likes', [ReportContentController::class, "like"])->name('report-contents.like');

    // 自分の日報
    Route::get('meetings/mine', [MeetingController::class, "mine"])->name('meetings.mine');

    // 議事録
    Route::resource('meetings', MeetingController::class);

    // 議事録いいね
    Route::put('meetings/{meeting}/likes', [MeetingController::class, "like"])->name('meetings.like');

    // 日報コメント
    Route::resource('meeting-comments', MeetingCommentController::class)->only(["store", "destroy"]);

    // お知らせ
    Route::resource('notices', NoticeController::class)->except(["create", "edit"]);

    // メンバー
    Route::resource('users', UserController::class)->only(["index", "show", "edit"]);

    // メンバー情報編集
    Route::get('user/profile-information', [UserProfileController::class, "edit"])->name("user-profile-information.edit");

    // 書類
    Route::inertia('/documents', "Documents")->name("documents");

    // スケジュール
    Route::resource('schedule', ScheduleController::class);

    // スケジュール
    Route::post('/schedule/delete', [ScheduleController::class,'delete'])->name("schedule.delete");

    // メンバー
    Route::get('schedule/getData/{scheduleId}', [ScheduleController::class, "getData"])->where('scheduleId', '[0-9]+');

    // 社内ToDo対応済みフラグトグル
    Route::put('reports/comment/{report_comment}/complete', [ReportCommentController::class, "complete"])->name('report-comments.complete');
});

// ファイルダウンロード
// 非ログイン時にログインページへのリダイレクトを防ぐためユーザ認証はコントローラ内で行う
Route::get('static/{path?}', StaticController::class)->where(['path' => '.*'])->name("static");

Route::group(['middleware' => 'auth:web,admin'], function () {
    // intervention/imageによる画像出力
    // 認証を有効にするためモジュール組み込みのルーティングを使用しない
    Route::get('img/cache/{template}/{filename}', [ImageCacheController::class, "getResponse"])
        ->where('filename', '.*')
        ->middleware(['json.response'])
        ->name('img.cache');
});

// Sentryのエラーを発生させるテストページ
Route::inertia('/sentry-test-send', "SentryTestSend")->name("sentry-test-send");


Route::domain('{account}.grouptube.local')->group(function(){

    Route::get('user/top', function($account){

        echo $account;

    });

});