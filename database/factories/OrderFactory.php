<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => 'ORD-' . $this->faker->date('Ymd') . '-' . $this->faker->unique()->numberBetween(10000, 99999),
            'user_id' => 1, // default, bisa di override di seeder
            'status' => 'confirmed',
            'subtotal' => $this->faker->randomFloat(2, 100000, 500000),
            'tax' => $this->faker->randomFloat(2, 10000, 50000),
            'total' => $this->faker->randomFloat(2, 110000, 550000),
            'customer_notes' => $this->faker->sentence(),
            'admin_notes' => $this->faker->sentence(),
            'expected_completion_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'actual_completion_date' => null,
            'custom_design_file' => null,
            'custom_bom_id' => null,
            'price_offer' => null,
            'price_accepted' => null,
        ];
    }
}
