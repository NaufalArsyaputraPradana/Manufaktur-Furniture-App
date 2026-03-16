<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create();
        Order::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);
        Order::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'in_production',
        ]);
    }
}
