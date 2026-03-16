<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin()
    {
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_category_crud_works()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        // Create
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Test Category',
            'description' => 'Test Description',
            'is_active' => true,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);

        $category = Category::first();

        // Read
        $response = $this->get(route('admin.categories.index'));
        $response->assertStatus(200);

        // Update
        $response = $this->patch(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'is_active' => true,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);

        // Delete
        $response = $this->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_product_crud_works()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $category = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'is_active' => true,
        ]);

        // Create
        $response = $this->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'category_id' => $category->id,
            'description' => 'Test Description',
            'base_price' => 100000,
            'is_active' => true,
            'is_customizable' => false,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);

        $product = Product::first();

        // Read
        $response = $this->get(route('admin.products.index'));
        $response->assertStatus(200);

        // Update
        $response = $this->patch(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'slug' => 'updated-product',
            'category_id' => $category->id,
            'description' => 'Updated Description',
            'base_price' => 150000,
            'is_active' => true,
            'is_customizable' => false,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);

        // Delete (Soft Delete)
        $response = $this->delete(route('admin.products.destroy', $product));
        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_authorization_works()
    {
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        $customerRole = Role::create(['name' => 'customer', 'description' => 'Customer']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
        ]);

        // Customer cannot access admin routes
        $this->actingAs($customer);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Admin can access admin routes
        $this->actingAs($admin);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }
}
