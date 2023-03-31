<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Model;
use Lang;
use Str;

/**
 * CRUD共通ベースコントローラ
 * パンくずリストの書き換えを行う
 * @package App\Admin\Controllers
 */
class BaseController extends AdminController
{
    /**
     * モデルクラス
     *
     * @var string
     */
    protected string $model;

    /**
     * パンくずリストに表示するカラム
     *
     * @var string
     */
    protected string $breadcrumb_display_column;

    /**
     * モデルクラスのインスタンスを取得
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function modelClass(): Model
    {
        return new $this->model();
    }


    /**
     * バリデーションの言語データからカラム名を翻訳
     * 言語行が存在しなければ属性キーを返す
     *
     * @param string $attribute
     * @param string|null $table
     * @return string
     */
    protected function trans(string $attribute, string $table = null): string
    {
        $table = $table ?? $this->modelClass()->getTable();

        // モデル固有の訳語を取得
        $key_models = "validation.attributes." . $table . "." . Str::snake($attribute);
        $trans = Lang::get($key_models);
        $trans = ($key_models !== $trans) ? $trans : null;

        // テーブル名の訳語を取得
        if ($trans === null) {
            $key = "validation.attributes.tables." . Str::snake($attribute);
            $trans = Lang::get($key);
            $trans = ($key !== $trans) ? $trans : null;
        }

        // デフォルトの訳語を取得
        if ($trans === null) {
            $key = "validation.attributes." . Str::snake($attribute);
            $trans = Lang::get($key);
            $trans = ($key !== $trans) ? $trans : null;
        }

        // 訳語がなければキー名をそのままレスポンスする
        return $trans ?? $attribute;
    }

    /**
     * リストから整形されたheader htmlを生成する
     *
     * @param array $messages
     * @return string
     */
    protected function makeHeader(array $messages): string
    {
        if (!$messages) {
            return "";
        }

        $header = '<ul class="text-muted">';
        foreach ($messages as $message) {
            $header .= "<li>";
            $header .= htmlspecialchars($message);
            $header .= "</li>";
        }
        $header .= "<ul>";

        return $header;
    }

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        // パンくずリストを書き換え
        return parent::index($content)->breadcrumb(
            ['text' => $this->title]
        );
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
            ['text' => $this->title, 'url' => '/' . request()->segment(2)],
            ['text' => $this->modelClass()->find($id)->{$this->breadcrumb_display_column ?? "id"} ?? $id]
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
            ['text' => $this->title, 'url' => '/' . request()->segment(2)],
            ['text' => $this->modelClass()->find($id)->{$this->breadcrumb_display_column ?? "id"}]
        );
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        // パンくずリストを書き換え
        return parent::create($content)->breadcrumb(
            ['text' => $this->title, 'url' => '/' . request()->segment(2)],
            ['text' => "作成"]
        );
    }
}