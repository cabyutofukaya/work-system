<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessDistrictFactory extends Factory
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
            'prefecture' => $this->faker->prefecture(),
            'address' => $this->faker->city(),
        ];
    }
}
