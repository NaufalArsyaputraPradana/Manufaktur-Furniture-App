<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\ProductionProcess;
use App\Models\ProductionLog;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class ProductionProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get production staff role
        $productionStaffRole = Role::where('name', 'production_staff')->first();
        $productionStaffs = User::where('role_id', $productionStaffRole->id)->get();
        if ($productionStaffs->isEmpty()) {
            $productionStaffs = User::factory()->count(3)->create(['role_id' => $productionStaffRole->id]);
        }

        // Get orders that are in production
        $orders = Order::where('status', 'in_production')->get();

        if ($orders->isEmpty()) {
            return; // No orders in production, skip
        }

        // ============================================
        // Create Production Processes for each order
        // ============================================
        foreach ($orders as $order) {
            // Get order details
            $orderDetails = $order->orderDetails;

            if ($orderDetails->isEmpty()) {
                continue; // No order details, skip
            }

            foreach ($orderDetails as $detail) {
                // ============================================
                // NOT STARTED - Belum Dimulai
                // ============================================
                ProductionProcess::create([
                    'production_code' => 'PROD-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 5, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'order_detail_id' => $detail->id,
                    'assigned_to' => $productionStaffs->random()->id,
                    'stage' => 'cutting',
                    'status' => 'pending',
                    'progress_percentage' => 0,
                    'notes' => 'Menunggu jadwal produksi',
                    'started_at' => null,
                    'completed_at' => null,
                    'created_at' => Carbon::now()->subDays(random_int(1, 5)),
                ]);

                // ============================================
                // IN PROGRESS - Sedang Dikerjakan
                // ============================================
                $inProgressProcess = ProductionProcess::create([
                    'production_code' => 'PROD-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 5, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'order_detail_id' => $detail->id,
                    'assigned_to' => $productionStaffs->random()->id,
                    'stage' => 'assembly',
                    'status' => 'in_progress',
                    'progress_percentage' => random_int(30, 70),
                    'notes' => 'Sedang dalam tahap pengerjaan',
                    'started_at' => Carbon::now()->subDays(random_int(1, 3)),
                    'completed_at' => null,
                    'created_at' => Carbon::now()->subDays(4),
                ]);

                // Add production logs for in progress
                $this->addProductionLogs($inProgressProcess, $productionStaffs->random(), 'in_progress');

                // ============================================
                // COMPLETED - Selesai
                // ============================================
                $completedProcess = ProductionProcess::create([
                    'production_code' => 'PROD-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 5, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'order_detail_id' => $detail->id,
                    'assigned_to' => $productionStaffs->random()->id,
                    'stage' => 'quality_control',
                    'status' => 'completed',
                    'progress_percentage' => 100,
                    'notes' => 'Produksi selesai dan telah dikontrol kualitas',
                    'started_at' => Carbon::now()->subDays(7),
                    'completed_at' => Carbon::now()->subDays(2),
                    'created_at' => Carbon::now()->subDays(8),
                ]);

                // Add production logs for completed
                $this->addProductionLogs($completedProcess, $productionStaffs->random(), 'completed');

                // ============================================
                // PAUSED - Ditunda
                // ============================================
                ProductionProcess::create([
                    'production_code' => 'PROD-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 5, '0', STR_PAD_LEFT),
                    'order_id' => $order->id,
                    'order_detail_id' => $detail->id,
                    'assigned_to' => $productionStaffs->random()->id,
                    'stage' => 'sanding',
                    'status' => 'paused',
                    'progress_percentage' => 50,
                    'notes' => 'Ditunda menunggu material tiba',
                    'started_at' => Carbon::now()->subDays(5),
                    'completed_at' => null,
                    'created_at' => Carbon::now()->subDays(6),
                ]);
            }
        }
    }

    /**
     * Add production logs to process
     *
     * @param ProductionProcess $process
     * @param User $staff
     * @param string $status
     * @return void
     */
    private function addProductionLogs($process, $staff, $status)
    {
        $logs = [];

        if ($status === 'in_progress') {
            $logs = [
                ['action' => 'started', 'stage' => 'assembly', 'progress' => 20, 'notes' => 'Mulai mengerjakan item'],
                ['action' => 'in_progress', 'stage' => 'assembly', 'progress' => 50, 'notes' => '50% selesai'],
                ['action' => 'in_progress', 'stage' => 'assembly', 'progress' => 75, 'notes' => 'Update progress produksi'],
            ];
        } elseif ($status === 'completed') {
            $logs = [
                ['action' => 'started', 'stage' => 'quality_control', 'progress' => 80, 'notes' => 'Mulai quality check'],
                ['action' => 'in_progress', 'stage' => 'quality_control', 'progress' => 90, 'notes' => 'Quality check in progress'],
                ['action' => 'completed', 'stage' => 'quality_control', 'progress' => 100, 'notes' => 'Quality check passed'],
                ['action' => 'completed', 'stage' => 'quality_control', 'progress' => 100, 'notes' => 'Item selesai dan siap dikirim'],
            ];
        }

        foreach ($logs as $index => $logData) {
            ProductionLog::create([
                'production_process_id' => $process->id,
                'user_id' => $staff->id,
                'stage' => $logData['stage'],
                'action' => $logData['action'],
                'progress_percentage' => $logData['progress'],
                'notes' => $logData['notes'],
                'created_at' => $process->started_at?->addHours($index + 1) ?? Carbon::now()->subHours(count($logs) - $index),
            ]);
        }
    }
}
