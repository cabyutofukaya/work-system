<?php

namespace App\Admin\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;

/**
 * 管理ユーザ
 * @package App\Admin\Controllers
 */
class AdminUserController extends BaseController
{
    /**
     * 対象とするアカウントが所属するroleのslugを指定
     *
     * @var string
     */
    protected string $role_slug = "adminuser";

    /**
     * AdminUserController constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = $this->trans('admin_users', 'admin_users');

        // モデルクラス
        $this->model = config('admin.database.users_model');

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

        // 管理ユーザロール adminuser が設定されたレコードに絞り込む
        $grid->model()->whereHas('roles', function (Builder $query) {
            $query->where('slug', $this->role_slug);
        });

        $grid->column('username', $this->trans('username', 'admin_users'));
        $grid->column('name', $this->trans('name', 'admin_users'));
        $grid->column('created_at', $this->trans('created_at', 'admin_users'));
        $grid->column('updated_at', $this->trans('updated_at', 'admin_users'));

        // 自分自身の削除を無効化
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() === Admin::user()->id) {
                $actions->disableDelete();
            }
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id): Show
    {
        // 管理ユーザロール adminuser が設定されたレコードに絞り込む
        $show = new Show($this->modelClass()::whereHas('roles', function (Builder $query) {
            $query->where('slug', $this->role_slug);
        })->findOrFail($id));

        $show->field('username', $this->trans('username', 'admin_users'));
        $show->field('name', $this->trans('name', 'admin_users'));
        $show->field('created_at', $this->trans('created_at', 'admin_users'));
        $show->field('updated_at', $this->trans('updated_at', 'admin_users'));

        return $show;
    }

     /**
         * Make a form builder.
         *
         * @return Form
         */
        public function form(): Form
        {
        $roleModel = config('admin.database.roles_model');
        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form = new Form($this->modelClass());

        $form->text('username', $this->trans('username', 'admin_users'))
            ->creationRules(['required', "unique:$connection.$userTable"])
            ->updateRules(['required', "unique:$connection.$userTable,username,{{id}}"]);

        $form->text('name', $this->trans('name'))->rules('required');

        $form->password('password', $this->trans('password'))->rules('required|confirmed');
        $form->password('password_confirmation', $this->trans('password_confirmation'))->rules('required')
            ->default(function (Form $form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        // 作成したレコードに管理ユーザロール adminuser を設定する
        $form->saved(function (Form $form) use ($roleModel) {
            $form->model()->roles()->sync($roleModel::firstWhere('slug', $this->role_slug)->id, false);
            $form->model()->save();
        });

        return $form;
    }
}
