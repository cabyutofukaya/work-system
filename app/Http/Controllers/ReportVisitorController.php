<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\ReportVisitor;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;
use Log;


class ReportVisitorController extends Controller
{
    /**
     * 日報一覧
     *
     * @param \App\Http\Requests\ShowReport $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index($user_id)
    {
        $reports_list = Report::where([
            'is_private' => 0,
        ])->where('date','>=', date('Y-m-d', strtotime('-3 month')))->get();
        
        $report_count = count($reports_list);

        $visitor_count = 0;

        foreach ($reports_list as $report) {
            
            $report_visitor = ReportVisitor::where([
                'report_id' => $report->id,
                'user_id' => $user_id,
            ])->count();


            if($report_visitor > 0){
                $visitor_count++;
            }
           
        }

        $visitor_rate = 0;
        if ($visitor_count != 0) {
            $visitor_rate = round(($visitor_count / $report_count) * 100, 0);
        }


        // $suggestion = [];
        return response()->json(['visitor_rate' => $visitor_rate], 200);
    }
}
