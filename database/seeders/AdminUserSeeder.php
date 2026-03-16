<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk create default admin user
 * Ini penting untuk initial setup agar ada admin yang bisa login
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin role
        // firstOrFail akan throw exception jika role tidak ditemukan
        // Ini ensure role seeder sudah dijalankan terlebih dahulu
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        // Create admin user
        // updateOrCreate untuk idempotency
        $admin = User::updateOrCreate(
            ['email' => 'admin@udbisa.com'], // Search by email
            [
                'name' => 'Administrator',
                'email' => 'admin@udbisa.com',
                'password' => Hash::make('password123'), // Default password, should be changed after first login
                'phone' => '081234567890',
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(), // Auto verify admin email
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->warn('Email: admin@udbisa.com');
        $this->command->warn('Password: password123');
        $this->command->warn('Please change this password after first login!');
    }
}
