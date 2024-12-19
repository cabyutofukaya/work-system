<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    // ホーム
    $router->get('/', 'HomeController@index')->name('admin.home');

    // スーパーユーザによる管理ユーザ編集機能
    // Admin::routes のオーバーライド
    $router->resource('/auth/users', 'AdminAuthUserController', ['as' => 'auth']);

    // スーパーユーザダッシュボード
    $router->get('dashboard', 'AdminDashboardController@index')->name('dashboard.index');

    // 管理ユーザ
    $router->resource('admin-users', 'AdminUserController');

    // 一般ユーザ
    $router->resource('users', 'UserController');

    // 営業ToDoリスト
    $router->resource('sales-todos', 'SalesTodoController');

    // 社内ToDoリスト
    $router->resource('office-todos', 'OfficeTodoController');

    // 会社 タクシー・バス会社
    $router->resource('clients-taxibus', 'ClientTaxibusController')->parameters([
        'clients-taxibus' => 'client'
    ]);

    // 会社 トラック会社
    $router->resource('clients-truck', 'ClientTruckController')->parameters([
        'clients-truck' => 'client'
    ]);

    // 会社 飲食店
    $router->resource('clients-restaurant', 'ClientRestaurantController')->parameters([
        'clients-restaurant' => 'client'
    ]);

    // 会社 旅行業者など
    $router->resource('clients-travel', 'ClientTravelController')->parameters([
        'clients-travel' => 'client'
    ]);

    // 営業所
    $router->resource('branches', 'BranchController');

    // 相手先担当者
    $router->resource('contact-persons', 'ContactPersonController');

    // 営業エリア
    $router->resource('business-districts', 'BusinessDistrictController');

    // 保有車両 タクシー
    $router->resource('vehicles-taxi', 'VehicleController')->parameters([
        'vehicles-taxi' => 'vehicle'
    ]);

    // 保有車両 バス
    $router->resource('vehicles-bus', 'VehicleController')->parameters([
        'vehicles-bus' => 'vehicle'
    ]);

    // 日報
    $router->resource('reports', 'ReportController');

    // 営業日報
    $router->resource('report-contents-sales', 'ReportContentSalesController')->parameters([
        'report-contents-sales' => 'report-content'
    ]);

    // 業務日報
    $router->resource('report-contents-work', 'ReportContentWorkController')->parameters([
        'report-contents-work' => 'report-content'
    ]);

    // 日報コメント
    $router->resource('report-comments', 'ReportCommentsController');

    // 議事録
    $router->resource('meetings', 'MeetingController');

    // 議事録コメント
    $router->resource('meeting-comments', 'MeetingCommentsController');

    // お知らせ
    $router->resource('notices', 'NoticeController');

    // 日報閲覧状況
    $router->get('report-visitors', 'ReportVisitorController@index');

    // 日報閲覧状況詳細
    $router->get('report-visitors/{user}', 'ReportVisitorController@detail');

    // 日報閲覧状況詳細
    $router->get('report-visitors/show/{user}', 'ReportVisitorController@all_detail');


    // 教材
    $router->resource('products', 'ProductController');
});

Route::group([
    'prefix'        => config('admin.route.prefix') . "/api",
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.api.',
], function (Router $router) {
    // 営業所セレクタ カスケード選択API
    $router->get('branches', 'ApiController@branches')->name('branches.index');
});
