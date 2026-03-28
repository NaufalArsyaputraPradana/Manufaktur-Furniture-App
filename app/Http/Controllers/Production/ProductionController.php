<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductionLog;
use App\Models\ProductionProcess;
use App\Models\ProductionSchedule;
use App\Models\ProductionTodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductionController extends Controller
{
    /**
     * Mendapatkan role user saat ini.
     */
    private function userRole(): string
    {
        $role = Auth::user()?->role;
        return is_string($role) ? $role : ($role?->name ?? '');
    }

    /**
     * Cek apakah user adalah staff produksi.
     */
    private function isProductionStaff(): bool
    {
        return $this->userRole() === 'production_staff';
    }

    /**
     * Menampilkan dashboard produksi.
     */
    public function index(): View
    {
        $userId = Auth::id() ?? 0;

        $baseQuery = ProductionProcess::with([
            'order:id,order_number',
            'orderDetail.product:id,name',
            'assignedTo:id,name',
        ]);

        // Staff produksi hanya melihat tugas sendiri + yang belum ditugaskan
        if ($this->isProductionStaff()) {
            $baseQuery->where(function ($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->orWhereNull('assigned_to');
            });
        }

        // Ambil semua proses sekaligus untuk statistik dan tabel
        $allProcesses = $baseQuery->latest()->get();

        $stats = [
            'pending'     => $allProcesses->where('status', 'pending')->count(),
            'in_progress' => $allProcesses->filter(fn($p) => $p->isInProgress())->count(),
            'completed'   => $allProcesses->where('status', 'completed')->count(),
            'my_tasks'    => $allProcesses->where('assigned_to', $userId)->count(),
        ];

        // Ambil 10 terbaru untuk tabel antrian
        $productions = $allProcesses->take(10);

        // Pesanan terbaru yang siap produksi (confirmed atau in_production)
        $recentOrders = Order::with([
                'user:id,name',
                'orderDetails:id,order_id,product_name,quantity'
            ])
            ->select('id', 'order_number', 'user_id', 'status', 'created_at')
            ->whereIn('status', ['confirmed', 'in_production'])
            ->latest()
            ->take(5)
            ->get();

        // Data untuk jadwal (tampilkan jadwal hari ini dan mendatang)
        $upcomingSchedules = ProductionSchedule::with('user')
            ->where('user_id', $userId)
            ->whereDate('start_datetime', '>=', now()->toDateString())
            ->orderBy('start_datetime')
            ->take(5)
            ->get();

        // Data untuk todo yang belum selesai (pending atau in_progress)
        $pendingTodos = ProductionTodo::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('deadline')
            ->take(5)
            ->get();

        return view('production.production', compact(
            'stats',
            'productions',
            'recentOrders',
            'upcomingSchedules',
            'pendingTodos'
        ));
    }

    /**
     * Memperbarui tahap produksi saat ini.
     */
    public function updateStage(Request $request, ProductionProcess $process): RedirectResponse
    {
        if ($this->isProductionStaff() && $process->assigned_to !== Auth::id()) {
            abort(403, 'Anda tidak memiliki otoritas untuk mengubah proses ini.');
        }

        $validated = $request->validate([
            'stage'         => 'required|in:cutting,assembly,sanding,finishing,quality_control,completed',
            'action'        => 'required|in:started,in_progress,paused,completed,issue',
            'notes'         => 'nullable|string|max:1000',
            'material_used' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $process) {
            $process->update(['status' => $validated['stage']]);

            if ($validated['action'] === 'started' && !$process->started_at) {
                $process->update(['started_at' => now()]);
            }

            if ($validated['stage'] === 'completed') {
                $process->update([
                    'completed_at'        => now(),
                    'progress_percentage' => 100,
                ]);
                $this->checkAndCompleteOrder($process->order_id);
            } else {
                $process->updateProgress();
            }

            ProductionLog::create([
                'production_process_id' => $process->id,
                'user_id'               => Auth::id(),
                'stage'                 => $validated['stage'],
                'action'                => $validated['action'],
                'progress_percentage'   => $process->fresh()->progress_percentage,
                'notes'                 => $validated['notes'] ?? null,
                'material_used'         => $validated['material_used'] ?? null,
            ]);
        });

        return redirect()->route('production.tracking.show', $process)
            ->with('success', 'Tahap produksi berhasil diperbarui.');
    }

    /**
     * Memulai proses produksi secara cepat.
     */
    public function startProduction(ProductionProcess $production): RedirectResponse
    {
        if ($this->isProductionStaff() && $production->assigned_to !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan memulai proses ini.');
        }

        if ($production->started_at) {
            return redirect()->route('production.tracking.show', $production)
                ->with('info', 'Proses ini sudah berjalan.');
        }

        DB::transaction(function () use ($production) {
            $production->update([
                'started_at'          => now(),
                'status'              => 'in_progress',
                'progress_percentage' => $production->progress_percentage ?: 15,
            ]);

            ProductionLog::create([
                'production_process_id' => $production->id,
                'user_id'               => Auth::id(),
                'stage'                 => $production->stage ?? 'cutting',
                'action'                => 'started',
                'progress_percentage'   => $production->progress_percentage,
            ]);
        });

        return redirect()->route('production.tracking.show', $production)
            ->with('success', 'Proses produksi telah resmi dimulai.');
    }

    /**
     * Menyelesaikan seluruh rangkaian produksi untuk item ini.
     */
    public function complete(ProductionProcess $production): RedirectResponse
    {
        if ($this->isProductionStaff() && $production->assigned_to !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan menyelesaikan proses ini.');
        }

        DB::transaction(function () use ($production) {
            $production->update([
                'status'              => 'completed',
                'progress_percentage' => 100,
                'completed_at'        => now(),
            ]);

            ProductionLog::create([
                'production_process_id' => $production->id,
                'user_id'               => Auth::id(),
                'stage'                 => 'completed',
                'action'                => 'completed',
                'progress_percentage'   => 100,
            ]);

            $this->checkAndCompleteOrder($production->order_id);
        });

        return redirect()->route('production.tracking.show', $production)
            ->with('success', 'Proses produksi telah diselesaikan dengan sukses.');
    }

    /**
     * Mencatat pemakaian material tanpa mengubah status produksi.
     */
    public function recordMaterialUsage(Request $request, ProductionProcess $production): RedirectResponse
    {
        if ($this->isProductionStaff() && $production->assigned_to !== Auth::id()) {
            abort(403, 'Otoritas ditolak.');
        }

        $validated = $request->validate([
            'material_used'         => 'required|array',
            'material_used.*.name'  => 'required|string|max:255',
            'material_used.*.qty'   => 'required|numeric|min:0',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        ProductionLog::create([
            'production_process_id' => $production->id,
            'user_id'               => Auth::id(),
            'stage'                 => $production->status,
            'action'                => 'in_progress',
            'progress_percentage'   => $production->progress_percentage,
            'notes'                 => $validated['notes'] ?? 'Pencatatan material rutin.',
            'material_used'         => $validated['material_used'],
        ]);

        return redirect()->route('production.tracking.show', $production)
            ->with('success', 'Data pemakaian material telah tersimpan.');
    }

    /**
     * Memeriksa dan memperbarui status pesanan utama jika semua item selesai.
     */
    private function checkAndCompleteOrder(int $orderId): void
    {
        $order = Order::find($orderId);
        if (!$order) return;

        $isAllFinished = !ProductionProcess::where('order_id', $orderId)
            ->where('status', '!=', 'completed')
            ->exists();

        if ($isAllFinished) {
            $order->update(['status' => 'completed']);
        }
    }
}