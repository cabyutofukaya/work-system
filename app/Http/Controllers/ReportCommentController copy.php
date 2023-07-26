<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportComment;
use App\Models\Base\Report;
use App\Models\ReportCommentMention;
use App\Models\ReportComment;
use App\Models\User;
use App\Models\ReportCommentUser;
use Illuminate\Http\RedirectResponse;

class ReportCommentController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(ReportComment::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreReportComment $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreReportComment $request): RedirectResponse
    {
        $report = Report::where('id',$request->report_id)->first();
        
     
        $report_comment = ReportComment::create([
            'user_id' => auth()->id(),
            'report_id' => $request->validated()["report_id"],
            'comment' => $request->validated()["comment"],
        ]);

        if(isset($request->mention_id)){
            foreach($request->mention_id as $mention_id){
                ReportCommentMention::create([
                    'user_id' => $mention_id,
                    'report_comment_id' => $report_comment->id,
                ]);
            }
        }

        return redirect()->route('reports.show', ['report' => $report_comment->report_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ReportComment $reportComment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ReportComment $reportComment): RedirectResponse
    {
        $report_id = $reportComment->report_id;

        $reportComment->delete();

        return redirect()->route('reports.show', ['report' => $report_id]);
    }

    public function complete(int $comment_id,int $user_id): RedirectResponse
    {
        ReportCommentMention::where([
            'report_comment_id' => $comment_id,
            'user_id' => $user_id,
        ])
        ->update([
            'is_readed' => 1,
        ]);
       

        return back();
    }
}
