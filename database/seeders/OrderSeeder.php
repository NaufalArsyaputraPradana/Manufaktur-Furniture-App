<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get or create test customer
        $customerRole = Role::where('name', 'customer')->first();
        $customer = User::where('role_id', $customerRole->id)->first() 
            ?? User::factory()->create(['role_id' => $customerRole->id]);

        // Get products for order details
        $products = Product::all();
        if ($products->isEmpty()) {
            $products = Product::factory()->count(5)->create();
        }

        // ============================================
        // NEW ORDERS (Pesanan Baru)
        // ============================================
        for ($i = 0; $i < 2; $i++) {
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'pending',
                'total' => 0,
                'expected_completion_date' => Carbon::now()->addDays(14),
                'customer_notes' => 'Order pesanan custom furniture',
                'order_date' => Carbon::now()->subDays(random_int(1, 5)),
            ]);

            // Add 2-3 items per order
            $this->addOrderDetails($order, $products, random_int(2, 3));
        }

        // ============================================
        // CONFIRMED ORDERS (Pesanan Dikonfirmasi)
        // ============================================
        for ($i = 0; $i < 3; $i++) {
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 10, 4, '0', STR_PAD_LEFT),
                'status' => 'confirmed',
                'total' => 0,
                'expected_completion_date' => Carbon::now()->addDays(21),
                'customer_notes' => 'Sudah dikonfirmasi oleh admin',
                'order_date' => Carbon::now()->subDays(random_int(6, 10)),
            ]);

            $this->addOrderDetails($order, $products, random_int(2, 4));
        }

        // ============================================
        // IN PRODUCTION (Dalam Proses Produksi)
        // ============================================
        for ($i = 0; $i < 3; $i++) {
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 20, 4, '0', STR_PAD_LEFT),
                'status' => 'in_production',
                'total' => 0,
                'expected_completion_date' => Carbon::now()->addDays(7),
                'customer_notes' => 'Sedang dikerjakan',
                'order_date' => Carbon::now()->subDays(random_int(11, 15)),
            ]);

            $this->addOrderDetails($order, $products, random_int(1, 3));
        }

        // ============================================
        // COMPLETED ORDERS (Pesanan Selesai)
        // ============================================
        for ($i = 0; $i < 2; $i++) {
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 30, 4, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'total' => 0,
                'expected_completion_date' => Carbon::now()->subDays(random_int(1, 10)),
                'actual_completion_date' => Carbon::now()->subDays(random_int(1, 10)),
                'shipped_at' => Carbon::now()->subDays(random_int(3, 8)),
                'delivered_at' => Carbon::now()->subDays(random_int(1, 5)),
                'customer_notes' => 'Pesanan selesai dan telah dikirim',
                'order_date' => Carbon::now()->subDays(random_int(16, 30)),
            ]);

            $this->addOrderDetails($order, $products, random_int(1, 2));
        }

        // ============================================
        // CANCELLED ORDERS (Pesanan Dibatalkan)
        // ============================================
        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . '9999',
            'status' => 'cancelled',
            'total' => 0,
            'expected_completion_date' => Carbon::now()->subDays(15),
            'customer_notes' => 'Pesanan dibatalkan oleh customer',
            'order_date' => Carbon::now()->subDays(20),
        ]);

        $this->addOrderDetails($order, $products, random_int(1, 2));
    }

    /**
     * Add order details to order
     *
     * @param Order $order
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param int $count
     * @return void
     */
    private function addOrderDetails($order, $products, $count)
    {
        $totalPrice = 0;

        for ($i = 0; $i < $count; $i++) {
            $product = $products->random();
            $quantity = random_int(1, 3);
            $unitPrice = $product->price ?? 50000;
            $subtotal = $unitPrice * $quantity;

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            $totalPrice += $subtotal;
        }

        // Update order with total
        $order->update([
            'total' => $totalPrice,
        ]);
    }
}
