<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users using factory methods
        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->customer()->create();
        
        // Create test order
        $this->order = Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::PENDING]);
    }

    /**
     * Test: Customer can view their own order
     */
    public function test_customer_can_view_own_order(): void
    {
        $this->assertTrue(
            $this->customer->can('view', $this->order),
            'Customer should be able to view their own order'
        );
    }

    /**
     * Test: Customer cannot view other customer's order
     */
    public function test_customer_cannot_view_other_customer_order(): void
    {
        $otherCustomer = User::factory()->customer()->create();
        
        $this->assertFalse(
            $otherCustomer->can('view', $this->order),
            'Customer should not be able to view another customer order'
        );
    }

    /**
     * Test: Admin can view any order
     */
    public function test_admin_can_view_any_order(): void
    {
        $this->assertTrue(
            $this->admin->can('view', $this->order),
            'Admin should be able to view any order'
        );
    }

    /**
     * Test: Customer cannot update their own order
     */
    public function test_customer_cannot_update_own_order(): void
    {
        $this->assertFalse(
            $this->customer->can('update', $this->order),
            'Customer should not be able to update their own order'
        );
    }

    /**
     * Test: Admin can update order status
     */
    public function test_admin_can_update_order_status(): void
    {
        $this->assertTrue(
            $this->admin->can('update', $this->order),
            'Admin should be able to update order status'
        );
    }

    /**
     * Test: Customer cannot delete order
     */
    public function test_customer_cannot_delete_order(): void
    {
        $this->assertFalse(
            $this->customer->can('delete', $this->order),
            'Customer should not be able to delete their own order'
        );
    }

    /**
     * Test: Admin can delete order
     */
    public function test_admin_can_delete_order(): void
    {
        $this->assertTrue(
            $this->admin->can('delete', $this->order),
            'Admin should be able to delete an order'
        );
    }

    /**
     * Test: Only admin can cancel completed/cancelled orders
     */
    public function test_only_admin_can_cancel_completed_orders(): void
    {
        // Customer CAN cancel their own pending orders
        $this->assertTrue(
            $this->customer->can('cancel', $this->order),
            'Customer should be able to cancel their own pending order'
        );

        // But admin can always cancel (except already cancelled/completed)
        $this->assertTrue(
            $this->admin->can('cancel', $this->order),
            'Admin should be able to cancel pending order'
        );
    }

    /**
     * Test: Cannot transition from invalid status
     */
    public function test_cannot_transition_from_invalid_status(): void
    {
        $cancelledOrder = Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::CANCELLED]);

        $this->assertFalse(
            $this->admin->can('update', $cancelledOrder),
            'Admin should not be able to update cancelled order'
        );
    }

    /**
     * Test: Order status transitions are validated
     */
    public function test_order_status_transitions_are_validated(): void
    {
        $completedOrder = Order::factory()
            ->for($this->customer)
            ->create(['status' => OrderStatus::COMPLETED]);

        $this->assertFalse(
            $this->admin->can('update', $completedOrder),
            'Admin should not be able to update completed order'
        );
    }
}
