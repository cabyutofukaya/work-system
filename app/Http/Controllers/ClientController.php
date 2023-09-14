<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowClient;
use App\Http\Requests\StoreClient;
use App\Http\Requests\UpdateClient;
use App\Models\Client;
use App\Models\ClientTypeRestaurant;
use App\Models\ClientTypeTaxibus;
use App\Models\ClientTypeTravel;
use App\Models\ClientTypeTruck;
use App\Models\Genre;
use App\Models\LatestEvaluation;
use App\Models\Product;
use App\Models\ReportContent;
use App\Models\SalesTodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Inertia\ResponseFactory;
use Lang;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param $client_type_id
     * @return \Inertia\Response|\Inertia\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request, $client_type_id): Response|ResponseFactory
    {
        // GETメソッドではInertiaによるバリデーションエラー処理が行われないため
        // リダイレクトを行わずエラーをhttpボディとして出力
        $validator = Validator::make($request->all(), (new ShowClient)->rules());
        if ($validator->fails()) {
            response(implode("\n", $validator->errors()->all()), 422)->send();
            exit;
        }
        $validated = $validator->validated();

        // client_type_idのバリデーション
        if (!in_array($client_type_id, array_keys(config("const.client_types")))) {
            abort(404);
        }

        $clients = Client::where("client_type_id", $client_type_id);

        // ワード検索
        if ($request->filled('word')) {
            // ワードを空白文字で分割してAND検索
            foreach (preg_split('/[\p{Z}\p{Cc}]++/u', $validated["word"], -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $clients->where(function ($query) use ($word) {
                    $query->whereLike('name', $word)
                        ->orWhereLike('name_kana', $word)
                        ->orWhereLike('tel', $word)
                        ->orWhereLike('fax', $word);

                    // 検索ワードが数字のみであれば電話番号の数字部分のみから検索する
                    if (preg_match('/^[\-\d]+$/', $word)) {
                        $query
                            ->orWhereRaw('replace(tel, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))])
                            ->orWhereRaw('replace(fax, "-", "") like concat("%", ?, "%")', [str_replace('-', '', str_replace('-', '', $word))]);
                    }
                });
            }
        }

        // 所在地検索
        if ($request->filled('address')) {
            $clients
                ->where(DB::raw('CONCAT(prefecture,address)'), "like", '%' . addcslashes($validated["address"], '%_\\') . '%')
                // 営業所の所在地からも検索
                // ->whereHas('branches', function ($query) use ($validated) {
                //     $query->where(DB::raw('CONCAT(prefecture,address)'), "like", '%' . addcslashes($validated["address"], '%_\\') . '%');
                // });
                ->orWhereHas('branches', function ($query) use ($validated) {
                    $query->where(DB::raw('CONCAT(prefecture,address)'), "like", '%' . addcslashes($validated["address"], '%_\\') . '%');
                });
        }

        // 営業エリア検索
        if ($request->filled('business_district')) {
            $clients->whereHas('business_districts', function ($query) use ($validated) {
                $query->where(DB::raw('CONCAT(prefecture,address)'), "like", '%' . addcslashes($validated["business_district"], '%_\\') . '%');
            });
        }

        // 固有情報 バス・タクシー会社
        if ($client_type_id === "taxibus") {
            // カテゴリー検索
            if ($request->filled('category')) {
                $clients->whereHas('client_type_' . $client_type_id, function (Builder $query) use ($validated) {
                    $query->where('category', $validated["category"]);
                });
            }

            // 各スイッチ要素を検索
            foreach (["has_dr_sightseeing", "has_dr_female", "has_dr_language_english", "has_dr_language_chinese", "has_dr_language_korean", "has_dr_language_other", "has_wheelchair", "has_baby_seat", "has_child_seat", "fee_child_seat", "has_junior_seat", "fee_junior_seat", "is_bus_association_member", "has_safety_mark"] as $key) {
                if ($request->boolean($key)) {
                    $clients->whereHas('client_type_' . $client_type_id, function (Builder $query) use ($key) {
                        $query->where($key, true);
                    });
                }
            }
        }

        // ジャンル検索
        if ($request->filled('genre_id')) {
            $clients->whereHas('genres', function (Builder $query) use ($validated) {
                // orderで指定したカラムにIntegrity constraint violationエラーが発生するためグローバルスコープを削除
                $query->withoutGlobalScope('order')->where('genre_id', $validated["genre_id"]);
            });
        }

        // 固有情報 保有車両
        if ($request->filled('vehicle')) {
            $clients->whereHas('vehicles', function ($query) use ($validated) {
                $query->whereLike('name', $validated["vehicle"]);
                $query->orWhereLike('description', $validated["vehicle"]);
            });
        }

        return inertia('Clients', [
            // 会社タイプ情報
            'client_type' => config("const.client_types." . $client_type_id),

            // 固有情報カラム名
            'client_type_column_names' => Lang::get('validation.attributes.client_type_' . $client_type_id),

            // ジャンルリスト
            'genres' => Genre::ofClientType($client_type_id)->get(["id", "name"]),

            // 会社リスト
            'clients' => $clients->paginate()->withQueryString(),

            // 検索フォームの初期値
            'form_params' => [
                'word' => $validated["word"] ?? null,
                'address' => $validated["address"] ?? null,
                'business_district' => $validated["business_district"] ?? null,
                'category' => $validated["category"] ?? null,
                'genre_id' => array_key_exists("genre_id", $validated) ? (int)$validated["genre_id"] : null,
                'vehicle' => $validated["vehicle"] ?? null,
                'has_dr_sightseeing' => $request->boolean("has_dr_sightseeing"),
                'has_dr_female' => $request->boolean("has_dr_female"),
                'has_dr_language_english' => $request->boolean("has_dr_language_english"),
                'has_dr_language_chinese' => $request->boolean("has_dr_language_chinese"),
                'has_dr_language_korean' => $request->boolean("has_dr_language_korean"),
                'has_dr_language_other' => $request->boolean("has_dr_language_other"),
                'has_wheelchair' => $request->boolean("has_wheelchair"),
                'has_baby_seat' => $request->boolean("has_baby_seat"),
                'has_child_seat' => $request->boolean("has_child_seat"),
                'has_junior_seat' => $request->boolean("has_junior_seat"),
                'is_bus_association_member' => $request->boolean("is_bus_association_member"),
                'has_safety_mark' => $request->boolean("has_safety_mark"),
            ],
        ]);
    }

    /**
     * 地図検索
     *
     * @param $client_type_id
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function map($client_type_id): Response|ResponseFactory
    {
        // client_type_idのバリデーション
        if (!in_array($client_type_id, array_keys(config("const.client_types")))) {
            abort(404);
        }

        // 会社情報を取得
        $clients = Client::where("client_type_id", $client_type_id);

        // 会社情報からマーカー情報を生成
        $markers = [];
        foreach ($clients->get() as $client) {
            if ($client->lat && $client->lng) {
                $markers[] = [
                    "position" => [
                        "lat" => (double)$client->lat,
                        "lng" => (double)$client->lng,
                    ],
                    "content" => $client->only(["id", "name", "prefecture", "address", "icon_image_url"])
                ];
            }
        }

        return inertia('ClientsMap', [
            'client_type' => config("const.client_types." . $client_type_id),
            'clients' => $clients,
            'markers' => $markers,
            // 位置情報初期値
            'location_default' => config("const.location_default"),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $client_type_id
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function create($client_type_id): Response|ResponseFactory
    {
        // client_type_idのバリデーション
        if (!in_array($client_type_id, array_keys(config("const.client_types")))) {
            abort(404);
        }

        $form_columns = [];

        // $inertia.form に設定する会社情報カラム
        $client_columns = collect((new Client)->getFillable())->flip()->except(["client_type_id", "image"])->keys();

        foreach ($client_columns as $column) {
            // 初期値としてすべてnullを設定する
            $form_columns[$column] = null;
        }

        // $inertia.form に設定する会社固有情報カラム
        $client_type_columns = [];

        if ($client_type_id === "taxibus") {
            $client_type_columns = collect((new ClientTypeTaxibus())->getFillable())->flip()->except(["client_id"])->keys();
        } else if ($client_type_id === "truck") {
            $client_type_columns = collect((new ClientTypeTruck())->getFillable())->flip()->except(["client_id"])->keys();
        } else if ($client_type_id === "restaurant") {
            $client_type_columns = collect((new ClientTypeRestaurant())->getFillable())->flip()->except(["client_id"])->keys();
        } else if ($client_type_id === "travel") {
            $client_type_columns = collect((new ClientTypeTravel())->getFillable())->flip()->except(["client_id"])->keys();
        }

        $form_columns["client_type_" . $client_type_id] = [];
        foreach ($client_type_columns as $column) {
            $form_columns["client_type_" . $client_type_id][$column] = null;
        }

      

        return inertia('ClientsCreate', [
            // 都道府県
            'prefectures' => config("const.prefectures"),
            // 会社タイプ
            'client_type' => config("const.client_types." . $client_type_id),
            // 会社情報テーブルカラム
            'form_columns' => $form_columns,
            // ジャンル
            'genres' => Genre::ofClientType($client_type_id)->get(["id", "name"]),
            // 商材
            'products' => Product::get(["id", "name"]),
            // 固有情報カラム名
            'client_type_column_names' => Lang::get('validation.attributes.client_type_' . $client_type_id),
            // 位置情報初期値
            'location_default' => config("const.location_default"),
             // 社内担当者リスト
             'charges' => User::ofClientType($client_type_id)->get(["id", "name"]),
         
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreClient $request
     * @param $client_type_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreClient $request, $client_type_id): RedirectResponse
    {
        $client = new Client;

        // 会社情報
        $client->fill($request->validated());

        // 会社タイプ
        $client->client_type_id = $client_type_id;

        if ($request->hasFile('_image')) {
            // 画像ファイルを取得
            $file = $request->file('_image');

            // ファイルをストレージに保存
            $client->image = $file->store('clients');
        }

        $client->save();

        // 会社タイプ固有情報
        if ($client_type_id === "taxibus" && $request->has("client_type_taxibus")) {
            $client->client_type_taxibus()->create($request->validated()["client_type_taxibus"]);
        } else if ($client_type_id === "truck" && $request->has("client_type_truck")) {
            $client->client_type_truck()->create($request->validated()["client_type_truck"]);
        } else if ($client_type_id === "restaurant" && $request->has("client_type_restaurant")) {
            $client->client_type_restaurant()->create($request->validated()["client_type_restaurant"]);
        } else if ($client_type_id === "travel" && $request->has("client_type_travel")) {
            $client->client_type_travel()->create($request->validated()["client_type_travel"]);
        }

        // ジャンル・商材
        // ファイルアップロードと同時に空の配列が送信された場合にキー自体が設定されなくなるためnull合体演算子で対応
        $client->genres()->sync($request->validated()["genre_ids"] ?? []);
        $client->products()->sync($request->validated()["product_ids"] ?? []);
        $client->users()->sync($request->validated()["charge_ids"] ?? []);

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $client_type_id]),
        ]);

        return redirect()->route('clients.show', [
            'client' => $client->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Client $client
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Client $client): Response|ResponseFactory
    {
        $client->load([
            'branches',
            'client_type_' . $client->client_type_id,
            'contact_persons',
            'genres',
            'products',
            'users',
        ]);

        if ($client->client_type_id === "taxibus") {
            $client->load([
                'business_districts',
                'vehicles_taxi',
                'vehicles_bus',
            ]);
        }

        // 更新日時を出力
        $client->makeVisible(['updated_at']);

        // 直近の営業ToDo
        $sales_todos = SalesTodo
            ::where("user_id", Auth::id())
            ->where("client_id", $client->id)
            ->where("is_completed", false)
            ->take(3)
            ->get(['id', 'scheduled_at', 'description', 'contact_person']);

        // 最近の営業日報
        $report_contents = ReportContent
            ::exceptPrivate()
            ->with(["report:id,user_id,date", "report.user:id,name"])
            ->where("type", "sales")
            ->where("client_id", $client->id)
            ->take(3)
            ->get(['id', 'report_id', 'type', 'description']);

        // 7月始まり年度別の訪問回数(営業日報数)
        $report_contents_count_by_fy = ReportContent
            ::with('report')
            ->where("type", "sales")
            ->where('client_id', $client->id)
            // 自分自身の日報を取得
            ->whereHas('report', function (Builder $query) {
                $query->where("user_id", auth()->id());
            })
            ->get()
            ->groupBy("report.fiscal_year")
            ->map(function ($fiscal_year) {
                return $fiscal_year->count();
            })
            // 連想配列の並び順がjson変換時に失われるためオブジェクトの配列にしてからソート
            ->map(function ($count, $fiscal_year) {
                return ["fiscal_year" => $fiscal_year, "count" => $count];
            })
            ->sortByDesc("fiscal_year")
            ->values();

        // 最近の商材評価
        $latest_evaluations = LatestEvaluation
            ::with([
                "product:id,name",
                "evaluation:id,grade,label",
                "report_content:id,report_id",
                "report_content.report:id,date,user_id",
                "report_content.report.user:id,name"
            ])
            ->where("client_id", $client->id)
            ->get();


        return inertia('ClientsShow', [
            // 都道府県
            'prefectures' => config("const.prefectures"),
            // 会社情報
            'client' => $client,
            // 固有情報カラム名
            'client_type_column_names' => Lang::get('validation.attributes.client_type_' . $client->client_type_id),
            // 直近の営業ToDo
            'sales_todos' => $sales_todos,
            // 最近の営業日報
            'report_contents' => $report_contents,
            // 7月始まり年度別の(訪問回数)営業日報数
            'report_contents_count_by_fy' => $report_contents_count_by_fy,
            // 最近の商材評価
            'latest_evaluations' => $latest_evaluations,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Client $client
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(Client $client): Response|ResponseFactory
    {
        $client->load([
            'client_type_' . $client->client_type_id,
            'genres',
            'products',
            'users',
        ]);


        // dd($client->products()->get()->pluck("id"));
        // dd($client->charges()->get()->pluck("id"));

        // dd(User::ofClientType($client->client_type_id)->get(["id", "name"]));

        return inertia('ClientsEdit', [
            // 都道府県
            'prefectures' => config("const.prefectures"),
            // 会社タイプ
            'client_type' => config("const.client_types." . $client->client_type_id),
            // 会社情報
            'client' => $client,
            // ジャンル情報
            'genre_ids' => $client->genres()->get()->pluck("id"),
            // 商材情報
            'product_ids' => $client->products()->get()->pluck("id"),
            // ジャンルリスト
            'genres' => Genre::ofClientType($client->client_type_id)->get(["id", "name"]),
            // 商材リスト
            'products' => Product::get(["id", "name"]),
            // 固有情報カラム名
            'client_type_column_names' => Lang::get('validation.attributes.client_type_' . $client->client_type_id),
            // 位置情報初期値
            'location_default' => config("const.location_default"),
            // 社内担当者リスト
            'charges' => User::ofClientType($client->client_type_id)->get(["id", "name"]),
             // 社内担当者リスト情報
             'charge_ids' => $client->charges()->get()->pluck("id"),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateClient $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateClient $request, Client $client): RedirectResponse
    {
        
        // 会社情報
        $client->fill($request->validated());

        if ($request->hasFile('_image')) {
            // 画像ファイルを取得
            $file = $request->file('_image');

            // ファイルをストレージに保存
            $client->image = $file->store('clients');
        }

        $client->save();

        // 会社タイプ固有情報
        if ($client->client_type_id === "taxibus" && $request->has("client_type_taxibus")) {
            $client->client_type_taxibus->fill($request->validated()["client_type_taxibus"])->save();
        } else if ($client->client_type_id === "truck" && $request->has("client_type_truck")) {
            $client->client_type_truck->fill($request->validated()["client_type_truck"])->save();
        } else if ($client->client_type_id === "restaurant" && $request->has("client_type_restaurant")) {
            $client->client_type_restaurant->fill($request->validated()["client_type_restaurant"])->save();
        } else if ($client->client_type_id === "travel" && $request->has("client_type_travel")) {
            $client->client_type_travel->fill($request->validated()["client_type_travel"])->save();
        }

        // ジャンル・商材
        // ファイルアップロードと同時に空の配列が送信された場合にキー自体が設定されなくなるためnull合体演算子で対応
        $client->genres()->sync($request->validated()["genre_ids"] ?? []);
        $client->products()->sync($request->validated()["product_ids"] ?? []);
        $client->users()->sync($request->validated()["charge_ids"] ?? []);

        // バックボタンの戻り先ページを設定
        $request->session()->flash('backButton', [
            "url" => route('client-types.clients.index', ['client_type' => $client->client_type_id]),
        ]);

        return redirect()->route('clients.show', ['client' => $client->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client_type_id = $client->client_type_id;

        $client->delete();

        // バックボタンの戻り先ページを設定
        request()->session()->flash('backButton', [
            "url" => route('home'),
        ]);

        return redirect()->route('client-types.clients.index', ['client_type' => $client_type_id]);
    }


     /**
     * Show the form for creating a new resource.
     *
     * @param $client_type_id
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function select(): Response|ResponseFactory
    {
        return inertia('ClientsSelect');
    }
}