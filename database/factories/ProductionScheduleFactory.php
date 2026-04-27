<?php

namespace Database\Factories;

use App\Models\ProductionSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionScheduleFactory extends Factory
{
    protected $model = ProductionSchedule::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+8 hours');
        
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'start_datetime' => $start,
            'end_datetime' => $end,
            'location' => $this->faker->word(),
        ];
    }

    /**
     * Create a morning shift (8 AM - 4 PM).
     */
    public function morningShift(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->addDays($this->faker->numberBetween(1, 30));
            $start = (clone $date)->setTime(8, 0);
            $end = (clone $date)->setTime(16, 0);
            
            return [
                'start_datetime' => $start,
                'end_datetime' => $end,
                'title' => 'Morning Shift',
            ];
        });
    }

    /**
     * Create an afternoon shift (4 PM - 12 AM).
     */
    public function afternoonShift(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->addDays($this->faker->numberBetween(1, 30));
            $start = (clone $date)->setTime(16, 0);
            $end = (clone $date)->setTime(0, 0)->addDay();
            
            return [
                'start_datetime' => $start,
                'end_datetime' => $end,
                'title' => 'Afternoon Shift',
            ];
        });
    }
}
