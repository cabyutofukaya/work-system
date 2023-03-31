<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class StoreBranch extends FormRequest
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
        $attributes = Lang::get("validation.attributes.branches");
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
            'name' => ['required'],
            'postcode' => ['nullable', 'regex:/^[0-9]{7}$/'],
            'prefecture' => ['nullable', Rule::in(config("const.prefectures"))],
            'address' => ['nullable'],
            'lat' => ['nullable', 'required_with:lng', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'required_with:lat', 'numeric', 'between:-180,180'],
            'tel' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'fax' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
        ];
    }
}
