<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Report;
use App\Models\ReportContent;
use App\Models\SalesMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportContent::class;

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
            'type' => $this->faker->randomElement(array_keys(config("const.report_content_type"))),
            // workの場合のみタイトルを設定
            'title' => function (array $attributes) {
                return ($attributes['type'] === "work") ? $this->faker->realText(rand(10, 30)) : null;
            },
            // salesの場合のみ会社ID・面談者・営業手段IDを設定
            'client_id' => function (array $attributes) {
                return ($attributes['type'] === "sales") ? Client::inRandomOrder()->first()->id : null;
            },
            'participants' => function (array $attributes) {
                if ($attributes['type'] !== "sales") {
                    return null;
                }

                foreach (range(1, random_int(1, 3)) as $count) {
                    $participants[] = $this->faker->name();
                }

                return implode(" ", $participants);
            },
            'sales_method_id' => function (array $attributes) {
                return ($attributes['type'] === "sales") ? SalesMethod::inRandomOrder()->first()->id : null;
            },
            'description' => $this->faker->realText(rand(150, 200)),
            'is_complaint' => $this->faker->boolean(20),
        ];
    }

    /**
     * モデルファクトリの設定
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (ReportContent $report_content) {
            // 会社IDに対応する営業所を設定する
            if ($report_content->type === "sales") {
                $branches = Branch::where("client_id", $report_content->client_id);

                if ($branches->exists()) {
                    $report_content->fill([
                        'branch_id' => $branches->inRandomOrder()->first()->id
                    ])->save();
                }
            }
        });
    }
}
