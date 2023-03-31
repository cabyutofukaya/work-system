<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesTodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $is_completed = $this->faker->boolean(20);

        // 対応済みであれば過去、そうでなければ未来の日付を設定
        $scheduled_at = ($is_completed)
            ? now()->subSeconds(rand(0, 60 * 60 * 21 * 31))
            : now()->addSeconds(rand(0, 60 * 60 * 21 * 31));

        return [
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
            'client_id' => function () {
                return Client::inRandomOrder()->first()->id;
            },
            'scheduled_at' => $scheduled_at,
            'contact_person' => $this->faker->name(),
            'description' => $this->faker->realText(rand(150, 200)),
            'is_completed' => $is_completed,
        ];
    }
}
