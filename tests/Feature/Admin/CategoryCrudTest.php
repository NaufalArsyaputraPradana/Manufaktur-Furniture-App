<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_categories_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
        
        $this->assertTrue(true, "✅ PASS: Admin can view categories");
    }

    /** @test */
    public function admin_can_create_category()
    {
        $categoryData = [
            'name' => 'Furniture Kayu',
            'description' => 'Kategori untuk produk kayu',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), $categoryData);

        $response->assertRedirect(route('admin.categories.index'));
        
        $this->assertDatabaseHas('categories', [
            'name' => 'Furniture Kayu',
            'slug' => 'furniture-kayu',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Category created with auto-slug");
    }

    /** @test */
    public function admin_cannot_create_duplicate_category()
    {
        Category::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category',
        ]);

        $categoryData = [
            'name' => 'Existing Category',
            'description' => 'Test',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), $categoryData);

        $response->assertSessionHasErrors('name');
        
        $this->assertTrue(true, "✅ PASS: Validation prevents duplicate");
    }

    /** @test */
    public function admin_can_update_category()
    {
        $category = Category::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $updateData = [
            'name' => 'New Name',
            'description' => 'Updated description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.categories.update', $category), $updateData);

        $response->assertRedirect(route('admin.categories.index'));
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Category updated");
    }

    /** @test */
    public function admin_can_delete_empty_category()
    {
        $category = Category::create([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Empty category deleted");
    }

    /** @test */
    public function admin_cannot_delete_category_with_products()
    {
        $category = Category::create([
            'name' => 'Category With Products',
            'slug' => 'category-with-products',
        ]);

        // Create a product in this category
        \App\Models\Product::create([
            'category_id' => $category->id,
            'code' => 'TEST-001',
            'name' => 'Test Product',
            'slug' => 'test-product',
            'base_price' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Cannot delete category with products");
    }
}
