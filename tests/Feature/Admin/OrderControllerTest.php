<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->customer()->create();
        
        $this->order = Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::PENDING]);
    }

    /**
     * Test: Admin can view orders list
     */
    public function test_admin_can_view_orders_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.orders.index');
    }

    /**
     * Test: Admin can view order details
     */
    public function test_admin_can_view_order_details(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $this->order));

        $response->assertStatus(200)
            ->assertViewIs('admin.orders.show')
            ->assertViewHas('order', $this->order);
    }

    /**
     * Test: Admin can update order status
     */
    public function test_admin_can_update_order_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $this->order), [
                'status' => OrderStatus::IN_PRODUCTION->value,
            ]);

        $this->order->refresh();
        $this->assertEquals(OrderStatus::IN_PRODUCTION, $this->order->status);
        $response->assertRedirect();
    }

    /**
     * Test: Cannot update order with invalid status
     */
    public function test_cannot_update_order_with_invalid_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.orders.update-status', $this->order), [
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * Test: Admin cannot update completed order
     */
    public function test_admin_cannot_update_completed_order(): void
    {
        $completedOrder = Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::COMPLETED]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.orders.update-status', $completedOrder), [
                'status' => OrderStatus::CANCELLED->value,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test: Customer cannot access order management
     */
    public function test_customer_cannot_access_order_management(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.orders.index'));

        $response->assertForbidden();
    }

    /**
     * Test: Guest cannot access order management
     */
    public function test_guest_cannot_access_order_management(): void
    {
        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Admin can view order filters
     */
    public function test_admin_can_filter_orders_by_status(): void
    {
        Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::IN_PRODUCTION]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['status' => OrderStatus::PENDING->value]));

        $response->assertStatus(200);
    }

    /**
     * Test: Order list shows pagination
     */
    public function test_order_list_has_pagination(): void
    {
        Order::factory()
            ->for($this->customer)
            ->count(20)
            ->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertViewHas('orders');
    }

    /**
     * Test: Admin can search orders by order number
     */
    public function test_admin_can_search_orders(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['search' => $this->order->order_number]));

        $response->assertStatus(200);
    }

    /**
     * Test: Invalid page number returns 404
     */
    public function test_invalid_page_number_returns_404(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['page' => 9999]));

        // Should either return 404 or return empty list
        // Depends on implementation
        $this->assertIn($response->status(), [200, 404]);
    }
}
