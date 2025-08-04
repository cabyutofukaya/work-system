<?php

namespace App\Http\Controllers;

use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        return inertia('Home');
    }
}
