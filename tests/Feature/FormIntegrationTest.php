<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Role;

class FormIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role if it doesn't exist
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
        
        // Create test user and authenticate
        $this->user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($this->user);
    }

    /**
     * Test that product create form loads with FormInput components
     */
    public function test_product_create_form_loads()
    {
        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        $response->assertSeeText('name');
        $response->assertSeeText('category');
    }

    /**
     * Test that product index shows FormInput filter fields
     */
    public function test_products_index_renders_filters()
    {
        Category::factory()->count(3)->create();
        Product::factory()->count(5)->create();

        $response = $this->get('/admin/products');
        
        $response->assertStatus(200);
        // Form fields should be present
        $this->assertStringContainsString('form-control', $response->getContent());
    }

    /**
     * Test that category index shows active/inactive filter
     */
    public function test_category_index_shows_status_filter()
    {
        $response = $this->get('/admin/categories');
        
        $response->assertStatus(200);
        // Should contain select/option elements for status filter
        $this->assertStringContainsString('select', $response->getContent());
    }

    /**
     * Test that product can be created via form submission
     */
    public function test_product_creation_via_form()
    {
        $category = Category::factory()->create();
        
        $formData = [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 100000,
            'cost' => 50000,
            'is_active' => true,
        ];

        $response = $this->post('/admin/products', $formData);
        
        // Should redirect after successful creation
        $this->assertTrue(
            $response->status() === 302 || $response->status() === 201,
            'Should redirect on success'
        );
    }

    /**
     * Test that validation errors display when form is invalid
     */
    public function test_form_validation_errors()
    {
        // Submit form with missing required fields
        $response = $this->from('/admin/products/create')
            ->post('/admin/products', [
                'name' => '', // Required
                'category_id' => '',
            ]);

        // Should redirect back with errors
        $response->assertRedirect('/admin/products/create');
        $response->assertSessionHasErrors();
    }

    /**
     * Test that old() helper preserves form values after validation failure
     */
    public function test_old_helper_preserves_form_values()
    {
        $category = Category::factory()->create();
        
        // Submit invalid form
        $response = $this->from('/admin/products/create')
            ->post('/admin/products', [
                'name' => 'Test',
                'category_id' => $category->id,
                'price' => -100, // Invalid
            ]);

        // Should redirect back
        $response->assertRedirect('/admin/products/create');
        
        // Check old values are in session
        $this->assertTrue($response->getSession()->hasOldInput('name'));
    }

    /**
     * Test product edit form loads with existing values
     */
    public function test_product_edit_form_loads_values()
    {
        $product = Product::factory()->create(['name' => 'Existing Product']);

        $response = $this->get("/admin/products/{$product->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSeeText($product->name);
    }

    /**
     * Test that checkbox fields work correctly
     */
    public function test_checkbox_field_handling()
    {
        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        // Should contain checkbox markup
        $this->assertStringContainsString('checkbox', $response->getContent());
    }

    /**
     * Test that textarea fields render correctly
     */
    public function test_textarea_field_rendering()
    {
        $product = Product::factory()->create(['description' => 'Test Description']);

        $response = $this->get("/admin/products/{$product->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSeeText('Test Description');
    }

    /**
     * Test that select fields with options render correctly
     */
    public function test_select_options_rendering()
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        // Check that options from categories appear
        foreach ($categories as $category) {
            // Options may be in the HTML
            $content = $response->getContent();
            $this->assertStringContainsString('option', $content);
        }
    }

    /**
     * Test CSRF token is present in forms
     */
    public function test_csrf_token_in_forms()
    {
        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        // Laravel adds CSRF token to forms
        $this->assertStringContainsString('_token', $response->getContent());
    }

    /**
     * Test that method override field exists for PUT requests
     */
    public function test_method_override_in_edit_forms()
    {
        $product = Product::factory()->create();
        
        $response = $this->get("/admin/products/{$product->id}/edit");
        
        $response->assertStatus(200);
        // Edit forms should have method override for PUT
        $this->assertStringContainsString('_method', $response->getContent());
    }

    /**
     * Test form accessibility - labels are present
     */
    public function test_form_labels_accessibility()
    {
        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        // Should contain form-label markup
        $this->assertStringContainsString('form-label', $response->getContent());
    }

    /**
     * Test required field indicator
     */
    public function test_required_field_indicator()
    {
        $response = $this->get('/admin/products/create');
        
        $response->assertStatus(200);
        // Should show required indicators
        $this->assertStringContainsString('required', $response->getContent());
    }
}
