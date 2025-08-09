<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function __invoke(Request $request): Response|ResponseFactory|RedirectResponse
    {
        // ログイン済みであればホームに移動
        return Auth::guard('web')->check() ? redirect()->route('calendar.index') : inertia('Login');
    }
}
