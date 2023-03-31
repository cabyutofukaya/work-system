<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Client;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 営業所
 * @package App\Admin\Controllers
 */
class BranchController extends BaseController
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
        $this->title = '営業所';

        // モデルクラス
        $this->model = Branch::class;

        // コンストラクタで会社情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから会社IDを取得
            $client_id = request()->input('client_id');

            // それ以外のアクションでは営業所IDから日報IDを取得
            if (!$client_id) {
                $branch_id = request()->route()->parameter('branch');
                $client_id = $this->modelClass()::find($branch_id)->{'client_id'};
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

        $show->field('id', $this->trans("Id", "branches"));
        $show->field('name', $this->trans("Name", "branches"));
        $show->field('postcode', $this->trans('Postcode', "branches"));
        $show->field('prefecture', $this->trans('Prefecture', "branches"));
        $show->field('address', $this->trans('Address', "branches"));
        if ($show->getModel()->lat && $show->getModel()->lng) {
            $show->field('Location', $this->trans('Location'))->latlong('lat', 'lng', 300);
        }
        $show->field('tel', $this->trans('Tel', "branches"));
        $show->field('fax', $this->trans('Fax', "branches"));
        $show->field('created_at', $this->trans("Created at", "branches"));
        $show->field('updated_at', $this->trans("Updated at", "branches"));
        $show->field('deleted_at', $this->trans("Deleted at", "branches"));

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

        $form->text('name', $this->trans("Name", "branches"))->required();
        $form->text('postcode', $this->trans('Postcode', "branches"))
            ->rules(["nullable", 'regex:/^[0-9]{7}$/'])
            ->help("半角数字7桁で入力してください");
        $form->select('prefecture', $this->trans('Prefecture', "branches"))
            ->options(array_combine(array_values(config("const.prefectures")), config("const.prefectures")))
            ->rules(["nullable", "in:" . implode(",", config("const.prefectures"))]);
        $form->text('address', $this->trans('Address', "branches"));
        $form->latlong('lat', 'lng', $this->trans('location'))
            ->default(['lat' => "", 'lng' => ""])
            ->height(500)
            // 座標が設定されていない場合に初期位置としてjqueryで読み取るdata属性を設定
            ->attribute(["data-lat-default" => config("const.location_default.lat"), "data-lng-default" => config("const.location_default.lng")])
            ->help("マーカーを動かして座標を指定してください。位置情報を使用しない場合は数値を空にしてください。");
        $form->text('tel', $this->trans('Tel', "branches"))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");
        $form->text('fax', $this->trans('Fax', "branches"))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");

        $form->hidden('client_id')->value($this->client->id);

        // 保存後にリダイレクト
        $form->saved(function (Form $form) {
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
