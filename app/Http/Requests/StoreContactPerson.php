<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class StoreContactPerson extends FormRequest
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
        $attributes = Lang::get("validation.attributes.contact_persons");
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
            'client_id' => ['required', Rule::exists('clients', "id")],
            'name' => ['required'],
            'email' => ['nullable', 'email:filter'],
            'tel' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'department' => ['nullable'],
            'position' => ['nullable'],
        ];
    }
}
