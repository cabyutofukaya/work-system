<?php

namespace App\Http\Controllers\Api;

use App\Models\Notice;
use App\Models\NoticeFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class NoticeController extends Controller
{
    public function updateFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,xlsx,xlsm,txt,csv',
            'id' => 'required|exists:notices,id',
        ]);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $extension;

        $file->storeAs('public/notice', $fileName);

        $type = in_array(strtolower($extension), ['csv', 'txt', 'pdf', 'xlsx', 'xlsm']) ? 'file' : 'image';

        $noticeFile = NoticeFile::create([
            'notice_id' => $request->id,
            'file_name' => $originalName,
            'file_path' => $fileName,
            'file_extension' => $extension,
            'type' => $type,
        ]);

        return response()->json([
            'message' => 'ファイルアップロード完了',
            'file' => $noticeFile,
        ], 201);
    }

    public function destroy(Request $request)
    {
              Log::debug($request->all());

        $request->validate([
            'notice' => 'required|exists:notices,id',
            'notice_file' => 'required|exists:notice_files,id',
        ]);


        $noticeFile = NoticeFile::where('id', $request->notice_file)
            ->where('notice_id', $request->notice)
            ->firstOrFail();

        \Storage::delete('public/notice/' . $noticeFile->file_path);
        $noticeFile->delete();

        return response()->json([
            'message' => 'ファイルを削除しました',
        ], 200);
    }
}
