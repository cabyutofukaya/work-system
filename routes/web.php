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
use App\Http\Controllers\ReportFileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserScheduleController;
use App\Http\Controllers\NoticeFileController;
use App\Http\Controllers\LatestProductController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ScheduleListController;
use App\Http\Controllers\ContactPersonImageController;

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

Route::pattern('type', 'wsd|icts|bpd|tcd|lod');
Route::pattern('category', 'bking|goodlearning|truckking|hakken|links|jtbbokun|taxi|bus|cbl|tabinoashi_taxi|tabinoashi_bus|yakatafune|kashikiribus');
Route::pattern('year', '2014|2015|2016|2017|2018|2019|2020|2021|2022|2023|2024|2025');
Route::pattern('month', '1|2|3|4|5|6|7|8|9|10|11|12|101|102|200');


// ログイン

Route::group(['middleware' => 'basicauth'], function () {
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
        Route::resource('contact-persons', ContactPersonController::class)->only(["store", "update", "destroy","show"]);

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
        Route::post('/schedule/delete', [ScheduleController::class, 'delete'])->name("schedule.delete");

        // メンバー
        Route::get('schedule/getData/{scheduleId}', [ScheduleController::class, "getData"])->where('scheduleId', '[0-9]+');

        // 社内ToDo対応済みフラグトグル
        Route::put('reports/comment/{report_comment}/complete', [ReportCommentController::class, "complete"])->name('report-comments.complete');

        // 日報のファイル削除
        Route::post('reports/file/delete', [ReportFileController::class, "destroy"])->name('reports-file.delete');

        // 日報のファイル編集
        Route::get('reports/{report}/file/edit/', [ReportFileController::class, "edit"])->name('reports.file.edit');

        // 日報のファイル追加
        Route::post('reports/{report}/file/store', [ReportFileController::class, "store"])->name('reports.file.store');

        // 日報のファイル追加
        Route::post('reports/{report}', [ReportController::class, "update"])->name('reports.update');

        // 集計データの項目
        Route::get('sales', [SalesController::class, "index"]);

        // 会社情報作成選択画面
        Route::get('client-types/clients/create', [ClientController::class, "select"]);

        // メンバースケジュール一覧
        Route::get('/schedule/{department}/{user}', [UserScheduleController::class, "index"])->name('users.schedule.index');

        // 日報一覧取得	// メンバースケジュール一覧
        Route::get('/redirect/schedule/{department}', [UserScheduleController::class, "redirect_schedule"])->name('schedule.redirect');

        // メンバースケジュール一覧
        Route::get('/list/schedule/{department}', [ScheduleListController::class, "index"])->name('schedule.list.redirect');

        // 日報一覧取得	// メンバースケジュール一覧
        Route::get('/list/redirect/schedule/{department}', [ScheduleListController::class, "redirect_schedule"]);


        // 日報一覧取得
        Route::get('/api/reports', [ReportController::class, "api_data"]);

        // お知らせのファイル削除
        Route::post('notices/file/delete', [NoticeFileController::class, "destroy"])->name('notices-file.delete');

        // お知らせのファイル追加
        Route::post('notices/file/add', [NoticeFileController::class, "update"])->name('notices.update_file');

        // 商材の評価
        Route::resource('latest-product', LatestProductController::class)->only(["store", "update", "destroy"]);

        // 商材の評価
        Route::get('latest-product/select/{latest_product_id}', [LatestProductController::class, "select"]);
    });
});



// 請求管理年ごと(各種別ごとの表示)
Route::get('/sales', [SalesController::class, "index"])->name('sales.index');

// 請求管理年ごと(各種別ごとの表示)
Route::get('/sales/{type}', [SalesController::class, "type_index"])->name('sales.type');

// 各種別管理ごと
Route::get('/sales/billing/{category}/{year}', [SalesController::class, "category_index"]);

// 各種別管理ごと(年度毎)
Route::get('/sales/billing/{category}/{month}', [SalesController::class, "category_month"]);


// 請求データ各月情報取得
Route::get('/sales/{category}/{year}/{month}', [SalesController::class, "get_month_data"]);

// 請求データ各月情報更新
Route::post('/sales/update', [SalesController::class, "update_data"])->name('sales.update');

// 営業ToDo対応済みフラグトグル
Route::post('todos/is_read', [TodoController::class, "is_read"])->name('todos.is_read');


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


Route::domain('{account}.grouptube.local')->group(function () {

    Route::get('user/top', function ($account) {

        echo $account;
    });
});


// // 一括登録用(データ整え)
// Route::get('data/change', [UserScheduleController::class, "change"]);

// // 一括登録用(データ登録)
// Route::get('data/store', [UserScheduleController::class, "store"]);

// 一括登録用(データ登録)
// Route::get('/read/data', [App\Http\Controllers\ReadController::class, "index"]);


Route::post('/contact/person/image/upload', [ContactPersonImageController::class, "upload"]);

//写真追加
Route::post('/contact/person/image/store', [ContactPersonImageController::class, "store"])->name('contact-persons-image.store');

//写真追加
Route::delete('/contact/person/image/{contact_person_image}', [ContactPersonImageController::class, "destory"])->name('contact-persons-image.destory');

//写真追加
Route::post('/upload/meeting/file', [MeetingController::class, "upload"]);

//写真追加
Route::post('/upload/meeting/update', [MeetingController::class, "file_update"])->name('meetings.update_file');

//写真削除
Route::post('/upload/meeting/delete', [MeetingController::class, "file_delete"])->name('meetings-file.delete');