<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientTypeRestaurantFactory extends Factory
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
            'languages' => $this->faker->randomElements(config("const.client_types.restaurant.languages"), rand(1, count(config("const.client_types.restaurant.languages")))),
        ];
    }
}
