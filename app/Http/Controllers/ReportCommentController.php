<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportComment;
use App\Models\ReportComment;
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
        $report_comment = ReportComment::create([
            'user_id' => auth()->id(),
            'report_id' => $request->validated()["report_id"],
            'comment' => $request->validated()["comment"],
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
}
