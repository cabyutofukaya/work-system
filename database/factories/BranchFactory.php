<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'client_id' => function () {
                return Client::inRandomOrder()->first()->id;
            },
            "name" => $this->faker->lastName() . "営業所",
            'postcode' => $this->faker->postcode(),
            'prefecture' => $this->faker->prefecture(),
            'address' => $this->faker->city() . $this->faker->streetAddress(),
            'lat' => config("const.location_default.lat") + 0.04 * rand(-100, 100) / 100, // 中心地から+-0.04
            'lng' => config("const.location_default.lng") + 0.06 * rand(-100, 100) / 100, // 中心地から+-0.06
            'tel' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
        ];
    }
}
