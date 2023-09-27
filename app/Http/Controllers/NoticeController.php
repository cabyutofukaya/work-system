<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotice;
use App\Http\Requests\UpdateNotice;
use App\Models\Notice;
use App\Models\NoticeFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;

class NoticeController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Notice::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        $notices = Notice
            ::with(['user:id,name,deleted_at'])
            ->paginate();

        // dd($notices);

        return inertia('Notices', [
            'notices' => $notices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreNotice $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreNotice $request): \Illuminate\Http\RedirectResponse
    {

        $notice = new Notice();
     
        $notice->fill($request->safe()->merge(['user_id' => Auth::id()])->all())
            ->save();

        //写真情報登録
        $fileName = NULL;
        $originalName = NULL;
        $extension_list = ['csv', 'txt', 'pdf', 'xlsx', 'xlsm'];

        if (isset($request->file_name)) {

            $notice_id = $notice->id;

            foreach ($request->file_name as $file) {

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

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('notices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Notice $notice
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Notice $notice): Response|ResponseFactory
    {

        $notice->load([
            'user:id,name,deleted_at',
            'notice_files',
        ]);

        return inertia('NoticesShow', [
            'notice' => $notice,
            'user' => User::where('id', $notice->user_id)->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateNotice $request
     * @param \App\Models\Notice $notice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateNotice $request, Notice $notice): \Illuminate\Http\RedirectResponse
    {
        $notice->fill($request->validated())->save();

          //写真情報登録
          $fileName = NULL;
          $originalName = NULL;
          $extension_list = ['csv', 'txt', 'pdf', 'xlsx', 'xlsm'];
  
          if (isset($request->file_name)) {
  
              $notice_id = $notice->id;
  
              foreach ($request->file_name as $file) {
  
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

        return redirect()->route('notices.show', ['notice' => $notice->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Notice $notice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Notice $notice): \Illuminate\Http\RedirectResponse
    {
        $notice->delete();

        // バックボタンの戻り先ページを設定
        request()->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('notices.index');
    }
}
