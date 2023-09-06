<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class UpdateReport extends FormRequest
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
        $attributes = Lang::get("validation.attributes.reports");
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
            // 更新・削除される日報コンテンツIDがルートパラメータで指定された日報に属しているかのチェックは省略しコントローラ側リレーションで制限
            'date' => ['required'],
            'is_private' => ['required', 'boolean'],
            // 日報コンテンツ
            'report_contents' => ['required', 'array'],
            'report_contents.*.id' => ['nullable', Rule::exists('report_contents', "id")],
            'report_contents.*.type' => ['required', 'in:' . implode(",", array_keys(config("const.report_content_type")))],
            'report_contents.*.description' => ['nullable', 'string'],
            'report_contents.*.is_complaint' => ['required', 'boolean'],
            'report_contents.*.is_zaitaku' => ['required', 'boolean'],
            // type:workのみ
            'report_contents.*.title' => ['required_if:report_contents.*.type,work'],
            // type:salesのみ
            'report_contents.*.client_id' => ['required_if:report_contents.*.type,sales', 'nullable', Rule::exists('clients', "id")],
            'report_contents.*.branch_id' => ['nullable', Rule::exists('branches', "id")],
            'report_contents.*.participants' => ['required_if:report_contents.*.type,sales'],
            'report_contents.*.sales_method_id' => ['required_if:report_contents.*.type,sales', 'nullable', Rule::exists('sales_methods', "id")],
            'report_contents.*.product_evaluation' => ['nullable', 'array'],
            'report_contents.*.product_evaluation.*.product_id' => [Rule::exists('products', "id")],

            'report_contents.*.required_time' => ['required_if:report_contents.*.type,sales','nullable', 'string'],
            'report_contents.*.departments' => ['nullable','string'],
            'report_contents.*.position' => ['nullable','string'],

            // 'report_contents.*.required_time' => ['required', 'string'],
            // 'report_contents.*.departments' => ['string'],

            'report_contents.*.product_evaluation.*.evaluation_id' => [Rule::exists('evaluations', "id")],
            // type:salesにおいて指定された会社IDに所属する営業所IDが指定されているかチェック
            'report_contents.*' => Rule::forEach(function ($value, $attribute, $attribute2) {
                return [
                    function ($attribute, $value, $fail) {
                        if (!empty($value["branch_id"]) && Branch::find($value["branch_id"])->client_id !== $value["client_id"]) {
                            $fail('指定された営業所ID:' . $value["branch_id"] . 'は会社ID:' . $value["client_id"] . 'に所属していません');
                        }
                    },
                ];
            }),
            // 削除する日報コンテンツID
            '_delete_report_content_ids' => ['nullable', 'array'],
        ];
    }
}
