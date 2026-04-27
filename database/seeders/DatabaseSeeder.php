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
            // ============================================
            // CORE SETUP SEEDERS (Must run first)
            // ============================================
            RoleSeeder::class,                          // Create all roles
            AdminUserSeeder::class,                     // Create admin users
            UserSeeder::class,                          // Create customer users
            CategorySeeder::class,                      // Create product categories
            ProductSeeder::class,                       // Create products
            ProductionStaffSeeder::class,               // Create production staff
            BankAccountSeeder::class,                   // Create bank accounts

            // ============================================
            // TRANSACTION SEEDERS (Orders & related)
            // ============================================
            OrderSeeder::class,                         // Create sample orders with details
            PaymentSeeder::class,                       // Create payment records
            
            // ============================================
            // PRODUCTION SEEDERS (For production testing)
            // ============================================
            ProductionProcessSeeder::class,             // Create production processes & logs
            ProductionScheduleSeeder::class,            // Create production schedules
            ProductionTodoSeeder::class,                // Create production todos
        ]);
    }
}
