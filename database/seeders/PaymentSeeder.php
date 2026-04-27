<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get orders
        $orders = Order::all();

        if ($orders->isEmpty()) {
            return;
        }

        foreach ($orders as $order) {
            // Skip cancelled orders
            if ($order->status === 'cancelled') {
                continue;
            }

            // Create payment based on order status
            $amount = $order->total ?? 50000;

            // ============================================
            // PENDING ORDERS - Unpaid
            // ============================================
            if ($order->status === 'pending') {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => round($amount * 0.5),
                    'payment_method' => 'transfer',
                    'payment_status' => 'pending',
                    'created_at' => $order->created_at,
                ]);
            }
            // ============================================
            // CONFIRMED ORDERS - DP Paid
            // ============================================
            elseif ($order->status === 'confirmed') {
                // DP Payment - Success
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => round($amount * 0.5),
                    'amount_paid' => round($amount * 0.5),
                    'expected_dp_amount' => round($amount * 0.5),
                    'payment_method' => 'transfer',
                    'payment_status' => 'dp_paid',
                    'created_at' => $order->created_at->addDays(1),
                ]);

                // Remaining Payment - Pending
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => round($amount * 0.5),
                    'amount_paid' => round($amount * 0.5),
                    'payment_method' => 'transfer',
                    'payment_status' => 'full_pending',
                    'created_at' => $order->created_at->addDays(2),
                ]);
            }
            // ============================================
            // IN PRODUCTION / COMPLETED - Fully Paid
            // ============================================
            elseif (in_array($order->status, ['in_production', 'completed'])) {
                // DP Payment
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => round($amount * 0.5),
                    'amount_paid' => round($amount * 0.5),
                    'expected_dp_amount' => round($amount * 0.5),
                    'payment_method' => 'transfer',
                    'payment_status' => 'dp_paid',
                    'created_at' => $order->created_at->addDays(1),
                ]);

                // Remaining Payment
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => round($amount * 0.5),
                    'amount_paid' => round($amount * 0.5),
                    'payment_method' => 'transfer',
                    'payment_status' => 'paid',
                    'created_at' => Carbon::now()->subDays(random_int(1, 5)),
                ]);
            }
        }
    }
}
