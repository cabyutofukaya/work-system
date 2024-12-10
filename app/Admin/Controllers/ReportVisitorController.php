<?php

namespace App\Admin\Controllers;

use App\Models\ReportContent;
use App\Models\ReportComment;
use App\Models\User;
use App\Models\ReportVisitor;
use App\Models\ReportContentLike;
use App\Models\Report;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

ini_set('max_execution_time', '300');

/**
 * 日報コメント
 * @package App\Admin\Controllers
 */
class ReportVisitorController extends BaseController
{
    /**
     * パラメータで指定された日報IDに対応するモデル
     * @var \App\Models\Report
     */
    private Report $report;

    /**
     * Title for current resource.
     *
     * @var string
     */
    public function index(Content $content)
    {
        $users_list = User::get();

        $thress_month = date('Y-m-d', strtotime('-3 month'));
        $report_list = Report::where('is_private', 0)->where('draft_flg', 0)->get();

        //日報全体の数
        $report_count = count($report_list);

    

        $users = [];
        $n = 0;
        foreach ($users_list as $user) {

            if ($user->id == 108) {
                continue;
            }

            $tmp = [];
            $tmp['id'] = $user->id;
            $tmp['name'] = $user->name;

            //閲覧率
            $visitor_count = 0;
            $likes_give = 0;
            $three_visitor_count = 0;
            $three_report_count = 0;

            //既読率参考値
            $report_visitor_count = ReportVisitor::where([
                'user_id' =>  $user->id,
            ])->count();

            // foreach ($report_list as $report) {
            //     //閲覧したかどうかチェック
            //     $report_visitor_count = ReportVisitor::where([
            //         'report_id' =>  $report->id,
            //         'user_id' =>  $user->id,
            //     ])->count();

            //     $visitor_count = $visitor_count + $report_visitor_count;

            //     if ($report->date >= $thress_month) {
            //         $three_report_count++;
            //         $three_visitor_count = $three_visitor_count + $report_visitor_count;
            //     }
            // }

            //日報閲覧率計算
            $visitor_rate = 0;
            if ($report_visitor_count != 0) {
                $visitor_rate = ($report_visitor_count / $report_count) * 100;
            }

            $tmp['visitor_rate'] = round($visitor_rate, 2);

            //日報閲覧率計算
            // $three_visitor_rate = 0;
            // if ($three_visitor_count != 0) {
            //     $three_visitor_rate = ($three_visitor_count / $three_report_count) * 100;
            // }

            // $tmp['three_visitor_rate'] = round($three_visitor_rate, 2);



            //自分の日報情報
            // $my_report_data = Report::where([
            //     'user_id' => $user->id,
            //     'is_private' => 0,
            // ])->get();

            $likes_receive = 0;
            // foreach ($my_report_data as $my_report_data) {

            //     $report_content_data_list = ReportContent::where('report_id', $my_report_data->id)->get();
            //     foreach ($report_content_data_list as $report_content_data) {
            //         $report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->count();
            //         $likes_receive += $report_likes_count;
            //     }
            // }

            $tmp['likes_receive'] = $likes_receive;


            //いいね
            $tmp['likes_give'] = 0;

            //いいねした数
            $report_likes_count = ReportContentLike::where('user_id', $user->id)->count();
            $tmp['likes_give'] = $report_likes_count;


            $users[$n] = $tmp;
            $n++;
        }

        return $content
            ->title('日報閲覧状況')
            ->view('admin/report-visitor', compact('users'));
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    public function detail(Content $content, int $user)
    {

        $user_data = User::find($user);

        $report_list = Report::where('is_private', 0)->where('draft_flg', 0)->whereNull('deleted_at')->get();

        //日報全体
        $all_report_count = count($report_list);

        $all_visitor_rate = 0;
        $all_likes_receive = 0;
        $all_likes_give = 0;

        $all_visitor_count = 0;
        foreach ($report_list as $report) {

            //閲覧したかどうかチェック
            $report_visitor_count = ReportVisitor::where([
                'report_id' =>  $report->id,
                'user_id' =>  $user,
            ])->count();

            $all_visitor_count = $all_visitor_count + $report_visitor_count;
        }

        if ($all_visitor_count != 0) {
            $all_visitor_rate = round(($all_visitor_count / $all_report_count) * 100, 2);
        }


        //自分の日報情報
        $my_report_data = Report::where([
            'user_id' => $user,
            'is_private' => 0,
            'draft_flg' => 0
        ])->whereNull('deleted_at')->get();

        $all_my_report_count = count($my_report_data);

        //いいね受け取った数
        foreach ($my_report_data as $report_data) {
            $report_content_data_list = ReportContent::where('report_id', $report_data->id)->get();
            foreach ($report_content_data_list as $report_content_data) {
                $my_report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->count();
                $all_likes_receive += $my_report_likes_count;
            }
        }

        //いいねした数
        $report_likes_count = ReportContentLike::where('user_id', $user)->count();
        $all_likes_give = $report_likes_count;




        // $three_ago_month = date('Y-m-01', strtotime('-2 month'));


        // //月のループ
        // for ($month = $now_month; strtotime($month) >= strtotime($three_ago_month); $month = date('Y-m-01', strtotime($month . '-1 month'))) {
        //     $month_list[$month] = $month;
        // }

        // $month_list = [1,2,3,4,5,6,7,8,9,10,11,12];
        // 
        $now_month = (int) date('n');
        $last_month = $now_month - 1;
        if ($last_month == 0) {
            $last_month = 12;
        }
        $two_ago_month = $last_month - 1;
        if ($two_ago_month == 0) {
            $two_ago_month = 12;
        }

        $now_month = date('Y-m-15');
        $last_month = date('Y-m', strtotime($now_month . '-1 month'));
        $two_ago_month = date('Y-m', strtotime($now_month . '-2 month'));


        $month_list = [$now_month, $last_month, $two_ago_month];

        $thress_month_data = [];
        $thress_month_data['visitor_rate'] = 0;
        $thress_month_data['likes_give'] = 0;
        $thress_month_data['likes_receive'] = 0;
        $thress_month_data['report_count'] = 0;
        $thress_month_data['visitor_count'] = 0;

        $n = 0;
        foreach ($month_list as $month) {
            $tmp = [];
            $tmp['month'] = $month;

            $firstDate = date('Y-m-d', strtotime('first day of ' . $month));
            $lastDate = date('Y-m-d', strtotime('last day of ' . $month));

            //日報全体
            $report_list = Report::where([
                'is_private' => 0,
                'draft_flg' => 0,
            ])
                ->whereNull('deleted_at')
                ->where('date', '>=', $firstDate)->where('date', '<=', $lastDate)->get();

            $report_count = count($report_list);

            $visitor_rate = 0;
            $likes_give = 0;
            $likes_receive = 0;

            $my_report_count = 0;

            $visitor_count = 0;
            foreach ($report_list as $report) {

                //閲覧したかどうかチェック
                $report_visitor_count = ReportVisitor::where([
                    'report_id' =>  $report->id,
                    'user_id' =>  $user,
                ])->count();

                $visitor_count = $visitor_count + $report_visitor_count;

                //いいねした数
                $report_content_data_list = ReportContent::where('report_id', $report->id)->get();

                foreach ($report_content_data_list as $report_content_data) {
                    $report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->where('user_id', $user)->count();
                    $likes_give = $likes_give + $report_likes_count;
                }

                //いいね受け取り
                if ($report->user_id == $user) {

                    $my_report_count++;

                    $report_content_data_list = ReportContent::where('report_id', $report->id)->get();

                    foreach ($report_content_data_list as $report_content_data) {
                        $report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->count();
                        $likes_receive = $likes_receive + $report_likes_count;
                    }
                }
            }

            //既読率計算
            if ($visitor_count != 0) {
                $visitor_rate = round(($visitor_count / $report_count) * 100, 2);
            }

            $month_txt = date(('Y年m月'), strtotime($month));


            $tmp['month'] = $month_txt;
            $tmp['visitor_rate'] = $visitor_rate;
            $tmp['likes_receive'] = $likes_receive;
            $tmp['likes_give'] = $likes_give;
            $tmp['report_count'] = $report_count;
            $tmp['my_report_count'] = $my_report_count;

            $thress_month_data['likes_receive'] += $likes_receive;
            $thress_month_data['likes_give'] += $likes_give;
            $thress_month_data['report_count'] += $report_count;
            $thress_month_data['visitor_count'] += $visitor_count;

            $month_data[$n] = $tmp;
            $n++;
        }

        $thress_month_data['visitor_rate'] = '-';
        if ($thress_month_data['visitor_count'] != 0) {
            $thress_month_data['visitor_rate'] = round(($thress_month_data['visitor_count'] / $thress_month_data['report_count']) * 100, 2) . "%";
        }


        return $content
            ->title('日報閲覧状況詳細')
            ->view('admin/report-visitor-detail', compact(
                'all_visitor_rate',
                'all_likes_receive',
                'all_likes_give',
                'all_my_report_count',
                'all_report_count',
                'month_data',
                'user_data',
                'thress_month_data'
            ));
    }


    /**
     * Title for current resource.
     *
     * @var string
     */
    public function all_detail(Content $content, int $user)
    {

        $user_data = User::find($user);

        // $report_visitors = ReportVisitor::where('user_id', 9)->get();

        // var_dump(count($report_visitors));

        // foreach($report_visitors as $report_visitor){
        //     $report_data = Report::where('id', $report_visitor->report_id)->get();

        //     if(count($report_data) > 1){
        //         var_dump('count');
        //         var_dump($report_visitor->report_id);
        //         continue;
        //     }

        //     if(empty($report_data[0])){
        //         var_dump($report_visitor->report_id);
        //         continue;
        //     }

        //     if($report_data[0]->deleted_at != ''){
        //         var_dump('deleted_at');
        //         var_dump($report_visitor->report_id);
        //         continue;
        //     }

        //     if($report_data[0]->is_private == 1){
        //         var_dump('is_private');
        //         var_dump($report_visitor->report_id);
        //         continue;
        //     }

        //     if($report_data[0]->draft_flg == 1){
        //         var_dump('draft_flg');
        //         var_dump($report_visitor->report_id);
        //         continue;
        //     }

        // }

        // dd('uu');

        $report_list = Report::where('is_private', 0)->where('draft_flg', 0)->whereNull('deleted_at')->get();


        //日報全体
        $all_report_count = count($report_list);

        $all_visitor_rate = 0;
        $all_likes_receive = 0;
        $all_likes_give = 0;

        $all_visitor_count = 0;
        foreach ($report_list as $report) {
            //閲覧したかどうかチェック
            $report_visitor_count = ReportVisitor::where([
                'report_id' =>  $report->id,
                'user_id' =>  $user,
            ])->count();

            $all_visitor_count = $all_visitor_count + $report_visitor_count;
        }

        if ($all_visitor_count != 0) {
            $all_visitor_rate = round(($all_visitor_count / $all_report_count) * 100, 2);
        }


        //自分の日報情報
        $my_report_data = Report::where([
            'user_id' => $user,
            'is_private' => 0,
            'draft_flg' => 0,
        ])->get();

        $all_my_report_count = count($my_report_data);

        //いいね受け取った数
        foreach ($my_report_data as $report_data) {
            $report_content_data_list = ReportContent::where('report_id', $report_data->id)->get();
            foreach ($report_content_data_list as $report_content_data) {
                $my_report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->count();
                $all_likes_receive += $my_report_likes_count;
            }
        }

        //いいねした数
        $report_likes_count = ReportContentLike::where('user_id', $user)->count();
        $all_likes_give = $report_likes_count;


        $three_ago_month = date('Y-m-01', strtotime('-2 month'));


        //月のループ
        for ($month = date('Y-m-15'); strtotime($month) >= strtotime('2023-04-01'); $month = date('Y-m-15', strtotime($month . '-1 month'))) {
            $month_list[$month] = $month;
        }

        // $now_month = date('Y-m');
        // $last_month = date('Y-m',strtotime('-1 month'));
        // $two_ago_month = date('Y-m',strtotime('-2 month'));

        // $month_list = [$now_month,$last_month,$two_ago_month];

        $month_data = [];
        $n = 0;
        foreach ($month_list as $month) {
            $tmp = [];
            $tmp['month'] = $month;

            $firstDate = date('Y-m-d', strtotime('first day of ' . $month));
            $lastDate = date('Y-m-d', strtotime('last day of ' . $month));

            //日報全体
            $report_list = Report::where([
                'is_private' => 0,
                'draft_flg' => 0,
            ])->where('date', '>=', $firstDate)->where('date', '<=', $lastDate)->get();

            $report_count = count($report_list);

            $visitor_rate = 0;
            $likes_give = 0;
            $likes_receive = 0;

            $my_report_count = 0;

            $visitor_count = 0;
            foreach ($report_list as $report) {

                //閲覧したかどうかチェック
                $report_visitor_count = ReportVisitor::where([
                    'report_id' =>  $report->id,
                    'user_id' =>  $user,
                ])->count();

                $visitor_count = $visitor_count + $report_visitor_count;

                if ($report_visitor_count != 1) {
                    var_dump($report->id);
                }

                //いいねした数
                $report_content_data_list = ReportContent::where('report_id', $report->id)->get();

                foreach ($report_content_data_list as $report_content_data) {
                    $report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->where('user_id', $user)->count();
                    $likes_give = $likes_give + $report_likes_count;
                }

                //いいね受け取り
                if ($report->user_id == $user) {

                    $my_report_count++;

                    $report_content_data_list = ReportContent::where('report_id', $report->id)->get();

                    foreach ($report_content_data_list as $report_content_data) {
                        $report_likes_count = ReportContentLike::where('report_content_id', $report_content_data->id)->count();
                        $likes_receive = $likes_receive + $report_likes_count;
                    }
                }
            }

            //既読率計算
            if ($visitor_count != 0) {
                $visitor_rate = round(($visitor_count / $report_count) * 100, 2);
            }

            $month_txt = date(('Y年m月'), strtotime($month));

            $tmp['month'] = $month_txt;
            $tmp['visitor_rate'] = $visitor_rate;
            $tmp['likes_receive'] = $likes_receive;
            $tmp['likes_give'] = $likes_give;
            $tmp['report_count'] = $report_count;
            $tmp['my_report_count'] = $my_report_count;


            $month_data[$n] = $tmp;
            $n++;
        }




        return $content
            ->title('日報閲覧状況詳細')
            ->view('admin/report-visitor-all-detail', compact(
                'all_visitor_rate',
                'all_likes_receive',
                'all_likes_give',
                'all_my_report_count',
                'all_report_count',
                'month_data',
                'user_data'
            ));
    }
}
