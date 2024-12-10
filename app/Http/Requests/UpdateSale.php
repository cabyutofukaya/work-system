<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class UpdateSale extends FormRequest
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
            'achievements' => ['nullable','integer','min:0','num_check'],
            'budget' => ['nullable','integer','min:0','num_check'],
            'count' => ['nullable','integer','min:0'],
            'profit' => ['nullable','integer','min:0','num_check'],
            'year' => ['required'],
            'month' => ['required'],
            'category' => ['required'],
        ];
    }


    public function messages()
    {
        return [
            '*.num_check' => ':attributeは、1000の単位で入力してください',
        ];
    }
}
