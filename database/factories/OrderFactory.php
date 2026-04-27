<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $subtotal = $this->faker->randomFloat(2, 100000, 500000);
        $total = $subtotal + $this->faker->randomFloat(2, 10000, 50000);
        
        return [
            'order_number' => 'ORD-' . $this->faker->date('Ymd') . '-' . $this->faker->unique()->numberBetween(10000, 99999),
            'user_id' => User::factory(),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'total' => $total,
            'customer_notes' => $this->faker->sentence(),
            'admin_notes' => $this->faker->sentence(),
            'order_date' => $this->faker->dateTime(),
            'expected_completion_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'actual_completion_date' => null,
        ];
    }
}
