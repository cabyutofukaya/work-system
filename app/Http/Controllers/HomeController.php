<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Meeting;
use App\Models\OfficeTodo;
use App\Models\Report;
use App\Models\SalesTodo;
use App\Models\User;
use App\Models\ReportContent;
use App\Models\ReportComment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;
use App\Common\TodoCheck;
use App\Models\Client;
use App\Models\ReportVisitor;

ini_set("max_execution_time", 0);

class HomeController extends Controller
{
    public function index(): Response|ResponseFactory
    {

        // $reports = Report::orderBy('id')->get();

        // foreach ($reports as $report) {

        //     $contents = ReportContent::where('report_id', $report->id)->get();

        //     foreach ($contents as $content) {

        //         if ($content->sales_method_id && $content->sales_method_id == 8) {

        //             ReportContent::where('id',$content->id)->update([
        //                 'sales_method_id' => 100,
        //             ]);


        //             // //日報に紐づく顧客を保存
        //             // $clients = Client::find($content->client_id);


        //             // $clients->client_report_user()->updateOrCreate([
        //             //     'user_id' => $report->user_id,
        //             // ]);
        //         }
        //     }
        // }

        // dd('uu');



        // $type_list = config('const.client_list');

        // foreach ($clients as $client) {
        // $name = $client->name;
        // $name_2 = '';
        // $name_position = '';

        // foreach ($type_list as $type) {
        //     if (preg_match('/' . $type .'/', $name)) {

        //         $num = mb_strpos($name, $type);

        //         if($num == 0){
        //             $name_position = '前';
        //         }else{
        //             $name_position = '後ろ';
        //         }

        //         //残り文字
        //         $name_2 = str_replace($type, '', $name);

        //         Client::where('id',$client->id)->update([
        //             'name' => $name_2,
        //             'name_2' => $name,
        //             'name_position' => $name_position,
        //             'type_name' => $type,
        //         ]);

        //     }
        // }

        // }

        // dd('uu');

        // $three_month = date('Y-m-d', strtotime('-3 month'));

        // $reports = Report::where([
        //     'draft_flg' => 0,
        //     'is_private' => 0,
        // ])->where('date', '>=', $three_month)
        //     ->get();

        // $report_count = count($reports);

        // $user_id = Auth::user()->id;


        // $visitor_count = 0;
        // foreach ($reports as $report) {
        //     $report_visitor = ReportVisitor::where('report_id', $report->id)->where('user_id', $user_id)->count();
        //     $visitor_count = $visitor_count + $report_visitor;
        // }

        // $visitor_rate = 0;
        // if ($visitor_count != 0) {
        //     $visitor_rate = round(($visitor_count / $report_count) * 100, 2);
        // }



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


        $is_read_meeting = false;

        // 自分自身が閲覧済みかどうか
        $meetings = Meeting::withExists([
            'meeting_visitors as is_visited' => function ($query) {
                $query->where('user_id', auth()->id());
            }
        ])->get();


        foreach ($meetings as $meeting) {
            if ($meeting['is_visited'] == 0) {
                $is_read_meeting = true;
                break;
            }
        }


        return inertia('Home', [
            // 新着お知らせ
            'notices' => fn() => Notice::with(['user:id,name'])->take(3)->get(),

            // 議事録お知らせ
            // 'meetings' => fn() => Meeting::with(['user:id,name'])->take(3)->get(),
            'meetings' => fn() => Meeting::with(['user:id,name,deleted_at'])
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
                ->exceptDraftFlg()
                ->with(['user:id,name'])
                ->withExists([
                    'report_contents_sales',
                    'report_contents_work',
                    'report_comments as is_readed' => function ($query) {
                        $query->where('mention_id', auth()->id());
                        $query->where('is_readed', 0);
                    }
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
                    'report_comments as is_readed' => function ($query) {
                        $query->where('mention_id', auth()->id());
                        $query->where('is_readed', 0);
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

            'user' => User::where('id', auth()->id())->first(),

            //日報コメント未読
            'is_read' => fn() => count(ReportComment::where([
                'mention_id' => auth()->id(),
                'is_readed' => 0,
            ])->get()),

            'todo_data' => TodoCheck::check(),

            'is_read_meeting' => $is_read_meeting,
        ]);
    }
}
