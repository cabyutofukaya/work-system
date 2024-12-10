<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalesTodo;
use App\Http\Requests\EditSalesTodo;
use App\Http\Requests\ShowSalesTodo;
use App\Http\Requests\StoreSalesTodo;
use App\Http\Requests\UpdateSalesTodo;
use App\Models\Client;
use App\Models\Genre;
use App\Models\SalesTodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class SalesTodoController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // ポリシーによる認可
        $this->authorizeResource(SalesTodo::class);
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
        $validator = Validator::make($request->all(), (new ShowSalesTodo)->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }
        $validated = $validator->validated();

        return inertia('SalesTodos', [
            'sales_todos' => function () use ($request, $validated) {
                $sales_todos = SalesTodo
                    ::with(['client:id,name,image', 'sales_todo_participants.user:id,name'])
                    // 自分のToDoに絞る
                    ->where('user_id', Auth::id());

                // 会社検索
                if ($request->filled('client_id')) {
                    $sales_todos->where('client_id', $validated["client_id"]);
                }

                // ワード検索
                if ($request->filled('word')) {
                    // ワードを空白文字で分割してAND検索
                    foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validated["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                        $sales_todos->where(function ($query) use ($word) {
                            // 詳細
                            $query->whereLike('description', $word);

                            // 相手先担当者
                            $query->orWhereLike('contact_person', $word);

                            // 開催日時
                            $query->orWhereLike('scheduled_at', $word);

                            // 検索ワードが数字のみであれば開催日付の数字部分から検索する
                            // ハイフンしか除去していないので時間部分には未対応
                            if (preg_match('/^[\-\d]+$/', $word)) {
                                $query->orWhereRaw('replace(scheduled_at, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))]);
                            }

                            // 会社名
                            $query->orWhereHas('client', function ($query) use ($word) {
                                $query
                                    ->whereLike('name', $word)
                                    ->orWhereLike('name_kana', $word);
                            });

                            // 社内担当者名
                            $query->orWhereHas('sales_todo_participants.user', function ($query) use ($word) {
                                $query->whereLike('name', $word);
                            });
                        });
                    }
                }

                return $sales_todos->paginate()->withQueryString();
            },

            // 検索フォームの初期値
            'form_params' => [
                'client_id' => $validated["client_id"] ?? null,
                'word' => $validated["word"] ?? null,
            ],

            // 会社ID検索時の対象
            'client' => fn() => Client::find($request->input('client_id'), ["id", "name"])
        ]);
    }

    /**
     * ToDo作成フォーム
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response|\Inertia\ResponseFactory|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        // 非同期の会社リスト取得ではバリデーションエラー時にリダイレクトを抑制するためエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new CreateSalesTodo())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $clients = null;
        if (request()->hasAny(["client_id"])) {
            // 会社IDが指定されていれば該当の会社だけを取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id'])
                ->where("id", $validator->validated()["client_id"]);
        } else if (request()->hasAny(["name", "client_type_id", "client_type_taxibus_category", "genre_id"])) {
            // 条件が指定されていれば該当する会社一覧を取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id']);

            // 会社名・よみがな
            if ($validator->validated()["name"]) {
                $clients->where(function ($query) use ($validator) {
                    $query->whereLike('name', $validator->validated()["name"])
                        ->orWhereLike('name_kana', $validator->validated()["name"]);
                });
            }

            // 会社タイプ
            if ($validator->validated()["client_type_id"]) {
                $clients->where("client_type_id", $validator->validated()["client_type_id"]);
            }

            // 固有情報 バス・タクシー会社
            if ($validator->validated()["client_type_taxibus_category"]) {
                // カテゴリー検索
                $clients->whereHas('client_type_taxibus', function (Builder $query) use ($validator) {
                    $query->where('category', $validator->validated()["client_type_taxibus_category"]);
                });
            }

            // ジャンル
            if ($validator->validated()["genre_id"]) {
                $clients->whereHas('genres', function (Builder $query) use ($validator) {
                    // orderで指定したカラムにIntegrity constraint violationエラーが発生するためグローバルスコープを削除
                    $query->withoutGlobalScope('order')->where('genre_id', $validator->validated()["genre_id"]);
                });
            }

            // 該当する件数が多すぎればエラーを設定して戻す
            if ($clients->count() > 100) {
                return inertia('SalesTodosCreate', [
                    'clients' => null,
                    'clients_count' => $clients->count(),
                    "errors" => ['event' => '会社を選択するには条件を追加して100件以下にしてください。'],
                ]);
            }
        }

        // レスポンス
        return inertia('SalesTodosCreate', [
            'client_types' => fn() => collect(config("const.client_types"))->values(),
            'client_type_taxibus_categories' => fn() => collect(config("const.client_types.taxibus.categories"))->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->values(),
            'genres' => fn() => Genre::get(),
            'clients_total_count' => fn() => Client::count(),
            'clients_count' => Inertia::lazy(fn() => $clients->count()),
            'clients' => Inertia::lazy(fn() => $clients->get(["id", "client_type_id", "name", "name_kana", "image"])),
            'users' => User::get(["id", "name"]),
        ]);
    }

    /**
     * ToDo作成
     *
     * @param \App\Http\Requests\StoreSalesTodo $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(StoreSalesTodo $request): RedirectResponse
    {
        $sales_todo = new SalesTodo();

        DB::transaction(function () use ($request, $sales_todo) {
            // Todo情報を保存
            $sales_todo->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 社内担当者情報を保存
            if (array_key_exists("participants", $request->validated())) {
                foreach ($request->validated()['participants'] as $participants) {
                    $sales_todo->sales_todo_participants()->create(["user_id" => $participants]);
                }
            }
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('sales-todos.index');
    }

    /**
     * Todo編集フォーム
     *
     * @param \App\Models\SalesTodo $salesTodo
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(SalesTodo $salesTodo, Request $request): Response|ResponseFactory
    {
        // 非同期の会社リスト取得ではバリデーションエラー時にリダイレクトを抑制するためエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new EditSalesTodo())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $salesTodo->load([
            'sales_todo_participants',
        ]);

        // 会社リスト 日報コンテンツに設定されている会社は削除済みでも含める
        $clients = Client::withTrashed()
            ->with(['client_type_taxibus:id,client_id,category', 'genres:id'])
            ->where(function (Builder $query) use ($salesTodo) {
                $query->whereNull("deleted_at")
                    ->orWhere("id", $salesTodo->client_id);
            });

        if (request()->hasAny(["client_id"])) {
            // 会社IDが指定されていれば該当の会社だけを取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id'])
                ->where("id", $validator->validated()["client_id"]);
        } else if (request()->hasAny(["name", "client_type_id", "client_type_taxibus_category", "genre_id"])) {
            // 条件が指定されていれば該当する会社一覧を取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id']);

            // 会社名・よみがな
            if ($validator->validated()["name"]) {
                $clients->where(function ($query) use ($validator) {
                    $query->whereLike('name', $validator->validated()["name"])
                        ->orWhereLike('name_kana', $validator->validated()["name"]);
                });
            }

            // 会社タイプ
            if ($validator->validated()["client_type_id"]) {
                $clients->where("client_type_id", $validator->validated()["client_type_id"]);
            }

            // 固有情報 バス・タクシー会社
            if ($validator->validated()["client_type_taxibus_category"]) {
                // カテゴリー検索
                $clients->whereHas('client_type_taxibus', function (Builder $query) use ($validator) {
                    $query->where('category', $validator->validated()["client_type_taxibus_category"]);
                });
            }

            // ジャンル
            if ($validator->validated()["genre_id"]) {
                $clients->whereHas('genres', function (Builder $query) use ($validator) {
                    // orderで指定したカラムにIntegrity constraint violationエラーが発生するためグローバルスコープを削除
                    $query->withoutGlobalScope('order')->where('genre_id', $validator->validated()["genre_id"]);
                });
            }

            // 該当する件数が多すぎればエラーを設定して戻す
            if ($clients->count() > 100) {
                return inertia('SalesTodosEdit', [
                    'clients_count' => $clients->count(),
                    'clients' => null,
                    "errors" => ['event' => '会社を選択するには条件を追加して100件以下にしてください。'],
                ]);
            }
        }

        // レスポンス
        return inertia('SalesTodosEdit', [
            'client_types' => fn() => collect(config("const.client_types"))->values(),
            'client_type_taxibus_categories' => fn() => collect(config("const.client_types.taxibus.categories"))->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->values(),
            'genres' => fn() => Genre::get(),
            'clients_total_count' => fn() => Client::count(),
            'clients_count' => Inertia::lazy(fn() => $clients->count()),
            'clients' => Inertia::lazy(fn() => $clients->get(["id", "client_type_id", "name", "name_kana", "image"])),
            'users' => User::get(["id", "name"]),
            'sales_todo' => $salesTodo,
        ]);
    }

    /**
     * Todo編集
     *
     * @param \App\Http\Requests\UpdateSalesTodo $request
     * @param \App\Models\SalesTodo $sales_todo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdateSalesTodo $request, SalesTodo $sales_todo): RedirectResponse
    {
        DB::transaction(function () use ($request, $sales_todo) {
            // Todo情報を保存
            $sales_todo->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 社内担当者情報を保存
            $sales_todo->sales_todo_participants()->delete();
            if (array_key_exists("participants", $request->validated())) {
                foreach ($request->validated()['participants'] as $participants) {
                    $sales_todo->sales_todo_participants()->create(["user_id" => $participants]);
                }
            }
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('sales-todos.index');
    }

    /**
     * ToDo削除
     *
     * @param \App\Models\SalesTodo $salesTodo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SalesTodo $salesTodo): RedirectResponse
    {
        $salesTodo->delete();

        return back();
    }

    /**
     * ToDo対応済みフラグ変更トグル
     *
     * @param \App\Models\SalesTodo $sales_todo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function complete(SalesTodo $sales_todo): RedirectResponse
    {
        $this->authorize('complete', $sales_todo);

        $sales_todo->is_completed = !$sales_todo->is_completed;
        $sales_todo->save();

        return back();
    }
}
