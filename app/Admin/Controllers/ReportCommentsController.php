<?php

namespace App\Admin\Controllers;

use App\Models\ReportComment;
use App\Models\User;
use App\Models\Report;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 日報コメント
 * @package App\Admin\Controllers
 */
class ReportCommentsController extends BaseController
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
        $this->title = 'コメント';

        // モデルクラス
        $this->model = ReportComment::class;

        // コンストラクタで日報情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから日報IDを取得
            $report_id = request()->get('report_id');

            // それ以外のアクションでは日報コンテンツIDから日報IDを取得
            if (!$report_id) {
                $report_comment_id = request()->route()->parameter('report_comment');
                $report_id = ReportComment::find($report_comment_id)->{'report_id'};
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
        $show = new Show($this->modelClass()::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
                $tools->disableDelete();
            });

        $show->field('id', $this->trans("Id", "report-comments"));
        $show->field('user.name', $this->trans("name", "users"))
            ->unescape()
            ->as(function ($content) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route("admin.users.show", ['user' => $this->user_id]),
                    $content
                );
            });
        $show->field('comment', $this->trans("comment", "report-comments"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('created_at', $this->trans("Created at", "report-comments"));
        $show->field('updated_at', $this->trans("Updated at", "report-comments"));
        $show->field('deleted_at', $this->trans("Deleted at", "report-comments"));

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
        $form->textarea('comment', $this->trans("comment", "report-comments"));

        $form->hidden('report_id')->value($this->report->id);

        // 保存後にリダイレクト
        $form->saved(function (Form $form) {
            admin_toastr(trans('admin.save_succeeded'));

            return redirect()->route('admin.reports.show', ['report' => $this->report->id]);
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
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
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
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => "作成"]
        );
    }
}
