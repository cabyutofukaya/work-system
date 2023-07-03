<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Meeting;
use App\Models\OfficeTodo;
use App\Models\Report;
use App\Models\SalesTodo;
use App\Models\User;
use App\Models\ReportComment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function index(): Response|ResponseFactory
    {

       
        // $meeting = Meeting::with(['user:id,name,deleted_at'])
        //     ->withCount([
        //         'meeting_likes',
        //         'meeting_comments',
        //     ])
        //     ->withExists([
        //         'meeting_visitors as is_visited' => function ($query) {
        //             $query->where('user_id', auth()->id());
        //         }
        //     ])
        //     ->take(3)->get();

        // dd($meeting->where('is_visited',1));

        return Inertia::render('Home', [
            // 新着お知らせ
            'notices' => fn () => Notice::with(['user:id,name'])->take(3)->get(),

            // 議事録お知らせ
            // 'meetings' => fn() => Meeting::with(['user:id,name'])->take(3)->get(),
            'meetings' => fn () => Meeting::with(['user:id,name,deleted_at'])
                ->withCount([
                    'meeting_likes',
                    'meeting_comments',
                ])
                //  自分自身が閲覧済みかどうか
                ->withExists([
                    'meeting_visitors as is_visited' => function ($query) {
                        $query->where('user_id', auth()->id());
                    }
                ])
                ->take(3)->get()->where('is_visited', 0),


            // 直近の営業ToDo
            'sales_todos' => fn () => SalesTodo::with(['client:id,name,image', 'sales_todo_participants.user:id,name'])
                // 自分のToDoに絞る
                ->where('user_id', Auth::id())
                ->take(3)->get(),

            // 直近の社内ToDo
            'office_todos' => fn () => OfficeTodo::with(['office_todo_participants.user:id,name'])
                // 自分のToDoに絞る
                ->where('user_id', Auth::id())
                ->take(3)->get(),

            // 最近の日報
            'reports' => fn () => Report
                ::exceptPrivate()
                ->with(['user:id,name'])
                ->withExists([
                    'report_contents_sales',
                    'report_contents_work',
                    'report_comments as is_readed' => function ($query) {
                        $query->where('mention_id', auth()->id());
                        $query->where('is_readed',0);
                    }
                ])
                ->withCount([
                    'report_content_likes',
                    'report_comments',
                ])
                ->take(3)->get(),

            // コメントがついた日報
            'reports_latest_comment' => fn () => Report
                ::exceptPrivate()
                ->with([
                    'latest_comment',
                    'user:id,name'
                ])
                ->withExists([
                    'report_contents_sales',
                    'report_contents_work',
                    'report_comments as is_readed' => function ($query) {
                        $query->where('mention_id', auth()->id());
                        $query->where('is_readed',0);
                    },
                ])
                ->withCount([
                    'report_content_likes',
                    'report_comments',
                ])
                ->whereNotNull('comment_updated_at')
                ->orderByDesc('comment_updated_at')
                ->take(3)
                ->get(),

            'user' => User::where('id',auth()->id())->first(),

            //日報コメント未読
            'is_read'=> fn () => count(ReportComment::where([
                'mention_id' => auth()->id(),
                'is_readed' => 0,
            ])->get()),

        ]);
    }
}
