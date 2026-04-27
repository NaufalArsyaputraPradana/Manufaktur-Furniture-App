<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->customer()->create();
        
        $this->product = Product::factory()
            ->active()
            ->create();
    }

    /**
     * Test: Customer can view active products
     */
    public function test_customer_can_view_active_products(): void
    {
        $this->assertTrue(
            $this->customer->can('view', $this->product),
            'Customer should be able to view active products'
        );
    }

    /**
     * Test: Customer cannot view inactive products
     */
    public function test_customer_cannot_view_inactive_products(): void
    {
        $inactiveProduct = Product::factory()
            ->inactive()
            ->create();

        $this->assertFalse(
            $this->customer->can('view', $inactiveProduct),
            'Customer should not be able to view inactive products'
        );
    }

    /**
     * Test: Admin can view any product
     */
    public function test_admin_can_view_any_product(): void
    {
        $this->assertTrue(
            $this->admin->can('view', $this->product),
            'Admin should be able to view any product'
        );
    }

    /**
     * Test: Customer cannot create product
     */
    public function test_customer_cannot_create_product(): void
    {
        $this->assertFalse(
            $this->customer->can('create', Product::class),
            'Customer should not be able to create products'
        );
    }

    /**
     * Test: Admin can create product
     */
    public function test_admin_can_create_product(): void
    {
        $this->assertTrue(
            $this->admin->can('create', Product::class),
            'Admin should be able to create products'
        );
    }

    /**
     * Test: Customer cannot update product
     */
    public function test_customer_cannot_update_product(): void
    {
        $this->assertFalse(
            $this->customer->can('update', $this->product),
            'Customer should not be able to update products'
        );
    }

    /**
     * Test: Admin can update product
     */
    public function test_admin_can_update_product(): void
    {
        $this->assertTrue(
            $this->admin->can('update', $this->product),
            'Admin should be able to update products'
        );
    }

    /**
     * Test: Customer cannot delete product
     */
    public function test_customer_cannot_delete_product(): void
    {
        $this->assertFalse(
            $this->customer->can('delete', $this->product),
            'Customer should not be able to delete products'
        );
    }

    /**
     * Test: Admin can delete product
     */
    public function test_admin_can_delete_product(): void
    {
        $this->assertTrue(
            $this->admin->can('delete', $this->product),
            'Admin should be able to delete products'
        );
    }

    /**
     * Test: Admin can manage product pricing
     */
    public function test_admin_can_manage_pricing(): void
    {
        $this->assertTrue(
            $this->admin->can('update', $this->product),
            'Admin should be able to manage product pricing'
        );
    }

    /**
     * Test: Admin can toggle product active status
     */
    public function test_admin_can_toggle_active_status(): void
    {
        $this->assertTrue(
            $this->admin->can('update', $this->product),
            'Admin should be able to toggle product active status'
        );
    }
}
