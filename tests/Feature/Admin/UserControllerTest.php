<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Role $adminRole;
    protected Role $customerRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles with display_name first
        $this->adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Admin']
        );
        $this->customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['display_name' => 'Customer']
        );

        // Create users with existing roles
        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $this->customer = User::factory()->create(['role_id' => $this->customerRole->id]);
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    public function test_admin_can_create_user_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'phone' => '081234567890',
                'role_id' => $this->customerRole->id,
                'address' => 'Test Address',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'existing@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role_id' => $this->customerRole->id,
                'is_active' => 1,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Different123!',
                'role_id' => $this->customerRole->id,
                'is_active' => 1,
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_admin_can_view_user_details(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'phone' => '085234567890',
                'role_id' => $this->customerRole->id,
                'address' => 'Updated Address',
                'is_active' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_change_user_role(): void
    {
        $user = User::factory()->customer()->create();
        $this->assertNotEquals($this->adminRole->id, $user->role_id);

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $this->adminRole->id,
                'is_active' => 1,
            ]);

        $user->refresh();
        $this->assertEquals($this->adminRole->id, $user->role_id);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $user = User::factory()->active()->create(['role_id' => $this->customerRole->id]);
        $this->assertTrue($user->is_active);

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'is_active' => false, // Explicitly pass false, not 0
            ]);

        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin));

        // Either redirect or forbidden is acceptable
        $this->assertTrue(
            in_array($response->status(), [403, 302]),
            "Expected status 403 or 302, got {$response->status()}"
        );
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_customer_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.users.index'));

        // Routes may redirect or forbid - either is acceptable
        $this->assertTrue(
            in_array($response->status(), [403, 302]),
            "Expected status 403 or 302, got {$response->status()}"
        );
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }
}
