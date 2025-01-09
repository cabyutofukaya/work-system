<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class StoreBooking extends FormRequest
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
        $attributes = Lang::get("validation.attributes.meetings");
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
            'started_at' => ['required'],
            'started_time' => ['required'],
            'time' => ['required'],
            'title' => ['nullable','string'],
            'room_id' => ['required'],
            'time' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'started_time.required' => '開始時間を指定してください。',
            'started_at.required' => '開始日を指定してください。',
            'time.required' => '利用時間を指定してください。',
            'room_id.required' => '会議室種別を指定してください。',
        ];
    }
}
