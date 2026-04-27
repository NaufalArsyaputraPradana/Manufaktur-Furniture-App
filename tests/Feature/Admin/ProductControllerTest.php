<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create();
    }

    /**
     * Test: Admin can view products list
     */
    public function test_admin_can_view_products_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.products.index');
    }

    /**
     * Test: Admin can view create product form
     */
    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.products.create')
            ->assertViewHas('categories');
    }

    /**
     * Test: Admin can create product with valid data
     */
    public function test_admin_can_create_product_with_valid_data(): void
    {
        $data = [
            'name' => 'Kursi Jati Minimalis',
            'sku' => 'KJM-001',
            'category_id' => $this->category->id,
            'description' => 'Kursi berkualitas tinggi dari kayu jati',
            'base_price' => 500000,
            'estimated_production_days' => 7,
            'dimensions' => '50x50x90 cm',
            'wood_type' => 'Jati Grade A',
            'finishing_type' => 'Melamine Natural',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $data);

        $this->assertDatabaseHas('products', [
            'name' => 'Kursi Jati Minimalis',
            'sku' => 'KJM-001',
        ]);

        $response->assertRedirect(route('admin.products.index'));
    }

    /**
     * Test: Admin can create product with image
     */
    public function test_admin_can_create_product_with_image(): void
    {
        $file = UploadedFile::fake()->image('product.jpg', 600, 400);

        $data = [
            'name' => 'Meja Makan Kayu',
            'sku' => 'MMK-001',
            'category_id' => $this->category->id,
            'base_price' => 1500000,
            'estimated_production_days' => 14,
            'images' => [$file],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $data);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('products/*');
    }

    /**
     * Test: Product name is required
     */
    public function test_product_name_is_required(): void
    {
        $data = [
            'sku' => 'TEST-001',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Product SKU is required
     */
    public function test_product_sku_is_required(): void
    {
        $data = [
            'name' => 'Test Product',
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $data);

        $response->assertSessionHasErrors('sku');
    }

    /**
     * Test: Product SKU must be unique
     */
    public function test_product_sku_must_be_unique(): void
    {
        $existing = Product::factory()->create(['sku' => 'UNIQUE-001']);

        $data = [
            'name' => 'Another Product',
            'sku' => 'UNIQUE-001',
            'category_id' => $this->category->id,
            'estimated_production_days' => 7,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $data);

        $response->assertSessionHasErrors('sku');
    }

    /**
     * Test: Admin can view product details
     */
    public function test_admin_can_view_product_details(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.show', $product));

        $response->assertStatus(200)
            ->assertViewIs('admin.products.show')
            ->assertViewHas('product', $product);
    }

    /**
     * Test: Admin can view edit form
     */
    public function test_admin_can_view_edit_form(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product));

        $response->assertStatus(200)
            ->assertViewIs('admin.products.edit')
            ->assertViewHas('product', $product);
    }

    /**
     * Test: Admin can update product
     */
    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product Name',
            'sku' => $product->sku,
            'category_id' => $this->category->id,
            'base_price' => 750000,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), $data);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'base_price' => 750000,
        ]);

        $response->assertRedirect();
    }

    /**
     * Test: Admin can delete product
     */
    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $response->assertRedirect();
    }

    /**
     * Test: Admin can toggle product active status
     */
    public function test_admin_can_toggle_product_active_status(): void
    {
        $product = Product::factory()->active()->create();
        
        $this->assertTrue($product->is_active);

        $data = [
            'name' => $product->name,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), $data);

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    /**
     * Test: Customer cannot access product management
     */
    public function test_customer_cannot_access_product_management(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)
            ->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    /**
     * Test: Guest cannot access product management
     */
    public function test_guest_cannot_access_product_management(): void
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('login'));
    }
}
