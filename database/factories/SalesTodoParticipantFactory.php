<?php

namespace Database\Factories;

use App\Models\SalesTodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesTodoParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'sales_todo_id' => function () {
                return SalesTodo::inRandomOrder()->first()->id;
            },
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
        ];
    }
}
