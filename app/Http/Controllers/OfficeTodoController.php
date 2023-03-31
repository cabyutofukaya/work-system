<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowClient;
use App\Http\Requests\StoreOfficeTodo;
use App\Http\Requests\UpdateOfficeTodo;
use App\Models\OfficeTodo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Inertia\ResponseFactory;

class OfficeTodoController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // ポリシーによる認可
        $this->authorizeResource(OfficeTodo::class);
    }

    /**
     * ToDoリスト
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): Response|ResponseFactory
    {
        // GETメソッドではInertiaによるバリデーションエラー処理が行われないため
        // リダイレクトを行わずエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new ShowClient)->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }
        $validated = $validator->validated();

        return inertia('OfficeTodos', [
            'office_todos' => function () use ($validated, $request) {
                $office_todos = OfficeTodo
                    ::with(['office_todo_participants.user:id,name'])
                    // 自分のToDoに絞る
                    ->where('user_id', Auth::id());

                // ワード検索
                if ($request->filled('word')) {
                    // ワードを空白文字で分割してAND検索
                    foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validated["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                        $office_todos->where(function ($query) use ($word) {
                            // タイトル
                            $query->whereLike('title', $word);

                            // 詳細
                            $query->orWhereLike('description', $word);

                            // 開催日時
                            $query->orWhereLike('scheduled_at', $word);

                            // 検索ワードが数字のみであれば開催日付の数字部分から検索する
                            // ハイフンしか除去していないので時間部分には未対応
                            if (preg_match('/^[\-\d]+$/', $word)) {
                                $query->orWhereRaw('replace(scheduled_at, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))]);
                            }

                            // 社内担当者名
                            $query->orWhereHas('office_todo_participants.user', function ($query) use ($word) {
                                $query->whereLike('name', $word);
                            });
                        });
                    }
                }

                return $office_todos->paginate()->withQueryString();
            },

            // 検索フォームの初期値
            'form_params' => [
                'word' => $validated["word"] ?? null,
            ],
        ]);
    }

    /**
     * ToDo作成フォーム
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function create(): Response|ResponseFactory
    {
        return inertia('OfficeTodosCreate', [
            'users' => User::where('id', '!=', Auth::id())->get(["id", "name"]),
        ]);
    }

    /**
     * ToDo作成
     *
     * @param \App\Http\Requests\StoreOfficeTodo $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(StoreOfficeTodo $request): RedirectResponse
    {
        $office_todo = new OfficeTodo();

        DB::transaction(function () use ($request, $office_todo) {
            // Todo情報を保存
            $office_todo->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 社内担当者情報を保存
            if (array_key_exists("participants", $request->validated())) {
                foreach ($request->validated()['participants'] as $participants) {
                    $office_todo->office_todo_participants()->create(["user_id" => $participants]);
                }
            }
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('office-todos.index');
    }

    /**
     * Todo編集フォーム
     *
     * @param \App\Models\OfficeTodo $officeTodo
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(OfficeTodo $officeTodo): Response|ResponseFactory
    {
        $officeTodo->load([
            'office_todo_participants',
        ]);

        return inertia('OfficeTodosEdit', [
            'users' => User::where('id', '!=', Auth::id())->get(["id", "name"]),
            'office_todo' => $officeTodo,
        ]);
    }

    /**
     * Todo編集
     *
     * @param \App\Http\Requests\UpdateOfficeTodo $request
     * @param \App\Models\OfficeTodo $office_todo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdateOfficeTodo $request, OfficeTodo $office_todo): RedirectResponse
    {
        DB::transaction(function () use ($request, $office_todo) {
            // Todo情報を保存
            $office_todo->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 社内担当者情報を保存
            $office_todo->office_todo_participants()->delete();
            if (array_key_exists("participants", $request->validated())) {
                foreach ($request->validated()['participants'] as $participants) {
                    $office_todo->office_todo_participants()->create(["user_id" => $participants]);
                }
            }
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('office-todos.index');
    }

    /**
     * ToDo削除
     *
     * @param \App\Models\OfficeTodo $officeTodo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(OfficeTodo $officeTodo): RedirectResponse
    {
        $officeTodo->delete();

        return back();
    }

    /**
     * ToDo対応済みフラグ変更トグル
     *
     * @param \App\Models\OfficeTodo $office_todo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function complete(OfficeTodo $office_todo): RedirectResponse
    {
        $this->authorize('complete', $office_todo);

        $office_todo->is_completed = !$office_todo->is_completed;
        $office_todo->save();

        return back();
    }
}
