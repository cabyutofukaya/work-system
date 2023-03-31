<?php

namespace App\Admin\Controllers;

/**
 * スーパーユーザによる管理ユーザ編集機能クラスのオーバーライド
 * @package App\Admin\Controllers
 */
class AdminAuthUserController extends \Encore\Admin\Controllers\UserController
{
    public function grid()
    {
        $grid = parent::grid();

        // グリッドレイアウトの初期設定で無効にとなっている機能を有効化
        $grid->disableFilter(false);
        $grid->disableExport(false);
        $grid->disableRowSelector(false);
        $grid->disableColumnSelector(false);

        return $grid;
    }
}