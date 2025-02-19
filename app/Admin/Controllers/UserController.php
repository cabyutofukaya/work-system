<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Client;

/**
 * 一般ユーザ
 * @package App\Admin\Controllers
 */
class UserController extends BaseController
{
    /**
     * constructor.
     */
    function __construct()
    {
        // タイトル
        $this->title = 'メンバー';

        // モデルクラス
        $this->model = User::class;

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

        // $clients = Client::get();

        // $type_list = config('const.client_list');
   

        // foreach ($clients as $client) {

        //     $name_text = '';
            
        //     if($client->name_position == '前'){
        //         $name_text .= $client->type_name;
        //     }

        //     $name_text .= $client->name;

        //     if($client->name_position == '後ろ'){
        //         $name_text .= $client->type_name;
        //     }

        //     Client::where('id',$client->id)->update([
        //         'name_text' => $name_text,
        //     ]);
        // }

        // dd('完了');

        $grid = new Grid($this->modelClass());

        $grid->header(function () {
            return $this->makeHeader([
                "作成された順に表示されます。",
                "メンバーは他のすべてのメンバーの情報を閲覧できます。",
                "メンバーは自分のパスワード・メールアドレスのみ編集できます。",
                "メンバーを削除すると日報や議事録の閲覧者一覧から削除され、閲覧数も集計に含まれなくなります。また日報に設定された社内担当者から削除されます。",
                "メンバーを削除しても削除したメンバーが作成した日報・議事録・お知らせ・コメント・いいね数は削除されません。",
            ]);
        });

        $grid->column('id', $this->trans('Id', 'users'));
        $grid->column('username', $this->trans('username', 'users'))->link(
            function () {
                return route('admin.users.show', ['user' => $this->id]);
            },
            "_parent"
        );
        $grid->column('name', $this->trans('Name', 'users'))->link(
            function () {
                return route('admin.users.show', ['user' => $this->id]);
            },
            "_parent"
        );
        $grid->column('email', $this->trans('email', 'users'));
        $grid->column('tel', $this->trans('tel', 'users'));
        $grid->column('department', $this->trans('department', 'users'));
        $grid->column('created_at', $this->trans('Created at'));
        $grid->column('login_at', $this->trans('login_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id): Show
    {
        $show = new Show($this->modelClass()::findOrFail($id));

        $show->field('id', $this->trans('Id', 'users'));
        $show->field('username', $this->trans('username', 'users'));
        $show->field('name', $this->trans('Name', 'users'));
        $show->field('name_kana', '氏名(カナ)');
        $show->field('email', $this->trans('Email', 'users'));
        $show->field('tel', $this->trans('tel', 'users'));
        $show->field('department', $this->trans('department', 'users'));

        // 商材
        $show->field('products', $this->trans("products", 'users'))
            ->unescape()
            ->as(function (Collection $products) {
                return $products->map(function ($product) {
                    return sprintf(
                        '%s',
                        htmlspecialchars($product["name"])
                    );
                })->implode(", ");
            });


        $show->field('type_id', $this->trans('type_id', 'users'));
        // $show->column('type_id', $this->trans("type_id", "users"))
        //     ->replace([0 => 'いいえ', 1 => 'はい'])
        //     ->label([
        //         "0" => 'default', "1" => 'success',
        //     ]);

        $show->field('created_at', $this->trans('Created at', 'users'));

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

        $form->text('username', $this->trans('username', 'users'))
            ->creationRules(['required', "alpha_dash", "regex:/^[\w_-]+$/", "unique:users"])
            ->updateRules(['required', "alpha_dash", "regex:/^[\w_-]+$/", "unique:users,username,{{id}}"])
            ->help("半角英数・ハイフン・アンダースコアのみ");

        $form->text('name', $this->trans('name', 'users'))
            ->rules('required');

            $form->text('name_kana', '氏名(カナ)')
            ->rules('required');

        // 論理削除済みユーザのメールアドレスを設定可能にする
        $form->email('email', $this->trans('email', 'users'))
            ->creationRules([
                'required', 'email',
                Rule::unique('users')->whereNull('deleted_at')
            ])
            ->updateRules([
                'required', 'email',
                Rule::unique('users')->whereNull('deleted_at')->ignore(request()->route()->parameter("user"))
            ]);

        $form->password('password', $this->trans('password', 'users'))
            ->rules(['required', "string", Password::min(6), 'confirmed'])
            ->help("6文字以上");

        $form->password('password_confirmation', $this->trans('password_confirmation', 'users'))
            ->rules('required')
            ->default(function (Form $form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->text('tel', $this->trans('tel', 'users'))
            ->rules(["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'])
            ->help("半角数字とハイフンのみで入力してください");

        // $form->text('department', $this->trans('department', 'users'))
        //     ->rules(['nullable' ,"string"]);

            $form->select('department', $this->trans('department', 'users'))
            ->options([
                'CCD' => 'CCD',
                'TCD' => 'TCD',
                'ICTS' => 'ICTS',
                '経理' => '経理',
                'WSD' => 'WSD',
                'MGT' => 'MGT',
                'BPD' => 'BPD',
                'LOD' => 'LOD',
            ]);

        $form->checkbox('products', $this->trans('products', 'users'))->options(Product::all()->pluck('name', 'id'));

        // $form->select('type_id', $this->trans('type_id', 'users'))
        // ->options(User::get()->pluck("name", "id"));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        $form->select('admin_report', '日報管理者')
        ->options([
            '0' => '0',
            '1' => '1',
        ]);

        return $form;
    }
}
