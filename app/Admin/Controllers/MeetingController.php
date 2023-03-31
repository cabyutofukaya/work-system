<?php

namespace App\Admin\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * 議事録
 * @package App\Admin\Controllers
 */
class MeetingController extends BaseController
{
    /**
     * constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = '議事録';

        // モデルクラス
        $this->model = Meeting::class;

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
            ->withCount([
                'meeting_likes',
                'meeting_comments',
                'meeting_visitors',
            ]);

        $grid->header(function () {
            return $this->makeHeader([
                "最近開催されたものから順に表示されます。",
                "メンバーはすべての議事録を閲覧できますが、自分が作成した議事録のみ編集・削除できます。",
            ]);
        });

        $grid->column('id', $this->trans("Id", "meetings"));
        $grid->column('started_at', $this->trans("Started at", "meetings"));
        $grid->column('title', $this->trans("Title", "meetings"))
            ->limit(64)
            ->link(
                function () {
                    return route('admin.notices.show', ['notice' => $this->id]);
                },
                "_parent"
            );

        $grid->column('user.name', "作成者")->display(function ($user_name, $column) {
            // 削除済みであればリンクなし
            if ($this->user->deleted_at) {
                return $user_name;
            }

            return $column->link(function () {
                return route('admin.users.show', ['user' => $this->user_id]);
            }, "_parent");
        });

        $grid->column('meeting_likes_count', $this->trans("meeting_likes"));
        $grid->column('meeting_comments_count', $this->trans("meeting_comments"));
        $grid->column('meeting_visitors_count', $this->trans("meeting_visitors"));

        $grid->column('created_at', $this->trans('Created at', 'notices'));
        $grid->column('updated_at', $this->trans('Updated at', 'notices'));

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
                ->loadCount([
                    'meeting_likes',
                    'meeting_comments',
                    'meeting_visitors'
                ])
        );

        $show->field('id', $this->trans("Id", "meetings"));
        $show->field('started_at', $this->trans("Started at", "meetings"));
        $show->field('title', $this->trans("Title", "meetings"));
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
        $show->field('participants', $this->trans("Participants", "meetings"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('content', $this->trans("Content", "meetings"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });

        $show->field('meeting_likes_count', $this->trans("meeting_likes"));
        $show->field('meeting_comments_count', $this->trans("meeting_comments"));
        $show->field('meeting_visitors_count', $this->trans("meeting_visitors_count"));
        $show->field('meeting_visitors', $this->trans("meeting_visitors"))
            ->unescape()
            ->as(function (Collection $meeting_visitors) {
                return $meeting_visitors->map(function ($meeting_visitor) {
                    // 削除済みであればリンクなし
                    if ($meeting_visitor->user->deleted_at) {
                        return htmlspecialchars($meeting_visitor->user->name);
                    }

                    return sprintf(
                        '<a href="%s">%s</a>',
                        route("admin.users.show", ["user" => $meeting_visitor->user_id]),
                        htmlspecialchars($meeting_visitor->user->name)
                    );
                })->implode(", ");
            });
        $show->field('created_at', $this->trans("Created at", "meetings"));
        $show->field('updated_at', $this->trans("Updated at", "meetings"));

        // コメント
        $show->meeting_comments($this->trans("meeting_comments"), function ($grid) {
            $grid->resource('/admin/meeting-comments');
            $grid->disablePagination();

            $grid->column('id', $this->trans("id", "meeting_comments"));
            $grid->column('user.name', $this->trans("name", "users"))->link(
                function () {
                    return route("admin.users.show", ["user" => $this->user_id]);
                },
                "_parent"
            );
            $grid->column('comment', $this->trans("comment", "meeting_comments"))->limit(64);
            $grid->column('created_at', $this->trans("Created at", "meeting_comments"));
            $grid->column('updated_at', $this->trans("Updated at", "meeting_comments"));
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

        $form->datetime('started_at', $this->trans("Started at", "meetings"))->default(date('Y-m-d H:i:s'));
        $form->text('title', $this->trans("Title", "meetings"));

        // 論理削除済みのメンバーが指定されている場合はリストに含むようにする
        $users = new User();
        if ($form->isEditing()) {
            $meeting_id = request()->route()->parameter('meeting');
            $meeting = Meeting::find($meeting_id);

            $users = $users
                ->withTrashed()->where(function (Builder $query) use ($meeting) {
                    $query->whereNull("deleted_at")->orWhere("id", $meeting->user_id);
                });
        }

        $form->select('user_id', "作成者")
            ->options($users->get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);

        $form->textarea('participants', $this->trans("Participants", "meetings"));
        $form->textarea('content', $this->trans("Content", "meetings"));

        return $form;
    }
}
