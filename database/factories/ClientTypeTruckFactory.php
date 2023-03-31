<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientTypeTruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'drivers_count' => $this->faker->randomElement(config("const.client_types.truck.drivers_count")),
        ];
    }
}
