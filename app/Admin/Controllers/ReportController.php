<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\Evaluation;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportContentProduct;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\MessageBag;

/**
 * 日報
 * @package App\Admin\Controllers
 */
class ReportController extends BaseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = '日報';

        // モデルクラス
        $this->model = Report::class;

        // パンくずリストに表示するカラム
        $this->breadcrumb_display_column = "title";
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid($this->modelClass());

        // リレーション追加
        $grid->model()
            ->withExists([
                'report_contents_sales',
                'report_contents_work',
            ])
            ->withCount([
                'report_content_likes',
                'report_comments',
                'report_visitors',
            ]);

        $grid->header(function () {
            return $this->makeHeader([
                "最近更新されたものから順に表示されます。",
                "メンバーはすべての日報を閲覧できますが、自分が作成した日報のみ編集・削除できます。",
                "非公開に設定された日報は作成者以外のユーザは閲覧できません。日報に含まれる商材の評価は集計に含まれなくなります。",
                "日報を削除すると営業日報に含まれる商材の評価も削除され集計に含まれなくなります。",
            ]);
        });

        $grid->column('id', $this->trans("Id", "reports"));
        $grid->column('date', $this->trans("Date", "reports"));

        $grid->column('user.name', "作成者")->display(function ($user_name, $column) {
            // 削除済みであればリンクなし
            if ($this->user->deleted_at) {
                return $user_name;
            }

            return $column->link(function () {
                return route('admin.users.show', ['user' => $this->user_id]);
            }, "_parent");
        });

        $grid->column('report_type', "日報タイプ")
            ->display(function () {
                $types = [];
                if ($this->report_contents_sales_exists) {
                    $types[] = "営業日報";
                }
                if ($this->report_contents_work_exists) {
                    $types[] = "業務日報";
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    route("admin.reports.show", ["report" => $this->id]),
                    implode("・", $types)
                );
            });

        $grid->column('report_content_likes_count', $this->trans("report_content_likes"));
        $grid->column('report_comments_count', $this->trans("report_comments"));
        $grid->column('report_visitors_count', $this->trans("report_visitors"));

        $grid->column('created_at', $this->trans("Created at", "reports"));
        $grid->column('updated_at', $this->trans("Updated at", "reports"));

        $grid->column('is_private', $this->trans("is_private", "reports"))->switch([
            'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
            'on' => ['value' => 1, 'text' => 'はい', 'color' => 'warning'],
        ]);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id): Show
    {
        $show = new Show(
            $this->modelClass()
                ::findOrFail($id)
                ->load([
                    'report_visitors.user',
                ])
                ->loadCount([
                    'report_content_likes',
                    'report_comments',
                    'report_visitors'
                ])
        );

        $show->field('id', $this->trans("Id", "reports"));
        // showではシリアライズされていないデータが出力されるため文字列として日付を出力するアクセサから取得
        $show->field('date_string', $this->trans("Date", "reports"));
        $show->field('user.name', "作成者")
            ->unescape()
            ->as(function ($content) {
                // 削除済みであればリンクなし
                if ($this->user->deleted_at) {
                    return $content;
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    route("admin.users.show", ["user" => $this->user_id]),
                    $content
                );
            });
        $show->field('is_private', $this->trans("is_private", "reports"))
            ->as(function ($content) {
                return $content ? 'はい' : 'いいえ';
            });

        $show->field('report_content_likes_count', $this->trans("report_content_likes"));
        $show->field('report_comments_count', $this->trans("report_comments"));
        $show->field('report_visitors_count', $this->trans("report_visitors_count"));
        $show->field('report_visitors', $this->trans("report_visitors"))
            ->unescape()
            ->as(function (Collection $report_visitors) {
                return $report_visitors->map(function ($report_visitor) {                // 削除済みであればリンクなし
                    if ($report_visitor->user->deleted_at) {
                        return htmlspecialchars($report_visitor->user->name);
                    }

                    return sprintf(
                        '<a href="%s">%s</a>',
                        route("admin.users.show", ["user" => $report_visitor->user_id]),
                        htmlspecialchars($report_visitor->user->name)
                    );
                })->implode(", ");
            });
        $show->field('created_at', $this->trans("Created at", "reports"));
        $show->field('updated_at', $this->trans("Updated at", "reports"));
        $show->field('deleted_at', $this->trans("Deleted at", "reports"));

        // 営業日報
        $show->report_contents_sales($this->trans("report_contents_sales"), function ($grid) use ($id) {
            $grid->resource('/admin/report-contents-sales');
            $grid->disablePagination();

            // リレーション追加
            $grid->model()->withCount(['report_content_likes']);

            $grid->column('id', $this->trans("id", "report_contents"));

            $grid->column('client.name', '会社名')->display(function ($client_name, $column) {
                // 削除済みであればリンクなし
                if ($this->client->deleted_at) {
                    return $client_name;
                }

                return $column->link(function () {
                    return route("admin.clients-" . $this->client->client_type_id . ".show", ["client" => $this->client_id]);
                }, "_parent");
            });

            $grid->column('branch.name', '営業所名');

            $grid->column('participants', $this->trans("participants", "report_contents_sales"))
                ->display(function ($participants) {
                    return preg_replace('/[\s]+/', ' ', $participants);
                });

            $grid->column('description', $this->trans("description", "report_contents_sales"))->limit(64);
            $grid->column('is_complaint', $this->trans("is_complaint", "report_contents_sales"))
                ->replace([0 => 'いいえ', 1 => 'はい'])
                ->label([
                    "0" => 'success', "1" => 'danger',
                ]);
            $grid->column('report_content_likes_count', $this->trans("report_content_likes"));
            $grid->column('created_at', $this->trans("Created at", "report_contents_sales"));
            $grid->column('updated_at', $this->trans("Updated at", "report_contents_sales"));
        });

        // 業務日報
        $show->report_contents_work($this->trans("report_contents_work"), function ($grid) {
            $grid->resource('/admin/report-contents-work');
            $grid->disablePagination();

            // リレーション追加
            $grid->model()->withCount(['report_content_likes']);

            $grid->column('id', $this->trans("id", "report_contents"));
            $grid->column('title', $this->trans("title", "report_contents_work"))->limit(32);
            $grid->column('description', $this->trans("description", "report_contents_work"))->limit(64);
            $grid->column('is_complaint', $this->trans("is_complaint", "report_contents_sales"))
                ->replace([0 => 'いいえ', 1 => 'はい'])
                ->label([
                    "0" => 'success', "1" => 'danger',
                ]);
            $grid->column('report_content_likes_count', $this->trans("report_content_likes"));
            $grid->column('created_at', $this->trans("Created at", "report_contents_work"));
            $grid->column('updated_at', $this->trans("Updated at", "report_contents_work"));

        });

        // コメント
        $show->report_comments($this->trans("report_comments"), function ($grid) {
            $grid->resource('/admin/report-comments');
            $grid->disablePagination();

            $grid->column('id', $this->trans("id", "report_comments"));
            $grid->column('user.name', $this->trans("name", "users"))->link(
                function () {
                    return route("admin.users.show", ["user" => $this->user_id]);
                },
                "_parent"
            );
            $grid->column('comment', $this->trans("comment", "report_comments"))->limit(64);
            $grid->column('created_at', $this->trans("Created at", "report_comments"));
            $grid->column('updated_at', $this->trans("Updated at", "report_comments"));
        });


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form(): Form
    {
        $form = new Form($this->modelClass());

        $form->date('date', $this->trans("Date", "reports"))->default(date('Y-m-d'))->required();

        // 論理削除済みのメンバーが指定されている場合はリストに含むようにする
        $users = new User();
        if ($form->isEditing()) {
            $report_id = request()->route()->parameter('report');
            $report = Report::find($report_id);

            $users = $users
                ->withTrashed()->where(function (Builder $query) use ($report) {
                    $query->whereNull("deleted_at")->orWhere("id", $report->user_id);
                });
        }

        $form->select('user_id', "作成者")
            ->options($users->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);

        $form->switch('is_private', $this->trans("is_private", "reports"))
            ->states([
                'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
                'on' => ['value' => 1, 'text' => 'はい', 'color' => 'warning'],
            ]);

        // 新規作成時は営業日報または業務日報のいずれかを同時に作成させる
        if ($form->isCreating()) {
            $form->divider();
            $form->html('<div style="margin-top: 8px;">新規ボタンから営業日報を追加してください。</div>', $this->trans('report_contents_sales'));

            // 営業日報
            $form->hasMany(
                'report_contents_sales',
                "",
                function (Form\NestedForm $nestedForm) {
                    $nestedForm->select('client_id', "会社")
                        ->options(Client::get()->pluck("name", "id"))
                        ->creationRules(['required', "exists:clients,id"])
                        ->updateRules(['required', "exists:clients,id"])
                        ->load('branch_id', route('admin.api.branches.index'), "id", "name");
                    $nestedForm->select('branch_id', "営業所")
                        ->creationRules(['nullable', "exists:branches,id"])
                        ->updateRules(['nullable', "exists:branches,id"]);
                    $nestedForm->textarea('participants', $this->trans("Participants", "report-contents-sales"));
                    $nestedForm->textarea('description', $this->trans("Description", "report-contents-sales"));
                    $nestedForm->switch('is_complaint', $this->trans("Is complaint", "report-contents-sales"))
                        ->states([
                            'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
                            'on' => ['value' => 1, 'text' => 'はい', 'color' => 'danger'],
                        ]);

                    // 商材評価リストを取得
                    $evaluations = Evaluation::get()->mapWithKeys(function ($evaluation) {
                        return [$evaluation->id => $evaluation->grade . " " . $evaluation->label];
                    });

                    // 現在の評価を取得
                    $report_content_product = ReportContentProduct::where("report_content_id", request()->route()->parameter('report_content'))->get();

                    // 取材ごとに評価セレクタを設置
                    foreach (Product::all() as $product) {
                        $product_evaluation = $report_content_product->firstWhere("product_id", $product->id);

                        $nestedForm
                            ->select(
                                'product_evaluation.' . $product->id,
                                $this->trans("report_content_product") . " " . $product->name
                            )
                            ->options($evaluations)
                            ->default($product_evaluation ? $product_evaluation["evaluation_id"] : null);
                    }

                    $nestedForm->hidden('type')->value('sales');
                }
            );

            $form->divider();
            $form->html('<div style="margin-top: 8px;">新規ボタンから業務日報を追加してください。</div>', $this->trans('report_contents_work'));

            // 業務日報
            $form->hasMany(
                'report_contents_work',
                "",
                function (Form\NestedForm $nestedForm) {
                    $nestedForm->text('title', $this->trans("title", "report_contents_work"))->required();
                    $nestedForm->textarea('description', $this->trans("Description", "report_contents_work"));
                    $nestedForm->switch('is_complaint', $this->trans("Is complaint", "report_contents_work"))
                        ->states([
                            'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
                            'on' => ['value' => 1, 'text' => 'はい', 'color' => 'danger'],
                        ]);

                    $nestedForm->hidden('type')->value('work');
                }
            );
        }

        // 保存前の処理
        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                // 営業日報または業務日報が設定されていなければ戻す
                $report_contents_sales_count = collect($form->report_contents_sales)->where("_remove_", "!=", 1)->count();
                $report_contents_work_count = collect($form->report_contents_work)->where("_remove_", "!=", 1)->count();

                if ($report_contents_sales_count === 0 && $report_contents_work_count === 0) {
                    $warning = new MessageBag([
                        'title' => '営業日報または業務日報のいずれかを設定してください。',
                    ]);

                    return back()->with(compact('warning'))->withInput();
                }
            }
        });

        // 保存後の処理
        $form->saved(function (Form $form) {
            if (request()->input("report_contents_sales")) {
                // 3段階以上のネストを保存できないため商材評価を手動で保存

                // 送信された値から保存する商材評価の値を生成
                $product_evaluations = [];
                foreach (request()->input("report_contents_sales") as $report_contents_sale) {
                    $product_evaluation = [];

                    foreach ($report_contents_sale as $key => $value) {
                        if (preg_match("/^product_evaluation.([0-9]+)$/", $key, $matches)) {
                            if (!empty($value)) {
                                $product_evaluation[$matches[1]] = ['evaluation_id' => $value];
                            }
                        }
                    }

                    $product_evaluations[] = $product_evaluation;
                }

                // 生成された営業日報に商材評価を保存
                $form->model()->report_contents_sales()->each(function ($report_contents_sale, $index) use ($product_evaluations) {
                    $report_contents_sale->products()->sync($product_evaluations[$index]);
                });
            }
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
        $report = $this->modelClass()->find($id);

        // パンくずリストを書き換え
        return parent::show($id, $content)->breadcrumb(
            ['text' => $this->title, 'url' => '/' . request()->segment(2)],
            ['text' => $report->user->name . " (" . $report->date_string . ')'],
        );
    }
}
