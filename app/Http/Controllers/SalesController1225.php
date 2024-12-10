<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotice;
use App\Http\Requests\UpdateNotice;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class SalesController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Notice::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
     
        return inertia('Sales', [
            // 'notices' => $notices,
        ]);
    }
  
}
