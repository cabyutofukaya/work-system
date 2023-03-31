<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;

class AuthController extends BaseAuthController
{
    /**
     * 管理者ログインを行うと利用者画面にリダイレクトする場合がある不具合への対応
     *
     * 利用者画面を閲覧するとurl.intendedが設定されるが続けて管理画面ログインを行うとセッションを共有しているため設定された利用者URLへリダイレクトされる
     * url.intendedはwebガードでのみ設定されておりadminガードでは使われていない事前に削除することで不具合を回避する
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function postLogin(Request $request): mixed
    {
        $request->session()->remove('url.intended');

        return parent::postLogin($request);
    }
}
