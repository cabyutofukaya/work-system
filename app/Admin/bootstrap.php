<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Form;
use Encore\Admin\Grid;

Encore\Admin\Form::forget(['map', 'editor']);

// グリッドレイアウトの初期設定
Grid::init(function (Grid $grid) {

    // 作成ボタンを無効にする
    //$grid->disableCreateButton();

    // クエリフィルターを無効にする
    $grid->disableFilter();

    // データのエクスポートボタンを無効にする
    $grid->disableExport();

    // 行選択チェックボックスを無効にする
    $grid->disableRowSelector();

    // 行操作列を無効にする
    //$grid->disableActions();

    // 行セレクターを無効にする
    $grid->disableColumnSelector();

    // ページングバーを無効にする
    //$grid->disablePagination();

    // ページングセレクタオプションを設定する
    $grid->perPages([10, 20, 30, 50, 100, 1000, 10000]);

    // ページごとのデフォルトのアイテム数
    $grid->paginate(100);

    // グリッドのヘッダーにあるすべてのツールを無効にする
    // フィルター、更新、エクスポート、バッチアクション
    //$grid->disableTools();
});

// フォームレイアウトの初期設定
Form::init(function (Form $form) {
    $form->footer(function ($footer) {
        // リセットボタン無効化
        $footer->disableReset();

        // 「表示」チェックボックス無効化
        $footer->disableViewCheck();

        // 「編集を続ける」チェックボックス無効化
        $footer->disableEditingCheck();

        // 「作成を続ける」チェックボックス無効化
        $footer->disableCreatingCheck();
    });
});

// ナビゲーションのユーザ設定リンクを削除
Admin::style('.user-footer .pull-left {display:none;}');

// モバイルビューでのページ切替時にメインコンテンツエリアのボックスサイズが一時的に狭くなることを防止
Admin::style('#app {width:100%;}');

// モバイルビューでボタンの省略表示を行わない
Admin::script('$(".grid-create-btn .fa-plus").next("span").removeClass("hidden-xs");');
Admin::script('$(".fa-list").next("span").removeClass("hidden-xs");');

// "Powered by laravel-admin" 表記を非表示に
$script = <<<JS
    $("[href^='https://github.com/z-song/laravel-admin']").parent("strong").hide();
JS;
Admin::script($script);