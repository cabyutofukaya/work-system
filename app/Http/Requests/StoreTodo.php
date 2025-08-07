<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodo extends FormRequest
{
    /**
     * 認可ロジック（例: 全ユーザーに許可）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'is_done' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * 属性名のカスタマイズ（日本語対応）
     */
    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
            'date' => '日付',
            'is_done' => '完了',
        ];
    }

    /**
     * メッセージカスタマイズ（任意）
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'date.required' => '日付は必須です。',
            'date.date' => '有効な日付を入力してください。',
        ];
    }
}
