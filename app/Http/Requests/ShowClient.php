<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class ShowClient extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * このモデル特有の訳語を設定
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $attributes = Lang::get("validation.attributes.clients");
        return is_array($attributes) ? $attributes : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'word' => ['nullable', 'max:255'],
            'address' => ['nullable', 'max:255'],
            'branch' => ['nullable', 'max:255'],
            'business_district' => ['nullable', 'max:255'],
            'category' => ['nullable', 'in:' . implode(",", array_keys(config("const.client_types.taxibus.categories")))],
            'genre_id' => ['nullable', Rule::exists('genres', "id")],
            'vehicle' => ['nullable', 'max:255'],
            // 真偽値 GETメソッドで文字列として送信されるためbooleanルールは利用できない
            'has_dr_female' => ['nullable', 'in:true,false'],
            'has_dr_language_english' => ['nullable', 'in:true,false'],
            'has_dr_language_chinese' => ['nullable', 'in:true,false'],
            'has_dr_language_korean' => ['nullable', 'in:true,false'],
            'has_dr_language_other' => ['nullable', 'in:true,false'],
            'has_wheelchair' => ['nullable', 'in:true,false'],
            'has_baby_seat' => ['nullable', 'in:true,false'],
            'has_child_seat' => ['nullable', 'in:true,false'],
            'has_junior_seat' => ['nullable', 'in:true,false'],
            'is_bus_association_member' => ['nullable', 'in:true,false'],
            'has_safety_mark' => ['nullable', 'in:true,false'],
        ];
    }
}

