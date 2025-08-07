<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class UpdateMeeting extends FormRequest
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
            'title' => ['required', 'max:255'],
            'started_at' => ['required', 'date'],
            'participants' => ['nullable'],
            'content' => ['nullable'],
            'files' => ['array','nullable'],
        ];
    }
}
