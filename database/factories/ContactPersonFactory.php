<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactPersonFactory extends Factory
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
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'tel' => $this->faker->phoneNumber(),
            'department' => $this->faker->randomElement(["法務部", "システム部", "デザイン部", "人事部", "総務部", "資材部", "経理部", "広報部", "CS部", "開発部", "製造部", "営業部"]),
            'position' => $this->faker->randomElement(["主任", "係長", "課長", "次長", "部長"]),
        ];
    }
}
