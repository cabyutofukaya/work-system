<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class StoreClient extends FormRequest
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
            '_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:' . (1024 * 20)], // 20MB
            'name' => ['required'],
            'name_kana' => ['nullable'],
            'postcode' => ['nullable', 'regex:/^[0-9]{7}$/'],
            'prefecture' => ['nullable', Rule::in(config("const.prefectures"))],
            'address' => ['nullable'],
            'lat' => ['nullable', 'required_with:lng', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'required_with:lat', 'numeric', 'between:-180,180'],
            'url' => ['nullable', 'url'],
            'email' => ['nullable', 'email:filter'],
            'representative' => ['nullable'],
            'tel' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'fax' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'business_hours' => ['nullable'],
            'description' => ['nullable'],
            'contact' => ['nullable'],

            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['nullable', Rule::exists('genres', "id")],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['nullable', Rule::exists('products', "id")],

            'client_type_taxibus.membership_fee' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.fee_taxi_cab' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.fee_taxi_tabinoashi' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.fee_bus_cab' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.fee_bus_tabinoashi' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.category' => ['nullable', Rule::in(array_keys(config("const.client_types.taxibus.categories")))],
            'client_type_taxibus.has_dr_sightseeing' => ['nullable', 'boolean'],
            'client_type_taxibus.has_dr_female' => ['nullable', 'boolean'],
            'client_type_taxibus.has_dr_language_english' => ['nullable', 'boolean'],
            'client_type_taxibus.has_dr_language_chinese' => ['nullable', 'boolean'],
            'client_type_taxibus.has_dr_language_korean' => ['nullable', 'boolean'],
            'client_type_taxibus.has_dr_language_other' => ['nullable', 'boolean'],
            'client_type_taxibus.has_wheelchair' => ['nullable', 'boolean'],
            'client_type_taxibus.has_baby_seat' => ['nullable', 'boolean'],
            'client_type_taxibus.has_child_seat' => ['nullable', 'boolean'],
            'client_type_taxibus.fee_child_seat' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.has_junior_seat' => ['nullable', 'boolean'],
            'client_type_taxibus.fee_junior_seat' => ['nullable', 'integer', 'min:0'],
            'client_type_taxibus.is_bus_association_member' => ['nullable', 'boolean'],
            'client_type_taxibus.has_safety_mark' => ['nullable', 'boolean'],

            'client_type_truck.drivers_count' => ['nullable', Rule::in(config("const.client_types.truck.drivers_count"))],

            'client_type_restaurant.languages' => ['nullable', 'array'],
            'client_type_restaurant.languages.*' => ['nullable', Rule::in(config("const.client_types.restaurant.languages"))],

            'client_type_travel.payment_method' => ['nullable', Rule::in(config("const.client_types.travel.payment_methods"))],
            'client_type_travel.registration_number' => ['nullable', 'string'],
            'client_type_travel.group' => ['nullable', Rule::in(config("const.client_types.travel.groups"))],
        ];
    }
}
