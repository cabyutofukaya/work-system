<?php

namespace App\Admin\Controllers;

use App\Models\Vehicle;
use App\Models\Client;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 保有車両
 *
 * @package App\Admin\Controllers
 */
class VehicleController extends BaseController
{
    /**
     * パラメータで指定された会社IDに対応するモデル
     * @var \App\Models\Client
     */
    private Client $client;

    /**
     * URLから取得した車両タイプ
     * @var string
     */
    private string $type;

    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = '保有車両';

        // モデルクラス
        $this->model = Vehicle::class;

        // コンストラクタで会社情報を取得するためのミドルウェアを登録
        $this->middleware(function ($request, $next) {
            // 新規作成時はクエリパラメータから会社IDを取得
            $client_id = request()->input('client_id');

            // それ以外のアクションでは保有車両IDから日報IDを取得
            if (!$client_id) {
                $vehicle_id = request()->route()->parameter('vehicle');
                $client_id = $this->modelClass()::find($vehicle_id)->{'client_id'};
            }

            $this->client = Client::find($client_id);

            // URLから車両タイプを取得
            $this->type = str_replace("vehicles-", "", request()->segment(2));

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

        $show->field('id', $this->trans("Id", "vehicle"));
        $show->field('image', $this->trans("Image", "vehicle"))->image();
        $show->field('name', $this->trans("Name", "vehicle"));
        $show->field('description', $this->trans("Description", "vehicle"))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });
        $show->field('created_at', $this->trans("Created at", "vehicle"));
        $show->field('updated_at', $this->trans("Updated at", "vehicle"));
        $show->field('deleted_at', $this->trans("Deleted at", "vehicle"));

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

        $form->image('image', $this->trans('image', "vehicle"))
            ->uniqueName()
            ->removable();
        $form->text('name', $this->trans("Name", "vehicle"))
            ->required();
        $form->textarea('description', $this->trans("Description", "vehicle"));

        $form->hidden('client_id')->value($this->client->id);
        $form->hidden('type')->value($this->type);

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
