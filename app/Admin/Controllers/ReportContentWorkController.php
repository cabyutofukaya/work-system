<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\Report;
use App\Models\ReportContent;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 業務日報
 * @package App\Admin\Controllers
 */
class ReportContentWorkController extends BaseController
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
        $this->title = '業務日報';

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
        $show = new Show($this->modelClass()::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
                $tools->disableDelete();
            });;

        $show->field('id', $this->trans("Id", "report_contents_work"));
        $show->field('title', $this->trans("title", "report_contents_work"));
        $show->field('description', $this->trans("Description", "report_contents_work"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('is_complaint', $this->trans("Is complaint", "report_contents_work"))
            ->as(function ($content) {
                return $content ? 'はい' : 'いいえ';
            });
        $show->field('created_at', $this->trans("Created at", "report_contents_work"));
        $show->field('updated_at', $this->trans("Updated at", "report_contents_work"));
        $show->field('deleted_at', $this->trans("Deleted at", "report_contents_work"));

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

        $form->text('title', $this->trans("title", "report_contents_work"))->required();
        $form->textarea('description', $this->trans("Description", "report_contents_work"));
        $form->switch('is_complaint', $this->trans("Is complaint", "report_contents_work"))
            ->states([
                'off' => ['value' => 0, 'text' => 'いいえ', 'color' => 'success'],
                'on' => ['value' => 1, 'text' => 'はい', 'color' => 'danger'],
            ]);

        $form->hidden('type')->value('work');
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
    public function show($id, Content $content)
    {
        // パンくずリストを書き換え
        return parent::show($id, $content)->breadcrumb(
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => mb_substr($this->modelClass()->find($id)->title, 1, 16)]
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
    public function edit($id, Content $content)
    {
        // パンくずリストを書き換え
        return parent::edit($id, $content)->breadcrumb(
            ['text' => (new ReportController())->title, 'url' => route('admin.reports.index')],
            ['text' => $this->report->user->name . " (" . $this->report->date_string . ')', 'url' => route('admin.reports.show', ['report' => $this->report->id])],
            ['text' => mb_substr($this->modelClass()->find($id)->title, 1, 16)]
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
