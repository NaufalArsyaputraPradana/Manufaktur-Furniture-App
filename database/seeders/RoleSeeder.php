<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
            ],
            [
                'name' => 'production_staff',
                'display_name' => 'Staff Produksi',
                'description' => 'Can manage production processes',
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Can place orders and view order status',
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrInsert(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}
