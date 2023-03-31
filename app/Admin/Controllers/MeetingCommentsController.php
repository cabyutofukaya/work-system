<?php

namespace App\Admin\Controllers;

use App\Models\Meeting;
use App\Models\MeetingComment;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 議事録コメント
 * @package App\Admin\Controllers
 */
class MeetingCommentsController extends BaseController
{
    /**
     * パラメータで指定された日報IDに対応するモデル
     * @var \App\Models\Meeting
     */
    private Meeting $meeting;

    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = 'コメント';

        // モデルクラス
        $this->model = MeetingComment::class;

        // コンストラクタで日報情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから日報IDを取得
            $meeting_id = request()->get('meeting_id');

            // それ以外のアクションでは日報コンテンツIDから日報IDを取得
            if (!$meeting_id) {
                $meeting_comment_id = request()->route()->parameter('meeting_comment');
                $meeting_id = MeetingComment::find($meeting_comment_id)->{'meeting_id'};
            }

            $this->meeting = Meeting::find($meeting_id);
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
        $show = new Show($this->modelClass()::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
                $tools->disableDelete();
            });
        
        $show->field('id', $this->trans("Id", "meeting-comments"));
        $show->field('user.name', $this->trans("name", "users"))
            ->unescape()
            ->as(function ($content) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route("admin.users.show", ['user' => $this->user_id]),
                    $content
                );
            });
        $show->field('comment', $this->trans("comment", "meeting-comments"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('created_at', $this->trans("Created at", "meeting-comments"));
        $show->field('updated_at', $this->trans("Updated at", "meeting-comments"));
        $show->field('deleted_at', $this->trans("Deleted at", "meeting-comments"));

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

        $form->select('user_id', $this->trans("name", "users"))
            ->options(User::get()->pluck("name", "id"))
            ->creationRules(['required', "exists:users,id"])
            ->updateRules(['required', "exists:users,id"]);
        $form->textarea('comment', $this->trans("comment", "meeting-comments"));

        $form->hidden('meeting_id')->value($this->meeting->id);

        // 保存後にリダイレクト
        $form->saved(function (Form $form) {
            admin_toastr(trans('admin.save_succeeded'));

            return redirect()->route('admin.meetings.show', ['meeting' => $this->meeting->id]);
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
            ['text' => (new ReportController())->title, 'url' => route('admin.meetings.index')],
            ['text' => $this->meeting->user->name . " (" . $this->meeting->date_string . ')', 'url' => route('admin.meetings.show', ['meeting' => $this->meeting->id])],
            ['text' => "コメント (" . $this->modelClass()->find($id)->user->name . ")"]
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
            ['text' => (new ReportController())->title, 'url' => route('admin.meetings.index')],
            ['text' => $this->meeting->user->name . " (" . $this->meeting->date_string . ')', 'url' => route('admin.meetings.show', ['meeting' => $this->meeting->id])],
            ['text' => "コメント (" . $this->modelClass()->find($id)->user->name . ")"]
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
            ['text' => (new ReportController())->title, 'url' => route('admin.meetings.index')],
            ['text' => $this->meeting->user->name . " (" . $this->meeting->date_string . ')', 'url' => route('admin.meetings.show', ['meeting' => $this->meeting->id])],
            ['text' => "作成"]
        );
    }
}
