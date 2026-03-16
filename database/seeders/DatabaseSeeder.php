<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class, // Create admin user after roles
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductionStaffSeeder::class, // Create production staff and sample data
        ]);
    }
}
