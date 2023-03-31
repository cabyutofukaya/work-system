<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * ユーザ情報インポートクラス
 *
 * - バリデーションですべてのエラーを収集するためバッチインサートを行う
 *   c.f. https://docs.laravel-excel.com/3.1/imports/validation.html#gathering-all-failures-at-the-end
 */
class UsersImport implements ToModel, WithValidation, WithHeadingRow, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            "username" => $row["username"],
            "name" => $row["name"],
            "email" => $row["email"],
            "password" => $row["password"],
            "tel" => $row["tel"],
            "department" => $row["department"],
        ]);
    }

    public function rules(): array
    {
        return [
            "username" => ["required"],
            "name" => ["required"],
            'email' => ['nullable', 'email:filter'],
            'password' => ['required'],
            'password_source' => ['required', 'string', Password::min(6)],
            'tel' => ['nullable', 'string', 'max:255', 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'department' => ['nullable'],
        ];
    }

    /**
     * バリデーションのためにデータを準備
     */
    public function prepareForValidation($data, $index): array
    {
        // 空白文字をトリム
        $data = array_map("trim", $data);

        // パスワードハッシュを生成
        $data["password_source"] = $data["password"];
        $data["password"] = Hash::make($data["password_source"]);

        return $data;
    }

    /**
     * バッチインサートで一度に実行されるクエリ数
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 999999;
    }
}
