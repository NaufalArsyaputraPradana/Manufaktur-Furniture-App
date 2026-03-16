<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\BillOfMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ComprehensiveCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    private function createRolesAndUsers()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator']
        );
        $customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['description' => 'Customer']
        );
        $staffRole = Role::firstOrCreate(
            ['name' => 'production_staff'],
            ['description' => 'Production Staff']
        );

        // Create users
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
                'role_id' => $staffRole->id,
            ]
        );

        return compact('admin', 'customer', 'staff');
    }

    /** @test */
    public function test_category_crud_operations()
    {
        $users = $this->createRolesAndUsers();
        $this->actingAs($users['admin']);

        // CREATE
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Test Category',
            'description' => 'Test Description',
            'is_active' => true,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);

        $category = Category::where('name', 'Test Category')->first();

        // READ
        $response = $this->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Category');

        $response = $this->get(route('admin.categories.show', $category));
        $response->assertStatus(200);

        // UPDATE
        $response = $this->patch(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'is_active' => true,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);

        // DELETE
        $response = $this->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function test_product_crud_operations()
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'is_active' => true,
        ]);

        // CREATE
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

        $product = Product::where('name', 'Test Product')->first();

        // READ
        $response = $this->get(route('admin.products.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.products.show', $product));
        $response->assertStatus(200);

        // UPDATE
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

        // DELETE
        $response = $this->delete(route('admin.products.destroy', $product));
        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    /** @test */
    public function test_user_crud_operations()
    {
        $this->actingAs($this->admin);

        $role = Role::where('name', 'customer')->first();

        // CREATE
        $response = $this->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);

        $user = User::where('email', 'newuser@test.com')->first();

        // READ
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.users.show', $user));
        $response->assertStatus(200);

        // UPDATE
        $response = $this->patch(route('admin.users.update', $user), [
            'name' => 'Updated User',
            'email' => 'newuser@test.com',
            'role_id' => $role->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['name' => 'Updated User']);

        // DELETE
        $response = $this->delete(route('admin.users.destroy', $user));
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function test_authorization_works()
    {
        // Customer cannot access admin routes
        $this->actingAs($this->customer);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Staff cannot access admin routes
        $this->actingAs($this->staff);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Admin can access admin routes
        $this->actingAs($this->admin);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }
}
