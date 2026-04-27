<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        return [
            'report_type' => $this->faker->randomElement(['sales', 'production', 'inventory', 'profitability']),
            'title' => $this->faker->sentence(3),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'filters' => [
                'date_range' => 'monthly',
                'format' => 'pdf',
            ],
            'data' => [
                'total_sales' => $this->faker->numberBetween(1000000, 50000000),
                'total_orders' => $this->faker->numberBetween(10, 500),
                'average_order' => $this->faker->numberBetween(100000, 5000000),
            ],
            'generated_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the report should be a sales report.
     */
    public function sales(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => 'sales',
            'title' => 'Sales Report - ' . now()->format('F Y'),
        ]);
    }

    /**
     * Indicate that the report should be a production report.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => 'production',
            'title' => 'Production Report - ' . now()->format('F Y'),
        ]);
    }

    /**
     * Indicate that the report should be completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the report should have failed status.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
