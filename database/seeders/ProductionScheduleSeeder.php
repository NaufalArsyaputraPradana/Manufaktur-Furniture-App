<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionSchedule;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class ProductionScheduleSeeder extends Seeder
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

        // Get a staff member for the schedules
        $staffMember = $productionStaffs->first();

        // ============================================
        // SCHEDULE 1 - Cutting Phase
        // ============================================
        ProductionSchedule::create([
            'user_id' => $staffMember->id,
            'title' => 'Pemotongan Material - Order 1',
            'description' => 'Tahap pemotongan material untuk item produksi order pertama',
            'start_datetime' => Carbon::now()->addDays(1)->setHour(8)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(1)->setHour(12)->setMinute(0),
            'location' => 'Workshop Area 1',
        ]);

        // ============================================
        // SCHEDULE 2 - Assembly Phase
        // ============================================
        ProductionSchedule::create([
            'user_id' => $staffMember->id,
            'title' => 'Perakitan Komponen - Order 1',
            'description' => 'Tahap perakitan komponen untuk item produksi order pertama',
            'start_datetime' => Carbon::now()->addDays(2)->setHour(9)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(2)->setHour(17)->setMinute(0),
            'location' => 'Workshop Area 2',
        ]);

        // ============================================
        // SCHEDULE 3 - Finishing Phase
        // ============================================
        ProductionSchedule::create([
            'user_id' => $staffMember->id,
            'title' => 'Finishing dan Polishing - Order 2',
            'description' => 'Tahap finishing dan polishing untuk item produksi order kedua',
            'start_datetime' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(3)->setHour(15)->setMinute(0),
            'location' => 'Finishing Workshop',
        ]);

        // ============================================
        // SCHEDULE 4 - Quality Control
        // ============================================
        ProductionSchedule::create([
            'user_id' => $staffMember->id,
            'title' => 'Quality Control - Order 1',
            'description' => 'Tahap quality control dan inspeksi untuk item produksi order pertama',
            'start_datetime' => Carbon::now()->addDays(4)->setHour(11)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(4)->setHour(13)->setMinute(0),
            'location' => 'Quality Control Lab',
        ]);

        // ============================================
        // SCHEDULE 5 - Packaging
        // ============================================
        ProductionSchedule::create([
            'user_id' => $staffMember->id,
            'title' => 'Pengemasan - Order 3',
            'description' => 'Tahap pengemasan dan labeling untuk item produksi order ketiga',
            'start_datetime' => Carbon::now()->addDays(5)->setHour(14)->setMinute(0),
            'end_datetime' => Carbon::now()->addDays(5)->setHour(16)->setMinute(0),
            'location' => 'Packaging Area',
        ]);
    }
}
