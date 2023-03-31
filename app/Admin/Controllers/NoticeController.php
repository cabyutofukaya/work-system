<?php

namespace App\Admin\Controllers;

use App\Models\Notice;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;

/**
 * お知らせ
 * @package App\Admin\Controllers
 */
class NoticeController extends BaseController
{
    /**
     * constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = 'お知らせ';

        // モデルクラス
        $this->model = Notice::class;

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
                "最近作成されたものから順に表示されます。",
                "メンバーはすべてのお知らせを閲覧できますが、自分が作成したお知らせのみ編集・削除できます。",
            ]);
        });

        $grid->column('id', $this->trans('Id', 'notices'));

        $grid->column('user.name', "作成者")->display(function ($user_name, $column) {
            // 削除済みであればリンクなし
            if ($this->user->deleted_at) {
                return $user_name;
            }

            return $column->link(function () {
                return route('admin.users.show', ['user' => $this->user_id]);
            }, "_parent");
        });

        $grid->column('title', $this->trans('Title', 'notices'))
            ->limit(64)
            ->link(
                function () {
                    return route('admin.notices.show', ['notice' => $this->id]);
                },
                "_parent"
            );
        $grid->column('created_at', $this->trans('Created at', 'notices'));
        $grid->column('updated_at', $this->trans('Updated at', 'notices'));

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

        $show->field('id', $this->trans('Id', 'notices'));
        $show->field('user.name', "作成者")
            ->unescape()
            ->as(function ($content) {
                // 削除済みであればリンクなし
                if ($this->user->deleted_at) {
                    return $content;
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    route('admin.users.show', ['user' => $this->user_id]),
                    $content
                );
            });
        $show->field('title', $this->trans('Title', 'notices'));
        $show->field('description', $this->trans('description', 'notices'))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('created_at', $this->trans('Created at', 'notices'));
        $show->field('updated_at', $this->trans('Updated at', 'notices'));

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

        // 論理削除済みのメンバーが指定されている場合はリストに含むようにする
        $users = new User();
        if ($form->isEditing()) {
            $notice_id = request()->route()->parameter('notice');
            $notice = Notice::find($notice_id);

            $users = $users
                ->withTrashed()->where(function (Builder $query) use ($notice) {
                    $query->whereNull("deleted_at")->orWhere("id", $notice->user_id);
                });
        }

        $form->select('user_id', "作成者")
            ->options($users->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);

        $form->text('title', $this->trans('Title', 'notices'));
        $form->textarea('description', $this->trans('description', 'notices'));

        return $form;
    }
}
