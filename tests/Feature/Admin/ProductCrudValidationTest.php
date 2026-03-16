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

class ProductCrudValidationTest extends TestCase
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
    public function product_create_validation_works()
    {
        echo "\n\n🧪 TEST: Product Create Validation\n";
        echo "=====================================\n";

        // Test 1: Create product tanpa data (harus gagal)
        echo "\n1️⃣ Test validation errors (no data)...\n";
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), []);

        $response->assertSessionHasErrors(['code', 'name', 'category_id', 'base_price']);
        echo "   ✅ PASS: Required fields validation works\n";

        // Test 2: Create product dengan data lengkap (harus berhasil)
        echo "\n2️⃣ Test create product with valid data...\n";
        $productData = [
            'code' => 'TEST-001',
            'name' => 'Test Product',
            'category_id' => $this->category->id,
            'description' => 'Test description',
            'base_price' => 1000000,
            'dimensions' => '100x50x75',
            'material_type' => 'Kayu Jati',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData);

        if ($response->status() === 302) {
            echo "   ✅ PASS: Product created, redirected to: " . $response->headers->get('Location') . "\n";
        } else {
            echo "   ❌ FAIL: Expected redirect, got status " . $response->status() . "\n";
        }

        $this->assertDatabaseHas('products', [
            'code' => 'TEST-001',
            'name' => 'Test Product',
            'slug' => 'test-product',
        ]);
        echo "   ✅ PASS: Product exists in database\n";

        // Test 3: Duplicate code (harus gagal)
        echo "\n3️⃣ Test duplicate code validation...\n";
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData);

        $response->assertSessionHasErrors('code');
        echo "   ✅ PASS: Duplicate code prevented\n";

        // Test 4: Invalid category_id (harus gagal)
        echo "\n4️⃣ Test invalid category validation...\n";
        $invalidData = $productData;
        $invalidData['code'] = 'TEST-002';
        $invalidData['category_id'] = 999999;

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $invalidData);

        $response->assertSessionHasErrors('category_id');
        echo "   ✅ PASS: Invalid category_id prevented\n";

        // Test 5: Negative price (harus gagal)
        echo "\n5️⃣ Test negative price validation...\n";
        $negativePrice = $productData;
        $negativePrice['code'] = 'TEST-003';
        $negativePrice['base_price'] = -1000;

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $negativePrice);

        $response->assertSessionHasErrors('base_price');
        echo "   ✅ PASS: Negative price prevented\n";

        echo "\n✅ ALL PRODUCT CREATE VALIDATIONS PASSED!\n\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function product_update_validation_works()
    {
        echo "\n\n🧪 TEST: Product Update Validation\n";
        echo "=====================================\n";

        // Create product untuk di-update
        $product = Product::create([
            'code' => 'UPD-001',
            'name' => 'Original Name',
            'slug' => 'original-name',
            'category_id' => $this->category->id,
            'base_price' => 1000000,
        ]);

        echo "\n1️⃣ Test update product...\n";
        $updateData = [
            'code' => 'UPD-001',
            'name' => 'Updated Name',
            'category_id' => $this->category->id,
            'base_price' => 1500000,
            'description' => 'Updated description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), $updateData);

        $response->assertRedirect();
        echo "   ✅ PASS: Update redirected successfully\n";

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'base_price' => 1500000,
        ]);
        echo "   ✅ PASS: Product updated in database\n";

        echo "\n✅ PRODUCT UPDATE VALIDATION PASSED!\n\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function product_delete_works()
    {
        echo "\n\n🧪 TEST: Product Delete\n";
        echo "==========================\n";

        $product = Product::create([
            'code' => 'DEL-001',
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'category_id' => $this->category->id,
            'base_price' => 1000000,
        ]);

        echo "\n1️⃣ Test delete product...\n";
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        echo "   ✅ PASS: Delete redirected successfully\n";

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        echo "   ✅ PASS: Product removed from database\n";

        echo "\n✅ PRODUCT DELETE PASSED!\n\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function product_with_image_upload_works()
    {
        echo "\n\n🧪 TEST: Product Image Upload\n";
        echo "================================\n";

        $image = UploadedFile::fake()->image('product.jpg', 800, 600);

        $productData = [
            'code' => 'IMG-001',
            'name' => 'Product with Image',
            'category_id' => $this->category->id,
            'base_price' => 2000000,
            'description' => 'Product with uploaded image',
            'image' => $image,
            'is_active' => true,
        ];

        echo "\n1️⃣ Test upload image...\n";
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect();
        echo "   ✅ PASS: Product with image created\n";

        $product = Product::where('code', 'IMG-001')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image);
        echo "   ✅ PASS: Image path saved: {$product->image}\n";

        echo "\n✅ IMAGE UPLOAD PASSED!\n\n";

        $this->assertTrue(true);
    }
}
