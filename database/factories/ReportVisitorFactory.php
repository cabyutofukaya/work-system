<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ReportVisitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportVisitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportVisitor::class;

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
            'report_id' => function () {
                return Report::inRandomOrder()->first()->id;
            },
        ];
    }
}
