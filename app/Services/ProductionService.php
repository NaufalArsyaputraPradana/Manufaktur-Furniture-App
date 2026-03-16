<?php


namespace App\Services;

use App\Models\Order;
use App\Models\ProductionLog;
use App\Models\ProductionProcess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk handle production workflow.
 * Mengelola alur tahapan: cutting → assembly → sanding → finishing → quality_control → completed
 */
class ProductionService
{
    /**
     * Memulai proses produksi untuk sebuah order.
     *
     * @throws \Exception
     */
    public function startProduction(Order $order, ?int $assignedTo = null): ProductionProcess
    {
        if ($order->status !== 'confirmed') {
            throw new \Exception(
                "Order harus dalam status 'confirmed' untuk memulai produksi. Status saat ini: {$order->status}"
            );
        }

        return DB::transaction(function () use ($order, $assignedTo) {
            $order->update(['status' => 'in_production']);

            $process = ProductionProcess::create([
                'production_code'     => ProductionProcess::generateProductionCode(),
                'order_id'            => $order->id,
                'order_detail_id'     => null,
                'status'              => 'cutting',
                'stage'               => 'cutting',
                'progress_percentage' => ProductionProcess::STAGES['cutting'],
                'assigned_to'         => $assignedTo,
                'started_at'          => now(),
            ]);

            ProductionLog::create([
                'production_process_id' => $process->id,
                'user_id'               => $assignedTo ?? Auth::id(),
                'stage'                 => 'cutting',
                'action'                => 'started',
                'progress_percentage'   => $process->progress_percentage,
                'notes'                 => 'Produksi dimulai — tahap pemotongan material.',
            ]);

            Log::info('Production started', [
                'order'           => $order->order_number,
                'production_code' => $process->production_code,
            ]);

            return $process->fresh(['order', 'assignedUser', 'logs']);
        });
    }

    /**
     * Memajukan tahapan produksi ke tahap berikutnya.
     *
     * @throws \Exception
     */
    public function advanceStage(ProductionProcess $process, array $data = []): ProductionProcess
    {
        $currentStage = $process->status;
        $nextStage    = $process->getNextStage();

        if (! $nextStage) {
            throw new \Exception('Production sudah selesai atau tidak dapat dilanjutkan.');
        }

        return DB::transaction(function () use ($process, $currentStage, $nextStage, $data) {
            $newProgress = ProductionProcess::STAGES[$nextStage] ?? $process->progress_percentage;

            $process->update([
                'status'              => $nextStage,
                'stage'               => $nextStage,
                'progress_percentage' => $newProgress,
            ]);

            ProductionLog::create([
                'production_process_id' => $process->id,
                'user_id'               => Auth::id(),
                'stage'                 => $nextStage,
                'action'                => $nextStage === 'completed' ? 'completed' : 'in_progress',
                'progress_percentage'   => $newProgress,
                'notes'                 => $data['notes'] ?? null,
            ]);

            if ($nextStage === 'completed') {
                $this->finalizeProduction($process);
            }

            Log::info('Production stage advanced', [
                'production' => $process->production_code,
                'from'       => $currentStage,
                'to'         => $nextStage,
            ]);

            return $process->fresh(['order', 'logs']);
        });
    }

    /**
     * Menyelesaikan proses produksi dan memeriksa apakah seluruh order selesai.
     */
    protected function finalizeProduction(ProductionProcess $process): void
    {
        $process->update([
            'status'              => 'completed',
            'progress_percentage' => 100,
            'completed_at'        => now(),
        ]);

        $order       = $process->order;
        $stillActive = $order->productionProcesses()
            ->where('status', '!=', 'completed')
            ->exists();

        if (! $stillActive) {
            $order->update([
                'status'                 => 'completed',
                'actual_completion_date' => now()->toDateString(),
            ]);

            Log::info('Order marked as completed', ['order' => $order->order_number]);
        }
    }

    /**
     * Mendapatkan statistik produksi.
     */
    public function getStatistics(array $filters = []): array
    {
        $query = ProductionProcess::query();

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $total     = $query->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $pending   = (clone $query)->where('status', 'pending')->count();

        $avgHours = ProductionProcess::whereNotNull('completed_at')
            ->whereNotNull('started_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total'                => $total,
            'completed'            => $completed,
            'in_progress'          => max(0, $total - $completed - $pending),
            'pending'              => $pending,
            'completion_rate'      => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'avg_completion_hours' => round($avgHours ?? 0, 2),
        ];
    }
}
