<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index($user)
    {

        dd($user);

        return view('welcome');
    }
}
