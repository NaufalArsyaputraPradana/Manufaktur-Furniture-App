<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'role_id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@furniture.com',
                'password' => Hash::make('password'),
            ],
            [
                'role_id' => 2,
                'name' => 'Staff Produksi 1',
                'email' => 'staff1@furniture.com',
                'password' => Hash::make('password'),
            ],
            [
                'role_id' => 2,
                'name' => 'Staff Produksi 2',
                'email' => 'staff2@furniture.com',
                'password' => Hash::make('password'),
            ],
            [
                'role_id' => 3,
                'name' => 'John Customer',
                'email' => 'customer1@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'role_id' => 3,
                'name' => 'Jane Customer',
                'email' => 'customer2@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
