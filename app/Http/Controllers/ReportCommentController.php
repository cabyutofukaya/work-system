<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportComment;
use App\Models\Base\Report;
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
        $mention_name = NULL;
        
        if($request->mention_id){
            $user = User::find($request->validated()["mention_id"]);
            $mention_id = $user->id;
            $mention_name = $user->name;
        }else{
            $user = User::find($report->user_id);
            $mention_id = $user->id;
        }
        
        $report_comment = ReportComment::create([
            'user_id' => auth()->id(),
            'report_id' => $request->validated()["report_id"],
            'comment' => $request->validated()["comment"],
            'mention_id' => $mention_id,
            'mention_name' => $mention_name,
        ]);

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

    public function complete(ReportComment $report_comment): RedirectResponse
    {

        $report_comment->is_readed = 1;
        $report_comment->save();

        return back();
    }
}
