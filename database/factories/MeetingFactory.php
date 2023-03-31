<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $names = [];
        foreach (range(1, rand(1, 3)) as $count) {
            $names[] = $this->faker->name();
        }

        return [
            'started_at' => now()->subSeconds(rand(0, 60 * 60 * 21 * 31)),
            'title' => $this->faker->realText(rand(10, 30)),
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
            'participants' => implode("\n", $names),
            'content' => $this->faker->realText(rand(150, 200)),
        ];
    }
}
