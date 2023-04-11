<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\OfficeTodo;
use App\Models\Report;
use App\Models\SalesTodo;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        return Inertia::render('Home', [
            // 新着お知らせ
            'notices' => fn() => Notice::with(['user:id,name'])->take(3)->get(),

            // 直近の営業ToDo
            'sales_todos' => fn() => SalesTodo::with(['client:id,name,image', 'sales_todo_participants.user:id,name'])
                // 自分のToDoに絞る
                ->where('user_id', Auth::id())
                ->take(3)->get(),

            // 直近の社内ToDo
            'office_todos' => fn() => OfficeTodo::with(['office_todo_participants.user:id,name'])
                // 自分のToDoに絞る
                ->where('user_id', Auth::id())
                ->take(3)->get(),

            // 最近の日報
            'reports' => fn() => Report
                ::exceptPrivate()
                ->with(['user:id,name'])
                ->withExists([
                    'report_contents_sales',
                    'report_contents_work',
                ])
                ->withCount([
                    'report_content_likes',
                    'report_comments',
                ])
                ->take(3)->get(),

            // コメントがついた日報
            'reports_latest_comment' => fn() => Report
                ::exceptPrivate()
                ->with([
                    'latest_comment',
                    'user:id,name'
                ])
                ->withExists([
                    'report_contents_sales',
                    'report_contents_work',
                ])
                ->withCount([
                    'report_content_likes',
                    'report_comments',
                ])
                ->whereNotNull('comment_updated_at')
                ->orderByDesc('comment_updated_at')
                ->take(3)
                ->get(),
        ]);
    }
}
