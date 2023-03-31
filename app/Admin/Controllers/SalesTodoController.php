<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\SalesTodo;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\MessageBag;

/**
 * 営業ToDoリスト
 *
 * @package App\Admin\Controllers
 */
class SalesTodoController extends BaseController
{
    /**
     * constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = '営業ToDoリスト';

        // モデルクラス
        $this->model = SalesTodo::class;

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

        $grid->header(function () {
            return $this->makeHeader([
                "日時の近いものから表示されます。",
                "対応済みの項目は一覧の末尾に移動されます。",
                "メンバーは自分の作成したToDoのみ閲覧・編集・削除できます。",
            ]);
        });

        $grid->column('id', $this->trans("Id", "sales_todos"));

        $grid->column('user.name', "作成者")->display(function ($user_name, $column) {
            // 削除済みであればリンクなし
            if ($this->user->deleted_at) {
                return $user_name;
            }

            return $column->link(function () {
                return route('admin.users.show', ['user' => $this->user_id]);
            }, "_parent");
        });

        $grid->column('client.name', '会社名')->display(function ($client_name, $column) {
            // 削除済みであればリンクなし
            if ($this->client->deleted_at) {
                return $client_name;
            }

            return $column->link(function () {
                return route("admin.clients-" . $this->client->client_type_id . ".show", ["client" => $this->client_id]);
            }, "_parent");
        });

        $grid->column('scheduled_at', $this->trans("Scheduled at", "sales_todos"));
        $grid->column('contact_person', $this->trans("Contact person", "sales_todos"));
        $grid->column('description', $this->trans("Description", "sales_todos"))
            ->limit(64);
        $grid->column('is_completed', $this->trans("Is completed", "sales_todos"))
            ->replace([0 => 'いいえ', 1 => 'はい'])
            ->label([
                "0" => 'default', "1" => 'success',
            ]);
        $grid->column('created_at', $this->trans("Created at", "sales_todos"));
        $grid->column('updated_at', $this->trans("Updated at", "sales_todos"));
        $grid->column('deleted_at', $this->trans("Deleted at", "sales_todos"));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param $id
     * @return Show
     */
    protected function detail($id): Show
    {
        $show = new Show($this->modelClass()::findOrFail($id));

        $show->field('id', $this->trans("Id", "sales_todos"));
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
        $show->field('client.name', "会社名")
            ->unescape()
            ->as(function ($content) {
                // 削除済みであればリンクなし
                if ($this->client->deleted_at) {
                    return $content;
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    route("admin.clients-" . $this->client->client_type_id . ".show", ["client" => $this->client_id]),
                    $content
                );
            });
        $show->field('scheduled_at', $this->trans("Scheduled at", "sales_todos"));
        $show->field('contact_person', $this->trans("Contact person", "sales_todos"));
        $show->field('description', $this->trans("Description", "sales_todos"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('sales_todo_participants', $this->trans("sales_todo_participants"))
            ->unescape()
            ->as(function (Collection $sales_todo_participants) {
                return $sales_todo_participants->map(function ($sales_todo_participant) {
                    return sprintf(
                        '<a href="%s">%s</a>',
                        route("admin.users.show", ["user" => $sales_todo_participant->user_id]),
                        htmlspecialchars($sales_todo_participant->user->name)
                    );
                })->implode(", ");
            });
        $show->field('is_completed', $this->trans("Is completed", "sales_todos"))
            ->as(function ($content) {
                return $content ? 'はい' : 'いいえ';
            });
        $show->field('created_at', $this->trans("Created at", "sales_todos"));
        $show->field('updated_at', $this->trans("Updated at", "sales_todos"));
        $show->field('deleted_at', $this->trans("Deleted at", "sales_todos"));

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

        // 論理削除済みのメンバーが指定されている場合はリストに含むようにする
        $users = new User();
        if ($form->isEditing()) {
            $sales_todo_id = request()->route()->parameter('sales_todo');
            $sales_todo = SalesTodo::find($sales_todo_id);

            $users = $users
                ->withTrashed()->where(function (Builder $query) use ($sales_todo) {
                    $query->whereNull("deleted_at")->orWhere("id", $sales_todo->user_id);
                });
        }

        $form->select('user_id', "作成者")
            ->options($users->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);

        // 論理削除済みの会社が指定されている場合はリストに含むようにする
        $clients = new Client();
        if ($form->isEditing()) {
            $sales_todo_id = request()->route()->parameter('sales_todo');
            $sales_todo = SalesTodo::find($sales_todo_id);

            $clients = $clients
                ->withTrashed()
                ->where(function (Builder $query) use ($sales_todo) {
                    $query->whereNull("deleted_at")->orWhere("id", $sales_todo->client_id);
                });
        }

        $form->select('client_id', "会社")
            ->options($clients->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:clients,id"])
            ->updateRules(['required', "exists:clients,id"]);

        $form->datetime('scheduled_at', $this->trans("Scheduled at", "sales_todos"));
        $form->text('contact_person', $this->trans("Contact person", "sales_todos"));
        $form->textarea('description', $this->trans("Description", "sales_todos"));

        $form->hasMany(
            'sales_todo_participants',
            $this->trans('sales_todo_participants', 'sales_todo_participants'),
            function (Form\NestedForm $form) {
                $form->select('user_id', $this->trans('sales_todo_participants', 'sales_todo_participants'))
                    ->required()
                    ->options(User::get()->pluck("name", "id"));
            }
        );
        $form->divider();

        $form->switch('is_completed', $this->trans("Is completed", "sales_todos"))
            ->states([
                'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'default'],
                'on' => ['value' => 1, 'text' => 'はい', 'color' => 'success'],
            ]);

        $form->saving(function (Form $form) {
            // 社内担当者に同じメンバーが指定されていればエラー
            if (collect($form->sales_todo_participants)->where("_remove_", "0")->pluck("user_id")->duplicates()->isNotEmpty()) {
                $warning = new MessageBag([
                    'title'   => '社内担当者に同じメンバーが設定されています。',
                ]);
                return back()->with(compact('warning'));
            }

            return true;
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
            ['text' => $this->title, 'url' => route('admin.sales-todos.index')],
            ['text' => $this->modelClass()->find($id)->user->name . " (" . $this->modelClass()->find($id)->scheduled_at . ")"]
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
            ['text' => $this->title, 'url' => route('admin.reports.index')],
            ['text' => $this->modelClass()->find($id)->user->name . " (" . $this->modelClass()->find($id)->scheduled_at . ")", 'url' => route('admin.sales-todos.show', ['sales_todo' => $id])],
            ['text' => "編集"]
        );
    }
}
