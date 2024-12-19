<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditReport extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => ['nullable', Rule::exists('clients', "id")],
            'name' => ['nullable', 'max:255'],
            'client_type_id' => ['nullable', Rule::in(array_keys(config("const.client_types")))],
            'client_type_taxibus_category' => ['nullable', Rule::in(array_keys(config("const.client_types.taxibus.categories")))],
            'genre_id' => ['nullable', Rule::exists('genres', "id")],

            'prefecture' => ['nullable', Rule::in(config("const.prefectures"))],
            'my_charge' => ['nullable'],
        ];
    }
}
