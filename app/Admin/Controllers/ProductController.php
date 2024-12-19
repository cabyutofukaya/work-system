<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

/**
 * 営業所
 * @package App\Admin\Controllers
 */
class ProductController extends BaseController
{
    /**
     * パラメータで指定された会社IDに対応するモデル
     * @var \App\Models\Client
     */
    private Product $product;

    /**
     * Title for current resource.
     *
     * @var string
     */
    function __construct()
    {
        // タイトル
        $this->title = '教材';

        // モデルクラス
        $this->model = Product::class;

    }

      /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid($this->modelClass());


        $grid->column('id', $this->trans('Id', 'users'));

        $grid->column('name', '商材名');


        return $grid;
    }
   
}
