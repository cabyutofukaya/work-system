<?php

namespace App\Http\Controllers;

use App\Models\ReportContent;
use Illuminate\Http\RedirectResponse;

class ReportContentController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\ReportContent $reportContent
     * @return \Illuminate\Http\RedirectResponse
     */
    public function like(ReportContent $reportContent): RedirectResponse
    {
        // 自分のいいねを検索
        $report_content_like_own = $reportContent->report_content_likes()->where("user_id", auth()->id());

        if (!$report_content_like_own->exists()) {
            // 存在しなければ登録
            $reportContent->report_content_likes()->create(["user_id" => auth()->id()]);
        } else {
            // 存在すれば解除
            $report_content_like_own->delete();
        }

        return redirect()->route('reports.show', ['report' => $reportContent->report_id]);
    }
}
