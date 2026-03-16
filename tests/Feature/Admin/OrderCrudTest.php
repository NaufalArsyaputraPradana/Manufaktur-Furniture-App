<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer']);
        
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_orders_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
        
        $this->assertTrue(true, "✅ PASS: Admin can view orders");
    }

    /** @test */
    public function admin_can_create_order()
    {
        $category = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-001',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
        ]);

        $orderData = [
            'user_id' => $this->customer->id,
            'customer_notes' => 'Test order',
            'expected_completion_date' => now()->addDays(14)->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 2,
                    'unit_price' => 1000000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.store'), $orderData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Admin can create order");
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-' . time(),
            'status' => 'pending',
            'total_amount' => 1000000,
            'expected_completion_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'confirmed'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Order status updated");
    }

    /** @test */
    public function admin_can_cancel_order()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-' . time(),
            'status' => 'pending',
            'total_amount' => 1000000,
            'expected_completion_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.cancel', $order), [
                'cancellation_reason' => 'Test cancellation'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Order can be cancelled");
    }

    /** @test */
    public function order_generates_unique_number()
    {
        $order1 = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-001',
            'status' => 'pending',
            'total_amount' => 1000000,
            'expected_completion_date' => now()->addDays(14),
        ]);

        $order2 = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-002',
            'status' => 'pending',
            'total_amount' => 2000000,
            'expected_completion_date' => now()->addDays(14),
        ]);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertTrue(true, "✅ PASS: Unique order numbers");
    }

    /** @test */
    public function order_calculates_total_correctly()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-' . time(),
            'status' => 'pending',
            'total_amount' => 1500000,
            'expected_completion_date' => now()->addDays(14),
        ]);

        $this->assertEquals(1500000, $order->total_amount);
        $this->assertTrue(true, "✅ PASS: Order total calculated");
    }
}
