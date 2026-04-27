<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionTodo;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class ProductionTodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get production staff
        $productionStaffRole = Role::where('name', 'production_staff')->first();
        $productionStaffs = User::where('role_id', $productionStaffRole->id)->get();
        if ($productionStaffs->isEmpty()) {
            $productionStaffs = User::factory()->count(3)->create(['role_id' => $productionStaffRole->id]);
        }

        // ============================================
        // PENDING TODOS - Belum Dikerjakan
        // ============================================
        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Persiapan material untuk order 1',
            'description' => 'Siapkan semua material kayu dan hardware yang dibutuhkan untuk produksi item order pertama',
            'deadline' => Carbon::now()->addDays(2),
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Persiapan alat cutting untuk minggu depan',
            'description' => 'Cek dan kalibrasi semua alat cutting di workshop',
            'deadline' => Carbon::now()->addDays(5),
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // ============================================
        // IN PROGRESS TODOS - Sedang Dikerjakan
        // ============================================
        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Proses cutting kayu untuk order 2',
            'description' => 'Mulai proses cutting dan shaping kayu sesuai design dan dimensi yang telah disepakati',
            'deadline' => Carbon::now()->addDays(1),
            'status' => 'in_progress',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Sanding dan finishing order 3',
            'description' => 'Lakukan sanding halus dan aplikasi finishing untuk hasil akhir yang sempurna',
            'deadline' => Carbon::now(),
            'status' => 'in_progress',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // ============================================
        // COMPLETED TODOS - Sudah Selesai
        // ============================================
        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Quality control order 1',
            'description' => 'Lakukan pengecekan kualitas menyeluruh terhadap hasil produksi order pertama',
            'deadline' => Carbon::now()->subDays(2),
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(4),
        ]);

        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Pengemasan order 2',
            'description' => 'Kemasan produk dengan aman dan berikan label sesuai dengan spesifikasi pengiriman',
            'deadline' => Carbon::now()->subDays(1),
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        ProductionTodo::create([
            'user_id' => $productionStaffs->random()->id,
            'title' => 'Assembly furniture order 3',
            'description' => 'Perakitan komponen-komponen furniture sesuai dengan manual assembly yang sudah disiapkan',
            'deadline' => Carbon::now()->subDays(3),
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(5),
        ]);
    }
}