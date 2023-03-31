<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportContent;
use App\Models\ReportContentProduct;
use App\Models\SalesMethod;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * 営業日報
 * @package App\Admin\Controllers
 */
class ReportContentSalesController extends BaseController
{
    /**
     * パラメータで指定された日報IDに対応するモデル
     * @var \App\Models\Report
     */
    private Report $report;

    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = '営業日報';

        // モデルクラス
        $this->model = ReportContent::class;

        // コンストラクタで日報情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから日報IDを取得
            $report_id = request()->get('report_id');

            // それ以外のアクションでは日報コンテンツIDから日報IDを取得
            if (!$report_id) {
                $report_contents_id = request()->route()->parameter('report_content');
                $report_id = ReportContent::find($report_contents_id)->{'report_id'};
            }

            $this->report = Report::find($report_id);
            return $next($request);
        });
    }

    /**
     * Make a show builder.
     *
     * @param $id
     * @return \Encore\Admin\Show
     */
    protected function detail($id): Show
    {
        $show = new Show(
            $this->modelClass()::findOrFail($id)->load(['products.pivot.evaluation'])
        );

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
                $tools->disableDelete();
            });

        $show->field('id', $this->trans("Id", "report-contents-sales"));
        $show->field('client.name', "会社名")
            ->unescape()
            ->as(function ($content) {
                // 削除済みであればリンクなし
                if ($this->client->deleted_at) {
                    return $content;
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client_id]),
                    $content
                );
            });
        $show->field('branch.name', "営業所名");
        $show->field('participants', $this->trans("Participants", "report-contents-sales"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('description', $this->trans("Description", "report-contents-sales"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('sales_method.name', $this->trans("sales_methods"));
        $show->field('is_complaint', $this->trans("Is complaint", "report-contents-sales"))
            ->as(function ($content) {
                return $content ? 'はい' : 'いいえ';
            });
        $show->field('products', $this->trans("report_content_product", "report-contents-sales"))
            ->unescape()
            ->as(function (Collection $products) {
                return $products->map(function ($product) {
                    return sprintf(
                        '%s : %s (%s)',
                        htmlspecialchars($product->name),
                        htmlspecialchars($product->pivot->evaluation->grade),
                        htmlspecialchars($product->pivot->evaluation->label)
                    );
                })->implode("<br>");
            });
        $show->field('created_at', $this->trans("Created at", "report-contents-sales"));
        $show->field('updated_at', $this->trans("Updated at", "report-contents-sales"));
        $show->field('deleted_at', $this->trans("Deleted at", "report-contents-sales"));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        $form = new Form($this->modelClass());

        $form->tools(function ($tools) {
            $tools->disableList();
            // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
            $tools->disableDelete();
        });

        // 論理削除済みの会社が指定されている場合はリストに含むようにする
        $clients = new Client();
        $branches = new Branch();
        if ($form->isEditing()) {
            $report_content_id = request()->route()->parameter('report_content');
            $report_content = ReportContent::find($report_content_id);

            $clients = $clients
                ->withTrashed()->where(function (Builder $query) use ($report_content) {
                    $query->whereNull("deleted_at")->orWhere("id", $report_content->client_id);
                });

            $branches = $branches
                ->withTrashed()
                ->where("client_id", $report_content->client_id)
                ->where(function (Builder $query) use ($report_content) {
                    $query->whereNull("deleted_at")->orWhere("id", $report_content->branch_id);
                });
        }

        // 会社選択 営業所選択をカスケードする
        $form->select('client_id', "会社")
            ->options($clients->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:clients,id"])
            ->updateRules(['required', "exists:clients,id"])
            ->load('branch_id', route('admin.api.branches.index'), "id", "name");

        // 営業所選択 作成
        if ($form->isCreating()) {
            $form->select('branch_id', "営業所")
                ->creationRules(['nullable', "exists:branches,id"])
                ->updateRules(['nullable', "exists:branches,id"]);
        }

        // 営業所選択 編集
        if ($form->isEditing()) {
            $form->select('branch_id', "営業所")
                ->options($branches->pluck("name", "id"))
                ->creationRules(['nullable', "exists:branches,id"])
                ->updateRules(['nullable', "exists:branches,id"]);
        }

        $form->textarea('participants', $this->trans("Participants", "report-contents-sales"));
        $form->textarea('description', $this->trans("Description", "report-contents-sales"));
        $form->switch('is_complaint', $this->trans("Is complaint", "report-contents-sales"))
            ->states([
                'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
                'on' => ['value' => 1, 'text' => 'はい', 'color' => 'danger'],
            ]);

        $form->select('sales_method_id', $this->trans("sales_methods"))
            ->options(SalesMethod::all()->pluck("name", "id"))
            ->required()
            ->creationRules(["exists:sales_methods,id"])
            ->updateRules(["exists:sales_methods,id"]);

        // 商材評価リストを取得
        $evaluations = Evaluation::get()->mapWithKeys(function ($evaluation) {
            return [$evaluation->id => $evaluation->grade . " " . $evaluation->label];
        });

        // 現在の評価を取得
        $report_content_product = ReportContentProduct::where("report_content_id", request()->route()->parameter('report_content'))->get();

        // 取材ごとに評価セレクタを設置
        foreach (Product::all() as $product) {
            $product_evaluation = $report_content_product->firstWhere("product_id", $product->id);

            $form
                ->select(
                    'product_evaluation.' . $product->id,
                    $this->trans("report_content_product") . " " . $product->name
                )
                ->options($evaluations)
                ->default($product_evaluation ? $product_evaluation["evaluation_id"] : null);
        }

        $form->hidden('type')->value('sales');
        $form->hidden('report_id')->value($this->report->id);

        // 保存後の処理
        $form->saved(function (Form $form) {
            // 商材評価の値を保存
            $form->model()->products()->detach();
            foreach (request()->get('product_evaluation') as $product_id => $evaluation_id) {
                if ($evaluation_id) {
                    $form->model()->products()->attach(
                        $product_id,
                        ['evaluation_id' => $evaluation_id]
                    );
                }
            }

            // トースト表示
            admin_toastr(trans('admin.save_succeeded'));

            // リダイレクト
            return redirect()->route('admin.report-contents-sales.show', ['report_content' => $form->model()->id]);
        });

        return $form;
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content): Content
    {
        // パンくずリストを書き換え
        return parent::show($id, $content)->breadcrumb(
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => $this->modelClass()->find($id)->client->name]
        );
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content): Content
    {
        // パンくずリストを書き換え
        return parent::edit($id, $content)->breadcrumb(
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => $this->modelClass()->find($id)->client->name]
        );
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content): Content
    {
        // パンくずリストを書き換え
        return parent::create($content)->breadcrumb(
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => "作成"]
        );
    }
}
