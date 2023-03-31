<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // 会社タイプ・カテゴリー
        $client_type_id = $this->faker->randomElement(array_keys(config("const.client_types")));

        // 会社タイプごとのサンプル画像ディレクトリからファイルリストを取得
        $files = File::files(base_path("database/seeders/client-images/" . $client_type_id));

        return [
            'client_type_id' => $client_type_id,
            'image' => Storage::disk(config('admin.upload.disk'))->putFile('images/clients', Arr::random($files)->getPathname()),
            "name" => $this->faker->company(),
            'name_kana' => mb_convert_kana($this->faker->kanaName(), "c"),
            'postcode' => $this->faker->postcode(),
            'prefecture' => $this->faker->prefecture(),
            'address' => $this->faker->city() . $this->faker->streetAddress(),
            'lat' => config("const.location_default.lat") + 0.04 * rand(-100, 100) / 100, // 中心地から+-0.04
            'lng' => config("const.location_default.lng") + 0.06 * rand(-100, 100) / 100, // 中心地から+-0.06
            'url' => $this->faker->url(),
            'email' => $this->faker->unique()->safeEmail(),
            'representative' => $this->faker->name(),
            'tel' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
            'business_hours' => $this->faker->randomElement(range(7, 10)) . ":00 - " . $this->faker->randomElement(range(16, 22)) . ":00",
            'description' => $this->faker->realText(rand(150, 200)),
        ];
    }
}
