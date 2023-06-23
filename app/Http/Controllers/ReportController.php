<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReport;
use App\Http\Requests\EditReport;
use App\Http\Requests\ShowReport;
use App\Http\Requests\StoreReport;
use App\Http\Requests\UpdateReport;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Genre;
use App\Models\Product;
use App\Models\Report;
use App\Models\SalesMethod;
use App\Models\User;
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

class ReportController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        // ポリシーによる認可
        $this->authorizeResource(Report::class);
    }

    /**
     * 日報一覧
     *
     * @param \App\Http\Requests\ShowReport $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): \Illuminate\Http\Response|Response|ResponseFactory|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        // index/mine共通処理
        // 以降の $request->input はバリデーション済み
        $reports = $this->search($request);

        // 非公開の日報を除外
        $reports->exceptPrivate();
   
        return inertia('Reports', [


            'reports' => $reports->paginate()->withQueryString(),

            // 'reports' => $report,

            // 検索フォームの初期値
            'form_params' => [
                'client_id' => $request->input('client_id'),
                'word' => $request->input('word'),
                'only_complaint' => $request->input('only_complaint'),
                'is_visited' => $request->input('is_visited'),
            ],

            // 会社ID検索時の対象
            'client' => Client::find($request->input('client_id'), ["id", "name"]),
        ]);
    }

    /**
     * 自分の日報一覧
     *
     * @param \App\Http\Requests\ShowReport $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function mine(Request $request): \Illuminate\Http\Response|Response|ResponseFactory|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        // index/mine共通処理
        // 以降の $request->input はバリデーション済み
        $reports = $this->search($request);

        // 自分の日報に絞る
        $reports->where('user_id', Auth::id());

        return inertia('ReportsMine', [
            'reports' => $reports->paginate()->withQueryString(),

            // 検索フォームの初期値
            'form_params' => [
                'client_id' => $request->input('client_id'),
                'word' => $request->input('word'),
                'only_complaint' => $request->input('only_complaint'),
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
        $validator = Validator::make($request->all(), (new ShowReport())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $reports = Report
            ::with(['user:id,name,deleted_at'])
            ->withExists([
                'report_contents_sales',
                'report_contents_work',
                // 自分自身が閲覧済みかどうか
                'report_visitors as is_visited' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ])
            ->withCount([
                'report_content_likes',
                'report_comments',
            ]);

        // 会社検索
        if ($request->filled('client_id')) {
            $reports->whereHas('report_contents', function ($query) use ($validator) {
                $query->where('client_id', $validator->validated()["client_id"]);
            });
        }

        // ワード検索
        if ($request->filled('word')) {
            foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validator->validated()["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $reports->where(function ($query) use ($word) {
                    $query->whereLike('date', $word);

                    // 検索ワードが数字のみであれば日付の数字部分から検索する
                    if (preg_match('/^[\-\d]+$/', $word)) {
                        $query->orWhereRaw('replace(date, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))]);
                    }

                    // ユーザID・ユーザ名を検索
                    $query->orWhereHas('user', function ($query) use ($word) {
                        $query->whereLike('name', $word);
                    });

                    // 日報コンテンツから検索
                    $query->orWhereHas('report_contents', function ($query) use ($word) {
                        // タイトル・本文・面談者の検索
                        $query
                            ->Where(function ($query) use ($word) {
                                $query->whereLike('title', $word)
                                    ->orWhereLike('description', $word)
                                    ->orWhereLike('participants', $word);
                            })
                            ->orWhereHas('client', function ($query) use ($word) {
                                $query
                                    ->whereLike('name', $word)
                                    ->orWhereLike('name_kana', $word);
                            });
                    });
                });
            }
        }

        // クレーム・トラブルのみ
        if ($request->filled('only_complaint') && $validator->validated()["only_complaint"]) {
            $reports->whereHas('report_contents', function ($query) use ($request) {
                $query->where('is_complaint', true);
            });
        }


        // 未読のみ
        if ($request->is_visited) {
            $reports->WhereDoesntHave('report_visitors', function ($query) use ($request) {
                $query->where('user_id', auth()->id());
            });
        }



        return $reports;
    }

    /**
     * 日報作成フォーム
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request): Response
    {
        // 非同期の会社リスト取得ではバリデーションエラー時にリダイレクトを抑制するためエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new CreateReport())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        // 会社リスト
        $clients = null;
        if (request()->hasAny(["client_id"])) {
            // 会社IDが指定されていれば該当の会社だけを取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id', 'branches:id,client_id,name'])
                ->where("id", $validator->validated()["client_id"]);
        } else if (request()->hasAny(["name", "client_type_id", "client_type_taxibus_category", "genre_id"])) {
            // 条件が指定されていれば該当する会社一覧を取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id', 'branches:id,client_id,name']);

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
                return inertia('ReportsCreate', [
                    'clients' => null,
                    'clients_count' => $clients->count(),
                    "errors" => ['event' => '会社を選択するには条件を追加して100件以下にしてください。'],
                ]);
            }
        }

        // レスポンス
        return inertia('ReportsCreate', [
            'client_types' => fn () => collect(config("const.client_types"))->values(),
            'client_type_taxibus_categories' => fn () => collect(config("const.client_types.taxibus.categories"))->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->values(),
            'genres' => fn () => Genre::get(),
            'clients_total_count' => fn () => Client::count(),
            'clients_count' => Inertia::lazy(fn () => $clients->count()),
            'clients' => Inertia::lazy(fn () => $clients->get(["id", "client_type_id", "name", "name_kana", "image"])),
            'products' => fn () => Product::get(),
            'evaluations' => fn () => Evaluation::get(),
            'sales_methods' => fn () => SalesMethod::get(),
        ]);
    }

    /**
     * 日報作成
     *
     * @param \App\Http\Requests\StoreReport $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(StoreReport $request): RedirectResponse
    {
        $report = new Report();

        DB::transaction(function () use ($request, $report) {
            // 日報情報を保存
            $report->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 日報コンテンツ情報を保存
            foreach ($request->validated()['report_contents'] as $report_content) {
                /* @var \App\Models\ReportContent $report_content_upsert */
                $report_content_upsert = $report->report_contents()->create([
                    'type' => $report_content['type'],
                    'description' => $report_content['description'] ?? null,
                    'is_complaint' => $report_content['is_complaint'],
                    'title' => $report_content['title'] ?? null,
                    'client_id' => $report_content['client_id'] ?? null,
                    'branch_id' => $report_content['branch_id'] ?? null,
                    'participants' => $report_content['participants'] ?? null,
                    'sales_method_id' => $report_content['sales_method_id'] ?? null,
                    'product_description' => $report_content['product_description'] ?? null,
                ]);

                // 商材評価情報を保存
                foreach ($report_content['product_evaluation'] as $product_evaluation) {
                    $report_content_upsert->products()->attach(
                        $product_evaluation["product_id"],
                        ['evaluation_id' => $product_evaluation["evaluation_id"]]
                    );
                }
            }
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('reports.mine'),
        ]);

        return redirect()->route('reports.show', ['report' => $report->id]);
    }

    /**
     * 日報詳細
     *
     * @param \App\Models\Report $report
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Report $report): Response|ResponseFactory
    {
        $report->load([
            'user:id,name,deleted_at',
            'report_contents.client',
            'report_contents.branch',
            'report_contents.sales_method:id,name',
            'report_contents' => function ($query) {
                $query
                    // いいね数を取得
                    ->withCount('report_content_likes as likes_count')

                    // 自分自身がいいねを実行しているかどうか
                    ->withExists([
                        'report_content_likes as has_own_like' => function ($query) {
                            $query->where('user_id', auth()->id());
                        }
                    ]);
            },
            'report_contents.products.pivot.evaluation',
            'report_comments.user',
            'report_visitors'
        ]);

        // 閲覧者IDを保存
        $report->report_visitors()->updateOrCreate([
            'user_id' => auth()->id(),
        ]);

        // 閲覧数の保存後に閲覧者情報を取得
        $report->loadCount(['report_visitors']);

        // 閲覧者一覧表示のため全ユーザリストを取得
        $users = User
            ::withExists(['report_visitors' => function ($query) use ($report) {
                $query->where('report_id', $report->id);
            }])
            // 閲覧者自身を除外
            ->whereNot('id', Auth::id())
            ->get()
            ->makeHidden("email");

        return inertia('ReportsShow', [
            'report' => $report,
            'users' => $users,
        ]);
    }

    /**
     * 日報編集フォーム
     *
     * @param \App\Models\Report $report
     * @return \Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Report $report, Request $request): Response|ResponseFactory
    {
        // 非同期の会社リスト取得ではバリデーションエラー時にリダイレクトを抑制するためエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new EditReport())->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }

        $report->load([
            'user:id,name,deleted_at',
            'report_contents:id,report_id,type,client_id,branch_id,sales_method_id,title,participants,description,is_complaint,product_description',
            'report_contents.client',
            'report_contents.branch',
            'report_contents.sales_method',
        ]);

        // 商材に対する評価の配列を追加
        $report->report_contents->each(function ($report_content) {
            $report_content
                ->makeHidden(['products'])
                ->makeVisible(['product_evaluation']);
        });

        // 会社リスト 日報コンテンツに設定されている会社は削除済みでも含める
        $clients = Client::withTrashed()
            ->with(['client_type_taxibus:id,client_id,category', 'genres:id'])
            ->where(function (Builder $query) use ($report) {
                $query->whereNull("deleted_at")
                    ->orWhereIn("id", $report->report_contents_sales->pluck("client_id"));
            });

        if (request()->hasAny(["client_id"])) {
            // 会社IDが指定されていれば該当の会社だけを取得
            $clients = Client::with(['client_type_taxibus:id,client_id,client_id,category', 'genres:id', 'branches:id,client_id,name'])
                ->where("id", $validator->validated()["client_id"]);
        } else if (request()->hasAny(["name", "client_type_id", "client_type_taxibus_category", "genre_id"])) {
            // 条件が指定されていれば該当する会社一覧を取得
            $clients = Client::with(['client_type_taxibus:id,client_id,category', 'genres:id', 'branches:id,client_id,name']);

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
                return inertia('ReportsEdit', [
                    'clients' => null,
                    'clients_count' => $clients->count(),
                    "errors" => ['event' => '会社を選択するには条件を追加して100件以下にしてください。'],
                ]);
            }
        }

        return inertia('ReportsEdit', [
            'report' => fn () => $report,
            'client_types' => fn () => collect(config("const.client_types"))->values(),
            'client_type_taxibus_categories' => fn () => collect(config("const.client_types.taxibus.categories"))->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->values(),
            'genres' => fn () => Genre::get(),
            'clients_total_count' => fn () => Client::count(),
            'clients_count' => Inertia::lazy(fn () => $clients->count()),
            'clients' => Inertia::lazy(fn () => $clients->get(["id", "client_type_id", "name", "name_kana", "image"])),
            'products' => fn () => Product::get(),
            'evaluations' => fn () => Evaluation::get(),
            'sales_methods' => fn () => SalesMethod::get(),
        ]);
    }

    /**
     * 日報編集
     *
     * @param \App\Http\Requests\UpdateReport $request
     * @param \App\Models\Report $report
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdateReport $request, Report $report): RedirectResponse
    {
        DB::transaction(function () use ($request, $report) {
            // 日報情報を保存
            $report->fill($request->safe()->merge(['user_id' => Auth::id()])->all())->save();

            // 日報コンテンツ情報を保存
            foreach ($request->validated()['report_contents'] as $report_content) {
                /* @var \App\Models\ReportContent $report_content_upsert */
                if ($report_content['id'] ?? null) {
                    $report_content_upsert = $report->report_contents()->updateOrCreate(
                        [
                            'id' => $report_content['id'],
                        ],
                        [
                            'description' => $report_content['description'] ?? null,
                            'is_complaint' => $report_content['is_complaint'],
                            'title' => $report_content['title'] ?? null,
                            'client_id' => $report_content['client_id'] ?? null,
                            'branch_id' => $report_content['branch_id'] ?? null,
                            'participants' => $report_content['participants'] ?? null,
                            'sales_method_id' => $report_content['sales_method_id'] ?? null,
                            'product_description' => $report_content['product_description'] ?? null,
                        ],
                    );
                } else {
                    $report_content_upsert = $report->report_contents()->create([
                        'type' => $report_content['type'],
                        'description' => $report_content['description'] ?? null,
                        'is_complaint' => $report_content['is_complaint'],
                        'title' => $report_content['title'] ?? null,
                        'client_id' => $report_content['client_id'] ?? null,
                        'branch_id' => $report_content['branch_id'] ?? null,
                        'participants' => $report_content['participants'] ?? null,
                        'sales_method_id' => $report_content['sales_method_id'] ?? null,
                        'product_description' => $report_content['product_description'] ?? null,
                    ]);
                }


                // 商材評価情報を保存
                $report_content_upsert->products()->detach();
                foreach ($report_content['product_evaluation'] as $product_evaluation) {
                    $report_content_upsert->products()->attach(
                        $product_evaluation["product_id"],
                        ['evaluation_id' => $product_evaluation["evaluation_id"]],
                    );
                }
            }

            // 日報コンテンツ情報の削除
            //  複数削除を行うとdeletedイベントが実行されないためeachで処理
            $report->report_contents()->whereIn('id', $request->validated()['_delete_report_content_ids'])->each(function ($report_content) {
                $report_content->delete();
            });
        });

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('reports.mine'),
        ]);

        return redirect()->route('reports.show', ['report' => $report->id]);
    }

    /**
     * 日報削除
     *
     * @param \App\Models\Report $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return redirect()->route('reports.mine');
    }
}
