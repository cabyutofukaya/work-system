<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // 会社タイプ・カテゴリー
        $type = $this->faker->randomElement(array_keys(config("const.vehicle_types")));

        return [
            'client_id' => function () {
                return Client::inRandomOrder()->first()->id;
            },
            'image' => null, // コールバックで生成
            'type' => $type,
            'name' => "", // コールバックで生成
            'description' => $this->faker->realText(rand(150, 200)),
        ];
    }

    /**
     * モデルファクトリの設定
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Vehicle $vehicle) {
            // 会社タイプごとのサンプル画像ディレクトリからファイルリストを取得
            $files = File::files(base_path("database/seeders/vehicle-images/" . $vehicle->type));

            $types = ($vehicle->type === "taxi")
                ? ["クラウンコンフォート", "セドリック", "プリウスα", "クルー", "ジャパンタクシー"]
                : ["ローザ", "エアロミディ", "エアロバス", "ジャーニー", "レインボー"];

            $vehicle->fill([
                'image' => Storage::disk(config('admin.upload.disk'))->putFile('images/vehicles', Arr::random($files)->getPathname()),
                'name' => $this->faker->randomElement($types),
            ])->save();
        });
    }
}
