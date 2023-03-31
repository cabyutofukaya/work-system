<?php

namespace Database\Factories;

use App\Models\ReportContent;
use App\Models\ReportContentLike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportContentLikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportContentLike::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return User::inRandomOrder()->first()->id;
            },
            'report_content_id' => function () {
                return ReportContent::inRandomOrder()->first()->id;
            },
        ];
    }
}
