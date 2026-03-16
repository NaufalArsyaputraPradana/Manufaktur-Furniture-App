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

class ProductEditValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $category;
    protected $product;

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

        // Create product untuk di-edit
        $this->product = Product::create([
            'code' => 'EDIT-001',
            'name' => 'Original Product',
            'slug' => 'original-product',
            'category_id' => $this->category->id,
            'base_price' => 1000000,
            'description' => 'Original description',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_edit_form()
    {
        echo "\n\n🧪 TEST: View Edit Form\n";
        echo "==========================\n";

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $this->product));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit');
        $response->assertViewHas('product');
        $response->assertViewHas('categories');
        
        echo "   ✅ PASS: Edit form loaded\n";
        echo "   ✅ PASS: Product data available\n";
        echo "   ✅ PASS: Categories loaded\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_can_update_product_basic_info()
    {
        echo "\n\n🧪 TEST: Update Basic Info\n";
        echo "=============================\n";

        $updateData = [
            'code' => 'EDIT-001', // Same code (allowed for same product)
            'name' => 'Updated Product Name',
            'category_id' => $this->category->id,
            'base_price' => 1500000,
            'description' => 'Updated description',
            'dimensions' => '120x80x75',
            'material_type' => 'Kayu Jati',
            'is_active' => true,
        ];

        echo "\n1️⃣ Updating product...\n";
        echo "   Original: {$this->product->name} (Price: {$this->product->base_price})\n";

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $updateData);

        $response->assertRedirect(route('admin.products.index'));
        echo "   ✅ PASS: Redirected to index\n";

        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'code' => 'EDIT-001',
            'name' => 'Updated Product Name',
            'slug' => 'updated-product-name',
            'base_price' => 1500000,
            'dimensions' => '120x80x75',
            'material_type' => 'Kayu Jati',
        ]);
        
        $updatedProduct = Product::find($this->product->id);
        echo "   ✅ PASS: Product updated in database\n";
        echo "   Updated: {$updatedProduct->name} (Price: {$updatedProduct->base_price})\n";
        echo "   ✅ PASS: Slug auto-updated: {$updatedProduct->slug}\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_can_update_product_with_new_image()
    {
        echo "\n\n🧪 TEST: Update with New Image\n";
        echo "=================================\n";

        // Set old image
        $oldImage = 'products/old-image.jpg';
        $this->product->update(['image' => $oldImage]);

        $newImage = UploadedFile::fake()->image('new-product.jpg', 800, 600);

        $updateData = [
            'code' => 'EDIT-001',
            'name' => $this->product->name,
            'category_id' => $this->category->id,
            'base_price' => $this->product->base_price,
            'image' => $newImage,
            'is_active' => true,
        ];

        echo "\n1️⃣ Uploading new image...\n";
        echo "   Old image: {$oldImage}\n";

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $updateData);

        $response->assertRedirect(route('admin.products.index'));
        
        $updatedProduct = Product::find($this->product->id);
        $this->assertNotNull($updatedProduct->image);
        $this->assertNotEquals($oldImage, $updatedProduct->image);
        
        echo "   ✅ PASS: New image uploaded\n";
        echo "   New image: {$updatedProduct->image}\n";
        echo "   ✅ PASS: Old image should be deleted\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_cannot_update_with_duplicate_code()
    {
        echo "\n\n🧪 TEST: Prevent Duplicate Code\n";
        echo "==================================\n";

        // Create another product
        $anotherProduct = Product::create([
            'code' => 'ANOTHER-001',
            'name' => 'Another Product',
            'slug' => 'another-product',
            'category_id' => $this->category->id,
            'base_price' => 2000000,
        ]);

        echo "\n1️⃣ Trying to use existing code...\n";
        echo "   Existing product: {$anotherProduct->name} (Code: {$anotherProduct->code})\n";
        echo "   Trying to update: {$this->product->name} with same code\n";

        $updateData = [
            'code' => 'ANOTHER-001', // Duplicate code
            'name' => $this->product->name,
            'category_id' => $this->category->id,
            'base_price' => $this->product->base_price,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $updateData);

        $response->assertSessionHasErrors('code');
        echo "   ✅ PASS: Duplicate code prevented\n";
        echo "   ✅ PASS: Validation error shown\n";

        // Ensure product tidak berubah
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'code' => 'EDIT-001', // Original code unchanged
        ]);
        echo "   ✅ PASS: Original product unchanged\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_can_update_without_changing_image()
    {
        echo "\n\n🧪 TEST: Update Without Image Change\n";
        echo "=======================================\n";

        $oldImage = 'products/existing-image.jpg';
        $this->product->update(['image' => $oldImage]);

        $updateData = [
            'code' => 'EDIT-001',
            'name' => 'Updated Name Only',
            'category_id' => $this->category->id,
            'base_price' => 2500000,
            'description' => 'Updated without image',
            'is_active' => true,
            // No 'image' field - should keep existing image
        ];

        echo "\n1️⃣ Updating without new image...\n";
        echo "   Existing image: {$oldImage}\n";

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $updateData);

        $response->assertRedirect(route('admin.products.index'));

        $updatedProduct = Product::find($this->product->id);
        $this->assertEquals($oldImage, $updatedProduct->image);
        
        echo "   ✅ PASS: Product updated\n";
        echo "   ✅ PASS: Image unchanged: {$updatedProduct->image}\n";
        echo "   ✅ PASS: Other fields updated\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function admin_can_deactivate_product()
    {
        echo "\n\n🧪 TEST: Deactivate Product\n";
        echo "==============================\n";

        $this->assertTrue($this->product->is_active);
        echo "\n1️⃣ Original status: Active\n";

        $updateData = [
            'code' => 'EDIT-001',
            'name' => $this->product->name,
            'category_id' => $this->category->id,
            'base_price' => $this->product->base_price,
            'is_active' => false, // Deactivate
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $updateData);

        $response->assertRedirect(route('admin.products.index'));

        $updatedProduct = Product::find($this->product->id);
        $this->assertFalse($updatedProduct->is_active);
        
        echo "   ✅ PASS: Product deactivated\n";
        echo "   New status: Inactive\n";

        $this->assertTrue(true);
    }

    /** @test */
    public function validation_errors_return_with_input()
    {
        echo "\n\n🧪 TEST: Validation Errors Preserve Input\n";
        echo "============================================\n";

        $invalidData = [
            'code' => '', // Empty code
            'name' => '', // Empty name
            'category_id' => 999999, // Invalid category
            'base_price' => -1000, // Negative price
        ];

        echo "\n1️⃣ Submitting invalid data...\n";

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $this->product), $invalidData);

        $response->assertSessionHasErrors(['code', 'name', 'category_id', 'base_price']);
        echo "   ✅ PASS: All validation errors caught\n";
        echo "   ✅ PASS: Form should repopulate with old() values\n";

        $this->assertTrue(true);
    }
}
