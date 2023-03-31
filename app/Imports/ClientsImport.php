<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * 会社情報インポートクラス
 *
 * - バリデーションですべてのエラーを収集するためバッチインサートを行う
 *   c.f. https://docs.laravel-excel.com/3.1/imports/validation.html#gathering-all-failures-at-the-end
 */
class ClientsImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts
{
    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row)
        {
            $client = Client::create([
                "client_type_id" => $row["client_type_id"],
                "name" => $row["name"],
                "name_kana" => $row["name_kana"],
                "postcode" => $row["postcode"],
                "prefecture" => $row["prefecture"],
                "address" => $row["address"],
                "url" => $row["url"],
                "email" => $row["email"],
                "representative" => $row["representative"],
                "tel" => $row["tel"],
                "fax" => $row["fax"],
                "business_hours" => $row["business_hours"],
                "description" => $row["description"],
            ]);

            // 空の会社タイプ固有情報を生成
            if ($row["client_type_id"] === "taxibus") {
                $client->client_type_taxibus()->create();
            } else if ($row["client_type_id"] === "truck") {
                $client->client_type_truck()->create();
            } else if ($row["client_type_id"] === "restaurant") {
                $client->client_type_restaurant()->create();
            } else if ($row["client_type_id"] === "travel") {
                $client->client_type_travel()->create();
            }
        }
    }

    public function rules(): array
    {
        return [
            "client_type_id" => ["required", Rule::in(array_keys(config("const.client_types")))],
            "name" => ["required"],
            "name_kana" => ["nullable"],
            'postcode' => ['nullable', 'regex:/^[0-9]{7}$/'],
            'prefecture' => ['nullable', Rule::in(config("const.prefectures"))],
            'address' => ['nullable'],
            'url' => ['nullable', 'url'],
            'email' => ['nullable', 'email:filter'],
            'representative' => ['nullable'],
            'tel' => ['nullable', 'max:255', 'regex:/^0[0-9][-0-9]+[0-9]$/'],
            'fax' => ['nullable', 'max:255', 'regex:/^0[0-9][-0-9]+[0-9]$/'],
            'business_hours' => ['nullable'],
            'description' => ['nullable'],
        ];
    }

    /**
     * バリデーションのためにデータを準備
     */
    public function prepareForValidation($data, $index): array
    {
        // 空白文字をトリム
        $data = array_map("trim", $data);

        // 郵便番号から数字以外を削除
        $data['postcode'] = preg_replace('/\D/', '', $data['postcode']);

        return $data;
    }

    /**
     * バッチインサートで一度に実行されるクエリ数
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }
}
