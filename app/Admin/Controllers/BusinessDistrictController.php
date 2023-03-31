<?php

namespace App\Admin\Controllers;

use App\Models\BusinessDistrict;
use App\Models\Client;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 営業エリア
 * @package App\Admin\Controllers
 */
class BusinessDistrictController extends BaseController
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
        $this->title = '営業エリア';

        // モデルクラス
        $this->model = BusinessDistrict::class;

        // コンストラクタで会社情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから会社IDを取得
            $client_id = request()->input('client_id');

            // それ以外のアクションでは営業エリアIDから日報IDを取得
            if (!$client_id) {
                $business_district_id = request()->route()->parameter('business_district');
                $client_id = $this->modelClass()::find($business_district_id)->{'client_id'};
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

        $show->field('id', $this->trans('Id', 'business_districts'));
        $show->field('prefecture', $this->trans('Prefecture', "business_districts"));
        $show->field('address', $this->trans('Address', "business_districts"));
        $show->field('created_at', $this->trans('Created at', 'business_districts'));
        $show->field('updated_at', $this->trans('Updated at', 'business_districts'));
        $show->field('deleted_at', $this->trans('Deleted at', 'business_districts'));

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

        $form->select('prefecture', $this->trans('Prefecture', "business_districts"))
            ->options(array_combine(array_values(config("const.prefectures")), config("const.prefectures")))
            ->rules(["nullable", "in:" . implode(",", config("const.prefectures"))]);
        $form->text('address', $this->trans('Address', "business_districts"));

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
        $businessDistrict = $this->modelClass()->find($id);

        // パンくずリストを書き換え
        return parent::show($id, $content)->breadcrumb(
            ['text' => $this->client->client_type_name, 'url' => route("admin.clients-" . $this->client->client_type_id . ".index")],
            ['text' => $this->client->name, 'url' => route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client->id])],
            ['text' => $businessDistrict->prefecture . $businessDistrict->address]
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
        $businessDistrict = $this->modelClass()->find($id);

        // パンくずリストを書き換え
        return parent::edit($id, $content)->breadcrumb(
            ['text' => $this->client->client_type_name, 'url' => route("admin.clients-" . $this->client->client_type_id . ".index")],
            ['text' => $this->client->name, 'url' => route('admin.clients-' . $this->client->client_type_id . '.show', ['client' => $this->client->id])],
            ['text' => $businessDistrict->prefecture . $businessDistrict->address]
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
