<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowMeeting;
use App\Http\Requests\StoreMeeting;
use App\Http\Requests\UpdateMeeting;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Inertia\ResponseFactory;

class MeetingController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // ポリシーによる認可
        $this->authorizeResource(Meeting::class);
    }

    /**
     * 議事録一覧
     *
     * @param \App\Http\Requests\ShowMeeting $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): \Illuminate\Http\Response|Response|ResponseFactory|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        // index/mine共通処理
        // 以降の $request->input はバリデーション済み
        $meetings = $this->search($request);

        return inertia('Meetings', [
            'meetings' => $meetings->paginate()->withQueryString(),

            // 検索フォームの初期値
            'form_params' => [
                'word' => $request->input('word'),
            ],
        ]);
    }

    /**
     * 自分の議事録一覧
     *
     * @param \App\Http\Requests\ShowMeeting $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function mine(Request $request): \Illuminate\Http\Response|Response|ResponseFactory|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        // index/mine共通処理
        // 以降の $request->input はバリデーション済み
        $meetings = $this->search($request);

        // 自分の議事録に絞る
        $meetings->where('user_id', Auth::id());


        return inertia('MeetingsMine', [
            'meetings' => $meetings->paginate()->withQueryString(),

            // 検索フォームの初期値
            'form_params' => [
                'word' => $request->input('word'),
            ],
        ]);
    }

    /**
     * index/mine共通処理
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Illuminate\Validation\ValidationException
     */
    public function search(Request $request): Builder
    {
        // GETメソッドではInertiaによるバリデーションエラー処理が行われないため
        // リダイレクトを行わずエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new ShowMeeting())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $meetings = Meeting
            ::with(['user:id,name,deleted_at'])
            ->withCount([
                'meeting_likes',
                'meeting_comments',
            ])
            // 自分自身が閲覧済みかどうか
            ->withExists([
                'meeting_visitors as is_visited' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ]);


        // ワード検索
        if ($request->filled('word')) {
            foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validator->validated()["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $meetings->where(function ($query) use ($word) {
                    // タイトル
                    $query->whereLike('title', $word);

                    // 開催日時
                    $query->orWhereLike('started_at', $word);

                    // 検索ワードが数字のみであれば開催日付の数字部分から検索する
                    // ハイフンしか除去していないので時間部分には未対応
                    if (preg_match('/^[\-\d]+$/', $word)) {
                        $query->orWhereRaw('replace(started_at, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))]);
                    }

                    // ユーザID・ユーザ名を検索
                    $query->orWhereHas('user', function ($query) use ($word) {
                        $query->whereLike('name', $word);
                    });
                });
            }
        }

        return $meetings;
    }

    /**
     * 議事録作成フォーム
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function create(): Response|ResponseFactory
    {
        return inertia('MeetingsCreate');
    }

    /**
     * 議事録作成
     *
     * @param \App\Http\Requests\StoreMeeting $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(StoreMeeting $request): RedirectResponse
    {
        $meeting = new Meeting();

        DB::transaction(function () use ($request, $meeting) {
            // 議事録情報を保存
            $meeting->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('meetings.mine'),
        ]);

        return redirect()->route('meetings.show', ['meeting' => $meeting->id]);
    }

    /**
     * 議事録詳細
     *
     * @param \App\Models\Meeting $meeting
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Meeting $meeting): Response|ResponseFactory
    {
        $meeting
            ->load([
                'user:id,name,deleted_at',
                'meeting_comments.user',
                'meeting_visitors'
            ])
            // いいね数を取得
            ->loadCount('meeting_likes as likes_count')
            // 自分自身がいいねを実行しているかどうか
            ->loadExists([
                'meeting_likes as has_own_like' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ]);

        // 閲覧者IDを保存
        $meeting->meeting_visitors()->updateOrCreate([
            'user_id' => auth()->id(),
        ]);

        // 閲覧数の保存後に閲覧者情報を取得
        $meeting->loadCount(['meeting_visitors']);

        // 閲覧者一覧表示のため全ユーザリストを取得
        $users = User
            ::withExists(['meeting_visitors' => function ($query) use ($meeting) {
                $query->where('meeting_id', $meeting->id);
            }])
            // 閲覧者自身を除外
            ->whereNot('id', Auth::id())
            ->get()
            ->makeHidden("email");

        return inertia('MeetingsShow', [
            'meeting' => $meeting,
            'users' => $users,
        ]);
    }

    /**
     * 議事録編集フォーム
     *
     * @param \App\Models\Meeting $meeting
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(Meeting $meeting): Response|ResponseFactory
    {
        $meeting->load([
            'user:id,name,deleted_at',
        ]);

        return inertia('MeetingsEdit', [
            'meeting' => $meeting,
        ]);
    }

    /**
     * 議事録編集
     *
     * @param \App\Http\Requests\UpdateMeeting $request
     * @param \App\Models\Meeting $meeting
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdateMeeting $request, Meeting $meeting): RedirectResponse
    {
        DB::transaction(function () use ($request, $meeting) {
            // 議事録情報を保存
            $meeting->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('meetings.mine'),
        ]);

        return redirect()->route('meetings.show', ['meeting' => $meeting->id]);
    }

    /**
     * 議事録削除
     *
     * @param \App\Models\Meeting $meeting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Meeting $meeting): RedirectResponse
    {
        $meeting->delete();

        return redirect()->route('meetings.mine');
    }

    /**
     * 議事録いいね
     *
     * @param \App\Models\Meeting $meeting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function like(Meeting $meeting): RedirectResponse
    {
        // 自分のいいねを検索
        $meeting_like_own = $meeting->meeting_likes()->where("user_id", auth()->id());

        if (!$meeting_like_own->exists()) {
            // 存在しなければ登録
            $meeting->meeting_likes()->create(["user_id" => auth()->id()]);
        } else {
            // 存在すれば解除
            $meeting_like_own->delete();
        }

        return redirect()->route('meetings.show', ['meeting' => $meeting->id]);
    }
}
