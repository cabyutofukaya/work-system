<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class StoreClientTelphone extends FormRequest
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
            'client_id' => ['required', Rule::exists('clients', "id")],
            'name' => ['required'],
            'tel' => ['required', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
        ];
    }
}
