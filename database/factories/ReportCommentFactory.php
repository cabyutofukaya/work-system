<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'report_id' => function () {
                return Report::inRandomOrder()->first()->id;
            },
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
            'comment' => $this->faker->realText(rand(150, 200)),
        ];
    }
}
