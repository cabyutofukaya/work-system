<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Validator;

class StoreSchedule extends FormRequest
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
        $attributes = Lang::get("validation.attributes.notices");
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
            'title' => ['required'],
            'content' => ['required'],
            'date' => ['required'],
            'start_time' => ['required_if:enabled,false'],
            'end_time' => ['required_if:enabled,false'],
            'start_time' => ['required_if:enabled,false'],
            'end_time' => ['required_if:enabled,false'],
            // 'end_time' => ['after:start_time'],
            // 'end_time' => ['exclude_unless:enabled,false|after:start_time'],
            'start_date' => ['required_if:rangeEnabled,true'],
            'end_date' => ['required_if:rangeEnabled,true'],
            'title' => ['required_without:title_type'],
            'title_type' => ['required_without:title'],
            'content' => ['nullable'],
        ];
    }

    public function withValidator(Validator $validator)
    {

    

     
    }
}
