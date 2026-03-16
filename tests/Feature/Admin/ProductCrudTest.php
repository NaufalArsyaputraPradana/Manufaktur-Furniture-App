<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
        ]);
    }

    /** @test */
    public function admin_can_view_products_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
        
        $this->assertTrue(true, "✅ PASS: Admin can view products");
    }

    /** @test */
    public function admin_can_create_product_with_image()
    {
        $image = UploadedFile::fake()->image('product.jpg', 800, 600);

        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'sku' => 'PROD-001',
            'description' => 'Test description',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
            'is_custom' => false,
            'image' => $image,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'PROD-001',
            'slug' => 'test-product',
        ]);
        
        // Verify image was uploaded
        $product = Product::where('sku', 'PROD-001')->first();
        $this->assertNotNull($product->image);
        
        $this->assertTrue(true, "✅ PASS: Product with image created");
    }

    /** @test */
    public function admin_cannot_create_product_with_duplicate_sku()
    {
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Existing Product',
            'slug' => 'existing-product',
            'sku' => 'PROD-001',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'category_id' => $this->category->id,
                'name' => 'New Product',
                'sku' => 'PROD-001',
                'description' => 'Test',
                'base_price' => 2000000,
                'estimated_production_days' => 5,
            ]);

        $response->assertSessionHasErrors('sku');
        
        $this->assertTrue(true, "✅ PASS: Duplicate SKU prevented");
    }

    /** @test */
    public function admin_can_update_product()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Original Name',
            'slug' => 'original-name',
            'sku' => 'PROD-001',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'name' => 'Updated Name',
                'sku' => 'PROD-001',
                'description' => 'Updated description',
                'base_price' => 1500000,
                'estimated_production_days' => 10,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'base_price' => 1500000,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Product updated");
    }

    /** @test */
    public function admin_can_delete_product_without_orders()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'sku' => 'PROD-001',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Product deleted");
    }

    /** @test */
    public function custom_product_flag_works()
    {
        $customProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Custom Design',
            'slug' => 'custom-design',
            'sku' => 'CUSTOM-001',
            'base_price' => 5000000,
            'estimated_production_days' => 14,
            'is_custom' => true,
        ]);

        $this->assertTrue($customProduct->is_custom);
        $this->assertTrue(true, "✅ PASS: Custom product flag works");
    }

    /** @test */
    public function product_auto_generates_slug()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Amazing Furniture Item',
            'sku' => 'PROD-001',
            'base_price' => 1000000,
            'estimated_production_days' => 7,
        ]);

        $this->assertEquals('amazing-furniture-item', $product->slug);
        $this->assertTrue(true, "✅ PASS: Slug auto-generated");
    }
}
