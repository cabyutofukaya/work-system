<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\ContactPerson;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 相手先担当者
 * @package App\Admin\Controllers
 */
class ContactPersonController extends BaseController
{
    /**
     * パラメータで指定された会社IDに対応するモデル
     * @var \App\Models\Client
     */
    private Client $client;

    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = '相手先担当者';

        // モデルクラス
        $this->model = ContactPerson::class;

        // コンストラクタで会社情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから会社IDを取得
            $client_id = request()->input('client_id');

            // それ以外のアクションでは営業所IDから日報IDを取得
            if (!$client_id) {
                $contact_person_id = request()->route()->parameter('contact_person');
                $client_id = $this->modelClass()::find($contact_person_id)->{'client_id'};
            }

            $this->client = Client::find($client_id);
            return $next($request);
        });
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

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableList();
                // 削除後に親モデルのshowにリダイレクトできないため削除ボタンを無効化
                $tools->disableDelete();
            });

        $show->field('id', $this->trans('Id', 'contact_persons'));
        $show->field('name', $this->trans('Name', 'contact_persons'));
        $show->field('email', $this->trans('Email', 'contact_persons'));
        $show->field('tel', $this->trans('tel', 'contact_persons'));
        $show->field('department', $this->trans('department', 'contact_persons'));
        $show->field('position', $this->trans('position', 'contact_persons'));
        $show->field('created_at', $this->trans('Created at', 'contact_persons'));
        $show->field('updated_at', $this->trans('Updated at', 'contact_persons'));
        $show->field('deleted_at', $this->trans('Deleted at', 'contact_persons'));

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

        $form->text('name', $this->trans("Name", "contact_persons"))->required();
        $form->email('email', $this->trans('Email', 'contact_persons'))
            ->rules(["nullable", 'email:filter']);
        $form->text('tel', $this->trans('Tel', 'clients'))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");
        $form->text('department', $this->trans('department', 'contact_persons'))
            ->rules(["nullable"]);
        $form->text('position', $this->trans('position', 'contact_persons'))
            ->rules(["nullable"]);

        $form->hidden('client_id')->value($this->client->id);

        // 保存後にリダイレクト
        $form->saved(function () {
            admin_toastr(trans('admin.save_succeeded'));

            return redirect()->route("admin.clients-" . $this->client->client_type_id . '.show', ['client' => $this->client->id]);
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
            ['text' => $this->client->client_type_name, 'url' => route("admin.clients-" . $this->client->client_type_id . ".index")],
            ['text' => $this->client->name, 'url' => route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client->id])],
            ['text' => $this->modelClass()->find($id)->name]
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
            ['text' => $this->client->client_type_name, 'url' => route("admin.clients-" . $this->client->client_type_id . ".index")],
            ['text' => $this->client->name, 'url' => route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client->id])],
            ['text' => $this->modelClass()->find($id)->name]
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
            ['text' => $this->client->client_type_name, 'url' => route("admin.clients-" . $this->client->client_type_id . ".index")],
            ['text' => $this->client->name, 'url' => route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client->id])],
            ['text' => $this->title . " 作成"]
        );
    }
}
