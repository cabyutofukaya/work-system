<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientTypeTaxibusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $category = $this->faker->randomElement(array_keys(config("const.client_types.taxibus.categories")));
        $has_child_seat = $this->faker->boolean();
        $has_junior_seat = $this->faker->boolean();

        return [
            'client_id' => function () {
                return Client::inRandomOrder()->first()->id;
            },
            'membership_fee' => 100 * rand(1, 10),
            'fee_taxi_cab' => function (array $attributes) {
                return Str::contains($attributes['category'], 'taxi') ? 100 * rand(1, 10) : null;
            },
            'fee_taxi_tabinoashi' => function (array $attributes) {
                return Str::contains($attributes['category'], 'taxi') ? 100 * rand(1, 10) : null;
            },
            'fee_bus_cab' => function (array $attributes) {
                return Str::contains($attributes['category'], 'bus') ? 100 * rand(1, 10) : null;
            },
            'fee_bus_tabinoashi' => function (array $attributes) {
                return Str::contains($attributes['category'], 'bus') ? 100 * rand(1, 10) : null;
            },
            'category' => $category,
            'has_dr_sightseeing' => $this->faker->boolean(),
            'has_dr_female' => $this->faker->boolean(),
            'has_dr_language_english' => $this->faker->boolean(),
            'has_dr_language_chinese' => $this->faker->boolean(),
            'has_dr_language_korean' => $this->faker->boolean(),
            'has_dr_language_other' => $this->faker->boolean(),
            'has_wheelchair' => $this->faker->boolean(),
            'has_baby_seat' => $this->faker->boolean(),
            'has_child_seat' => $has_child_seat,
            'fee_child_seat' => $has_child_seat ? $this->faker->randomElement([0, 500]) : null,
            'has_junior_seat' => $has_junior_seat,
            'fee_junior_seat' => $has_junior_seat ? $this->faker->randomElement([0, 500]) : null,
            'is_bus_association_member' => Str::contains($category, 'bus') ? $this->faker->boolean() : null,
            'has_safety_mark' => $this->faker->boolean(),
        ];
    }
}
