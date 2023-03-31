<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientTypeTravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'payment_method' => $this->faker->randomElement(config("const.client_types.travel.payment_methods")),
            'registration_number' => "観光庁長官登録旅行業第" . rand(10000, 99999) . "号",
            'group' => $this->faker->randomElement(config("const.client_types.travel.groups")),
        ];
    }
}
