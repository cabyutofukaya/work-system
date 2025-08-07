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
            'notice_visitors',
        ]);

        // 閲覧者IDを保存
        $notice->notice_visitors()->updateOrCreate([
            'user_id' => auth()->id(),
        ]);

        // 閲覧数の保存後に閲覧者情報を取得
        $notice->loadCount(['notice_visitors']);


        // 閲覧者一覧表示のため全ユーザリストを取得
        $users = User
            ::withExists(['notice_visitors' => function ($query) use ($notice) {
                $query->where('notice_id', $notice->id);
            }])
            // 閲覧者自身を除外
            // ->whereNot('id', Auth::id())
            ->get()
            ->makeHidden("email");


        return inertia('NoticesShow', [
            'notice' => $notice,
            'user' => User::where('id', $notice->user_id)->first(),
            'users' => $users,
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

        return redirect()->route('notices.index');
    }
}
