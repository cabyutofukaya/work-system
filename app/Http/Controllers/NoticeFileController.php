<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Genre;
use App\Models\Product;
use App\Models\Notice;
use App\Models\NoticeFile;
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

class NoticeFileController extends Controller
{

    /**
     * 日報詳細
     *
     * @param \App\Models\Notice $report
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */

    public function edit(Notice $notice): Response|ResponseFactory
    {
        return inertia('NoticesFileAdd', [
            'notice' => $notice
        ]);
    }


    /**
     * 日報削除
     *
     * @param \App\Models\Notice $report
     * @param \App\Models\NoticeFile $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Notice $notice): RedirectResponse
    {

        $notice_file = new NoticeFile();
        $notice_file->where('id', $request->notice_file)
            ->update([
                'deleted_at' => date('Y-m-d H:i'),
            ]);

        return redirect()->route('notices.show', ['notice' => $notice->id]);
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {

        //写真情報登録
        $fileName = NULL;
        $originalName = NULL;
        $extension_list = ['csv', 'txt', 'pdf', 'xlsx', 'xlsm'];

        if (isset($request->file)) {

            $notice_id = $request->id;

            foreach ($request->file as $file) {

                $originalName = $file->getClientOriginalName();

                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $fileName =  auth()->id() . date('YmdGhi') . mt_rand('111111111', '999999999') . '.' . $extension;
                $file->storeAs('', '/public/notice/' . $fileName);

                $type = 'image';
                if (in_array($extension, $extension_list)) {
                    $type = 'file';
                }

                $notice_file = new NoticeFile();
                $notice_file->create([
                    'type' => $type,
                    'name' => $originalName,
                    'path' => $fileName,
                    'notice_id' => $notice_id,
                ]);
            }
        }

        return redirect()->route('notices.show', ['notice' => $request->id]);
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

        $notice_file = new NoticeFile();
        $notice_file->where('id', $request->notice_file)
            ->update([
                'deleted_at' => date('Y-m-d H:i'),
            ]);

        return redirect()->route('notices.show', ['notice' => $request->notice]);
    }
}
