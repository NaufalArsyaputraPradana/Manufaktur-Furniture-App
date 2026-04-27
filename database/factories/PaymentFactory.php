<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 500000, 5000000);
        
        return [
            'order_id' => Order::factory(),
            'amount' => $amount,
            'amount_paid' => 0,
            'expected_dp_amount' => $amount * 0.3,
            'payment_status' => Payment::STATUS_PENDING,
            'payment_method' => $this->faker->randomElement(['transfer', 'cash', 'credit_card']),
            'payment_channel' => $this->faker->randomElement([Payment::CHANNEL_MANUAL_DP, Payment::CHANNEL_MANUAL_FULL, Payment::CHANNEL_MIDTRANS]),
            'transaction_id' => 'TXN-' . $this->faker->unique()->numerify('##########'),
            'payment_proof' => null,
            'payment_proof_dp' => null,
            'payment_proof_full' => null,
            'payment_date' => null,
        ];
    }

    /**
     * Payment is fully paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => Payment::STATUS_PAID,
            'amount_paid' => $attributes['amount'],
            'payment_date' => now(),
        ]);
    }

    /**
     * Payment is pending verification.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
            'amount_paid' => 0,
        ]);
    }
}
