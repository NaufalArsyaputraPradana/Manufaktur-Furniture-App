<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer']);
        
        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
        
        $this->assertTrue(true, "✅ PASS: Admin can view users index");
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $customerRole = Role::where('name', 'customer')->first();
        
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'address' => 'Test Address',
            'role_id' => $customerRole->id,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'name' => 'New User',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Admin can create new user");
    }

    /** @test */
    public function admin_cannot_create_user_with_duplicate_email()
    {
        $customerRole = Role::where('name', 'customer')->first();
        
        $userData = [
            'name' => 'Duplicate User',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $customerRole->id,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertSessionHasErrors('email');
        $this->assertTrue(true, "✅ PASS: Validation prevents duplicate email");
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create(['role_id' => $this->admin->role_id]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role_id' => $user->role_id,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
        
        $this->assertTrue(true, "✅ PASS: Admin can update user");
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create(['role_id' => $this->admin->role_id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Admin can delete user");
    }

    /** @test */
    public function admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
        
        $this->assertTrue(true, "✅ PASS: Security prevents self-deletion");
    }

    /** @test */
    public function guest_cannot_access_user_management()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
        
        $this->assertTrue(true, "✅ PASS: Auth middleware works");
    }
}
