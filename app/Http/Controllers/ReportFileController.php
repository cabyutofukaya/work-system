<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Genre;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\SalesMethod;
use App\Models\User;
use App\Models\ReportCommentUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;
use Log;

class ReportFileController extends Controller
{
  
      /**
     * 日報詳細
     *
     * @param \App\Models\Report $report
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */

    public function edit(Report $report): Response|ResponseFactory
    {
        return inertia('ReportsFileAdd', [
            'report' => $report
        ]);
    }


      /**
     * 日報削除
     *
     * @param \App\Models\Report $report
     * @param \App\Models\ReportFile $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request ,Report $report): RedirectResponse
    {

        dd($request->all());

        $report_file = new ReportFile();
        $report_file->where('id',$request->report_file)
        ->update([
            'deleted_at' => date('Y-m-d H:i'),
        ]);
        
        return redirect()->route('reports.edit', ['report' => $report->id]);
    }

    /**
     * 日報削除
     *
     * @param \App\Models\Report $report
     * @param \App\Models\ReportFile $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {

        $report_file = new ReportFile();
        $report_file->where('id',$request->report_file)
        ->update([
            'deleted_at' => date('Y-m-d H:i'),
        ]);
        
        return redirect()->route('reports.edit', ['report' => $request->report]);
    }
}
