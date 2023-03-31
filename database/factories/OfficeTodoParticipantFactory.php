<?php

namespace Database\Factories;

use App\Models\OfficeTodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeTodoParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'office_todo_id' => function () {
                return OfficeTodo::inRandomOrder()->first()->id;
            },
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
        ];
    }
}
