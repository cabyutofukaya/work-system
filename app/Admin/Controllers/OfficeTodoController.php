<?php

namespace App\Admin\Controllers;

use App\Models\OfficeTodo;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\MessageBag;

/**
 * 社内ToDoリスト
 *
 * @package App\Admin\Controllers
 */
class OfficeTodoController extends BaseController
{
    /**
     * constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = '社内ToDoリスト';

        // モデルクラス
        $this->model = OfficeTodo::class;

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

        $grid->column('id', $this->trans("Id", "office_todos"));

        $grid->column('user.name', "作成者")->display(function ($user_name, $column) {
            // 削除済みであればリンクなし
            if ($this->user->deleted_at) {
                return $user_name;
            }

            return $column->link(function () {
                return route('admin.users.show', ['user' => $this->user_id]);
            }, "_parent");
        });

        $grid->column('scheduled_at', $this->trans("Scheduled at", "office_todos"));
        $grid->column('title', $this->trans("Title", "office_todos"));
        $grid->column('is_completed', $this->trans("Is completed", "office_todos"))
            ->replace([0 => 'いいえ', 1 => 'はい'])
            ->label([
                "0" => 'default', "1" => 'success',
            ]);
        $grid->column('created_at', $this->trans("Created at", "office_todos"));
        $grid->column('updated_at', $this->trans("Updated at", "office_todos"));
        $grid->column('deleted_at', $this->trans("Deleted at", "office_todos"));

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

        $show->field('id', $this->trans("Id", "office_todos"));
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
        $show->field('scheduled_at', $this->trans("Scheduled at", "office_todos"));
        $show->field('title', $this->trans("Title", "office_todos"));
        $show->field('description', $this->trans("Description", "office_todos"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('office_todo_participants', $this->trans("office_todo_participants"))
            ->unescape()
            ->as(function (Collection $office_todo_participants) {
                return $office_todo_participants->map(function ($office_todo_participant) {
                    return sprintf(
                        '<a href="%s">%s</a>',
                        route("admin.users.show", ["user" => $office_todo_participant->user_id]),
                        htmlspecialchars($office_todo_participant->user->name)
                    );
                })->implode(", ");
            });
        $show->field('is_completed', $this->trans("Is completed", "office_todos"))
            ->as(function ($content) {
                return $content ? 'はい' : 'いいえ';
            });
        $show->field('created_at', $this->trans("Created at", "office_todos"));
        $show->field('updated_at', $this->trans("Updated at", "office_todos"));
        $show->field('deleted_at', $this->trans("Deleted at", "office_todos"));

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
            $office_todo_id = request()->route()->parameter('office_todo');
            $office_todo = OfficeTodo::find($office_todo_id);

            $users = $users
                ->withTrashed()->where(function (Builder $query) use ($office_todo) {
                    $query->whereNull("deleted_at")->orWhere("id", $office_todo->user_id);
                });
        }

        $form->select('user_id', "作成者")
            ->options($users->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);

        $form->datetime('scheduled_at', $this->trans("Scheduled at", "office_todos"));
        $form->text('title', $this->trans("Title", "office_todos"));
        $form->textarea('description', $this->trans("Description", "office_todos"));

        $form->hasMany(
            'office_todo_participants',
            $this->trans('office_todo_participants', 'office_todo_participants'),
            function (Form\NestedForm $form) {
                $form->select('user_id', $this->trans('office_todo_participants', 'office_todo_participants'))
                    ->required()
                    ->options(User::get()->pluck("name", "id"));
            }
        );
        $form->divider();

        $form->switch('is_completed', $this->trans("Is completed", "office_todos"))
            ->states([
                'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'default'],
                'on' => ['value' => 1, 'text' => 'はい', 'color' => 'success'],
            ]);

        $form->saving(function (Form $form) {
            // 社内担当者に同じメンバーが指定されていればエラー
            if (collect($form->office_todo_participants)->where("_remove_", "0")->pluck("user_id")->duplicates()->isNotEmpty()) {
                $warning = new MessageBag([
                    'title'   => '社内担当者に同じメンバーが設定されています。',
                ]);
                return back()->with(compact('warning'));
            }

            return true;
        });

        return $form;
    }
}
