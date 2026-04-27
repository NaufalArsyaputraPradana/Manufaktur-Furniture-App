<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->customer()->create();
        $this->category = Category::factory()->active()->create();
    }

    public function test_admin_can_view_categories_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewHas('parents');
    }

    public function test_admin_can_create_category_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'New Category',
                'description' => 'Test Category Description',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'description' => 'Test Category Description',
        ]);
    }

    public function test_admin_can_create_subcategory(): void
    {
        $parent = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Sub Category',
                'parent_id' => $parent->id,
                'description' => 'Sub category description',
                'is_active' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Sub Category',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_category_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => '',
                'description' => 'Test',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_view_category_details(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.show', $this->category));

        $response->assertStatus(200);
        $response->assertViewHas('category');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $this->category));

        $response->assertStatus(200);
        $response->assertViewHas('category');
    }

    public function test_admin_can_update_category(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $this->category), [
                'name' => 'Updated Category',
                'description' => 'Updated description',
                'is_active' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $this->category->id,
            'name' => 'Updated Category',
        ]);
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $this->assertTrue($this->category->is_active);

        $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $this->category), [
                'name' => $this->category->name,
                'is_active' => 0,
            ]);

        $this->category->refresh();
        $this->assertFalse($this->category->is_active);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $emptyCategory = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $emptyCategory));

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $emptyCategory->id]);
    }

    public function test_customer_cannot_access_category_management(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.categories.index'));

        // Routes may redirect or forbid - either is acceptable
        $this->assertTrue(
            in_array($response->status(), [403, 302]),
            "Expected status 403 or 302, got {$response->status()}"
        );
    }

    public function test_guest_cannot_access_category_management(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_filter_categories_by_status(): void
    {
        Category::factory()->inactive()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['is_active' => '1']));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_admin_can_search_categories(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => $this->category->name]));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }
}
