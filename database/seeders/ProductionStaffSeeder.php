<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductionProcess;
use App\Models\ProductionLog;

class ProductionStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get production_staff role
        $productionStaffRole = Role::where('name', 'production_staff')->first();
        
        if (!$productionStaffRole) {
            $this->command->warn('Production staff role not found! Please create it first.');
            return;
        }

        // Create production staff users
        $staffUsers = [
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@furniture.com',
                'phone' => '081234567891',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@furniture.com',
                'phone' => '081234567892',
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra.dewi@furniture.com',
                'phone' => '081234567893',
            ],
        ];

        $createdStaff = [];
        
        foreach ($staffUsers as $staffData) {
            $staff = User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'phone' => $staffData['phone'],
                    'password' => bcrypt('password'), // Default password
                    'role_id' => $productionStaffRole->id,
                ]
            );
            $createdStaff[] = $staff;
            $this->command->info("Created/found production staff: {$staff->name}");
        }

        // Get some orders with "processing" status to create production processes
        $processingOrders = Order::where('status', 'processing')->with('orderDetails.product')->take(3)->get();
        
        if ($processingOrders->isEmpty()) {
            $this->command->warn('No processing orders found. Skipping production process creation.');
            return;
        }

        $stages = ['cutting', 'assembly', 'sanding', 'finishing', 'quality_control'];
        $actions = ['started', 'in_progress', 'completed', 'paused'];
        
        foreach ($processingOrders as $order) {
            foreach ($order->orderDetails as $orderDetail) {
                // Create production process for each product in the order
                $assignedStaff = $createdStaff[array_rand($createdStaff)];
                
                // Random status and progress
                $statusOptions = ['pending', 'in_progress', 'completed'];
                $status = $statusOptions[array_rand($statusOptions)];
                $progressPercentage = match($status) {
                    'pending' => 0,
                    'in_progress' => rand(20, 80),
                    'completed' => 100,
                };

                $productionProcess = ProductionProcess::create([
                    'production_code' => 'PROD-' . strtoupper(uniqid()),
                    'order_id' => $order->id,
                    'product_id' => $orderDetail->product_id,
                    'quantity' => $orderDetail->quantity,
                    'status' => $status,
                    'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                    'assigned_to' => $assignedStaff->id,
                    'start_date' => now()->subDays(rand(1, 10)),
                    'estimated_completion_date' => now()->addDays(rand(5, 15)),
                    'progress_percentage' => $progressPercentage,
                    'notes' => 'Proses produksi untuk order ' . $order->order_number,
                ]);

                $this->command->info("Created production process: {$productionProcess->production_code}");

                // Create production logs based on status
                if ($status !== 'pending') {
                    // Initial log - started
                    ProductionLog::create([
                        'production_process_id' => $productionProcess->id,
                        'user_id' => $assignedStaff->id,
                        'stage' => 'cutting',
                        'action' => 'started',
                        'progress_percentage' => 10,
                        'notes' => 'Memulai proses pemotongan bahan baku',
                        'documentation_images' => [],
                        'created_at' => now()->subDays(rand(5, 8)),
                    ]);

                    // Progress logs
                    $numLogs = $status === 'completed' ? rand(4, 6) : rand(2, 4);
                    for ($i = 0; $i < $numLogs; $i++) {
                        $logStage = $stages[array_rand($stages)];
                        $logAction = $actions[array_rand($actions)];
                        $logProgress = min(100, ($i + 1) * (100 / ($numLogs + 1)));

                        ProductionLog::create([
                            'production_process_id' => $productionProcess->id,
                            'user_id' => $assignedStaff->id,
                            'stage' => $logStage,
                            'action' => $logAction,
                            'progress_percentage' => round($logProgress),
                            'notes' => $this->getRandomNote($logStage, $logAction),
                            'documentation_images' => [], // Empty for now, can be populated manually
                            'created_at' => now()->subDays(rand(1, 5))->addHours(rand(0, 23)),
                        ]);
                    }

                    // If completed, add final log
                    if ($status === 'completed') {
                        ProductionLog::create([
                            'production_process_id' => $productionProcess->id,
                            'user_id' => $assignedStaff->id,
                            'stage' => 'quality_control',
                            'action' => 'completed',
                            'progress_percentage' => 100,
                            'notes' => 'Quality control selesai. Produk siap untuk pengiriman.',
                            'documentation_images' => [],
                            'created_at' => now()->subHours(rand(1, 12)),
                        ]);
                    }

                    $this->command->info("Created {$numLogs} production logs for {$productionProcess->production_code}");
                }
            }
        }

        $this->command->info('Production staff seeder completed successfully!');
    }

    /**
     * Get random note based on stage and action
     */
    private function getRandomNote(string $stage, string $action): string
    {
        $notes = [
            'cutting' => [
                'started' => 'Mempersiapkan bahan baku dan alat potong',
                'in_progress' => 'Sedang memotong kayu sesuai ukuran yang dibutuhkan',
                'completed' => 'Pemotongan bahan selesai dengan presisi tinggi',
                'paused' => 'Menunggu bahan tambahan untuk melanjutkan pemotongan',
            ],
            'assembly' => [
                'started' => 'Memulai proses perakitan komponen furniture',
                'in_progress' => 'Merakit bagian-bagian utama dengan presisi',
                'completed' => 'Perakitan struktur utama telah selesai',
                'paused' => 'Menunggu pengecekan dimensi sebelum melanjutkan',
            ],
            'sanding' => [
                'started' => 'Memulai proses penghalusan permukaan kayu',
                'in_progress' => 'Menghaluskan permukaan dengan amplas berbagai tingkat kekasaran',
                'completed' => 'Permukaan sudah halus dan siap untuk finishing',
                'paused' => 'Istirahat sejenak, akan dilanjutkan dalam beberapa jam',
            ],
            'finishing' => [
                'started' => 'Mempersiapkan cat dan bahan finishing',
                'in_progress' => 'Menerapkan lapisan finishing sesuai spesifikasi',
                'completed' => 'Proses finishing selesai, menunggu pengeringan sempurna',
                'paused' => 'Menunggu lapisan pertama kering sebelum lapisan kedua',
            ],
            'quality_control' => [
                'started' => 'Memulai inspeksi quality control',
                'in_progress' => 'Memeriksa kualitas, dimensi, dan finishing produk',
                'completed' => 'Produk lolos quality control dan siap kirim',
                'paused' => 'Ditemukan minor issue, sedang diperbaiki',
            ],
        ];

        return $notes[$stage][$action] ?? 'Update proses produksi';
    }
}
