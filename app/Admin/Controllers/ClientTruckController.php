<?php

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\Genre;
use App\Models\Product;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * 会社 トラック会社
 *
 * @package App\Admin\Controllers
 */
class ClientTruckController extends BaseController
{
    private string $client_type_id;
    private string $client_type_table;

    /**
     * constructor.
     */
    function __construct()
    {
        // 会社タイプ
        $this->client_type_id = 'truck';

        // タイトル
        $this->title = config('const.client_types.' . $this->client_type_id . '.name');

        // モデルクラス
        $this->model = Client::class;

        // 固有情報テーブル名
        $this->client_type_table = 'client_type_' . $this->client_type_id;

        // パンくずリストに表示するカラム
        $this->breadcrumb_display_column = "name";
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid($this->modelClass());

        // 会社タイプで絞り込む
        $grid->model()->ofClientType($this->client_type_id);

        $grid->header(function () {
            return $this->makeHeader([
                "登録の新しいものから表示されます。",
                "メンバーはすべての会社を閲覧・編集・削除できます。",
                "営業日報に含まれる会社を削除しても、その営業日報は削除されません。",
            ]);
        });

        $grid->column('id', $this->trans('Id', 'clients'));
        $grid->column('name', $this->trans('Name', 'clients'))->link(
            function () {
                return route("admin.clients-" . $this->client_type_id . ".show", ['client' => $this->id]);
            }, "_parent"
        );
        $grid->column('tel', $this->trans('tel', 'clients'));
        $grid->column('url', $this->trans('url', 'clients'))
            ->display(function ($url) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    $url,
                    Str::limit($url, 32, '...')
                );
            });
        $grid->column('created_at', $this->trans('Created at', 'clients'));

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

        //  会社タイプの異なる項目へのアクセス時
        if ($this->client_type_id !== $show->getModel()->client_type_id) {
            Permission::error();
        }

        $show->field('id', $this->trans('Id', 'clients'));
        $show->field('image', $this->trans('Image', 'clients'))->image();
        $show->field('name', $this->trans('Name', 'clients'));
        $show->field('name_kana', $this->trans('Name kana', 'clients'));
        $show->field('postcode', $this->trans('Postcode', 'clients'));
        $show->field('prefecture', $this->trans('Prefecture', 'clients'));
        $show->field('address', $this->trans('Address', 'clients'));
        if ($show->getModel()->lat && $show->getModel()->lng) {
            $show->field('Location', $this->trans('Location'))->latlong('lat', 'lng', 300);
        }
        $show->field('url', $this->trans('Url', 'clients'))->link();
        $show->field('email', $this->trans('Email', 'clients'));
        $show->field('representative', $this->trans('Representative', 'clients'));
        $show->field('tel', $this->trans('Tel', 'clients'));
        $show->field('fax', $this->trans('Fax', 'clients'));
        $show->field('business_hours', $this->trans('Business hours', 'clients'));

        // 固有情報
        $show->field("$this->client_type_table.drivers_count", $this->trans('drivers_count', $this->client_type_table));

        // ジャンル
        $show->field('genres', $this->trans("genres"))
            ->unescape()
            ->as(function (Collection $genres) {
                return $genres->map(function ($genre) {
                    return sprintf(
                        '%s',
                        htmlspecialchars($genre["name"])
                    );
                })->implode(", ");
            });

        // 商材
        $show->field('products', $this->trans("products"))
            ->unescape()
            ->as(function (Collection $products) {
                return $products->map(function ($product) {
                    return sprintf(
                        '%s',
                        htmlspecialchars($product["name"])
                    );
                })->implode(", ");
            });

        // 詳細
        $show->field('description', $this->trans('Description', 'clients'))
            ->unescape()
            ->as(function ($content) {
                return "<span style='white-space: pre-line'>" . htmlspecialchars(trim($content)) . "</span>";
            });

        // タイムスタンプ
        $show->field('created_at', $this->trans('Created at', 'clients'));
        $show->field('updated_at', $this->trans('Updated at', 'clients'));
        $show->field('deleted_at', $this->trans('Deleted at', 'clients'));

        // 営業所
        $show->branches($this->trans('branches', 'clients'), function ($grid) {
            $grid->resource('/admin/branches');
            $grid->disablePagination();

            $grid->column('id', $this->trans('Id', 'branches'));
            $grid->column('name', $this->trans('Name', 'branches'))->link(
                function () {
                    return route("admin.branches.show", ['branch' => $this->id]);
                }, "_parent"
            );
            $grid->column('address', $this->trans('address', 'branches'))->display(function ($address) {
                return $this->prefecture . $address;
            });
            $grid->column('created_at', $this->trans("Created at", "branches"));
            $grid->column('updated_at', $this->trans("Updated at", "branches"));
        });

        // 相手先担当者
        $show->contact_persons($this->trans('contact_persons', 'clients'), function ($grid) {
            $grid->resource('/admin/contact-persons');
            $grid->disablePagination();

            $grid->column('id', $this->trans('Id', 'contact_persons'));
            $grid->column('name', $this->trans('Name', 'contact_persons'))->link(
                function () {
                    return route("admin.contact-persons.show", ['contact_person' => $this->id]);
                }, "_parent"
            );
            $grid->column('email', $this->trans('email', 'contact_persons'));
            $grid->column('tel', $this->trans('tel', 'contact_persons'));
            $grid->column('department', $this->trans('department', 'contact_persons'));
            $grid->column('position', $this->trans('position', 'contact_persons'));
            $grid->column('created_at', $this->trans("Created at", "contact_persons"));
            $grid->column('updated_at', $this->trans("Updated at", "contact_persons"));
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

        //  会社タイプの異なる項目へのアクセス時
        if ($form->isEditing() && $this->client_type_id !== $form->model()->find(request()->route()->parameter('client'))["client_type_id"]) {
            Permission::error();
        }

        $form->image('image', $this->trans('image'))
            ->uniqueName()
            ->removable();
        $form->text('name', $this->trans('Name', 'clients'))->required();
        $form->text('name_kana', $this->trans('Name kana', 'clients'));
        $form->text('postcode', $this->trans('Postcode', 'clients'))
            ->rules(["nullable", 'regex:/^[0-9]{7}$/'])
            ->help("半角数字7桁で入力してください");
        $form->select('prefecture', $this->trans('Prefecture', 'clients'))
            ->options(array_combine(array_values(config("const.prefectures")), config("const.prefectures")))
            ->rules(["nullable", "in:" . implode(",", config("const.prefectures"))]);
        $form->text('address', $this->trans('Address', 'clients'));
        $form->latlong('lat', 'lng', $this->trans('location'))
            ->default(['lat' => "", 'lng' => ""])
            ->height(500)
            // 座標が設定されていない場合に初期位置としてjqueryで読み取るdata属性を設定
            ->attribute(["data-lat-default" => config("const.location_default.lat"), "data-lng-default" => config("const.location_default.lng")])
            ->help("マーカーを動かして座標を指定してください。位置情報を使用しない場合は数値を空にしてください。");
        $form->text('url', $this->trans('Url', 'clients'))
            ->rules(["nullable", 'url']);
        $form->email('email', $this->trans('Email', 'clients'))
            ->rules(["nullable", 'email:filter']);
        $form->text('representative', $this->trans('Representative', 'clients'));
        $form->text('tel', $this->trans('Tel', 'clients'))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");
        $form->text('fax', $this->trans('Fax', 'clients'))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");
        $form->text('business_hours', $this->trans('Business hours', 'clients'));

        // 固有情報
        $form->radio("$this->client_type_table.drivers_count", $this->trans('drivers_count', $this->client_type_table))
            ->options(array_combine(array_values(config("const.client_types.truck.drivers_count")), config("const.client_types.truck.drivers_count")))
            ->rules(["nullable", "in:" . implode(",", config("const.client_types.truck.drivers_count"))]);

        $form->checkbox('genres', $this->trans('genres', 'clients'))->options(Genre::ofClientType($this->client_type_id)->pluck('name', 'id'));
        $form->checkbox('products', $this->trans('products', 'clients'))->options(Product::all()->pluck('name', 'id'));
        $form->textarea('description', $this->trans('Description', 'clients'));

        $form->hidden('client_type_id')->value($this->client_type_id);

        /**
         * スクリプト
         */

        // Place APIを有効化していないため「場所を入力」フォームを無効化
        $script_search_latlng_disable = <<<JS
$(function() {
    $("#search-latlng").parent(".input-group").hide();
});
JS;
        Admin::script($script_search_latlng_disable);

        return $form;
    }
}
