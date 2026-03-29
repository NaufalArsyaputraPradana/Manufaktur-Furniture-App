<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductionProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductionProcessController extends Controller
{
    public function ordersIndex(): View
    {
        $orders = Order::whereIn('status', ['confirmed', 'in_production'])
            ->with([
                'user:id,name,email',
                'orderDetails:id,order_id,product_name,quantity',
                'productionProcesses:id,order_id,status,progress_percentage,stage',
            ])
            ->select('id', 'order_number', 'user_id', 'status', 'total', 'created_at')
            ->orderByDesc('id')
            ->get();

        return view('production.orders.index', compact('orders'));
    }

    public function createStages(Order $order): RedirectResponse
    {
        return redirect()
            ->route('production.processes.create', ['order_id' => $order->id])
            ->with('info', 'Silakan tambahkan tahapan produksi untuk order ini.');
    }

    public function index(Order $order): View
    {
        $order->load(['user:id,name,email', 'payment:id,order_id,payment_status', 'orderDetails:id,order_id,product_name,quantity']);

        $processes = ProductionProcess::where('order_id', $order->id)
            ->with(['assignedTo:id,name', 'logs:id,production_process_id,status,created_at,created_by'])
            ->orderBy('created_at')
            ->get();

        return view('production.process.index', compact('order', 'processes'));
    }

    public function create(Request $request): View
    {
        $order = null;

        if ($request->filled('order_id')) {
            $order = Order::with(['user:id,name', 'orderDetails.product:id,name,sku'])
                ->findOrFail($request->integer('order_id'));
        }

        return view('production.process.create', compact('order'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'stage' => 'required|string|in:cutting,assembly,sanding,finishing,quality_control',
            'status' => 'required|string|in:pending,in_progress,completed',
            'documentation' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $process = ProductionProcess::create([
            'order_id' => $validated['order_id'],
            'stage' => $validated['stage'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'production_code' => ProductionProcess::generateProductionCode(),
            'progress_percentage' => ProductionProcess::STAGES[$validated['stage']] ?? 0,
            'assigned_to' => null,
        ]);

        if ($request->hasFile('documentation')) {
            $process->update([
                'documentation' => $request->file('documentation')->store('production-docs', 'public'),
            ]);
        }

        return redirect()
            ->route('production.monitoring.index', $process->order_id)
            ->with('success', 'Proses produksi berhasil ditambahkan ke antrean.');
    }

    public function show(ProductionProcess $process): View
    {
        $process->load(['order.user:id,name,email', 'orderDetails.product:id,name,sku', 'assignedTo:id,name', 'logs.user:id,name']);

        return view('production.process.show', compact('process'));
    }

    public function edit(ProductionProcess $process): View
    {
        $process->load(['order.user:id,name', 'orderDetails:id,order_id,product_name']);

        return view('production.process.edit', compact('process'));
    }

    public function update(Request $request, ProductionProcess $process): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => 'required|string|in:cutting,assembly,sanding,finishing,quality_control',
            'status' => 'required|string|in:pending,in_progress,completed,paused,issue',
            'documentation' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'stage' => $validated['stage'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('documentation')) {
            if ($process->documentation) {
                Storage::disk('public')->delete($process->documentation);
            }
            $data['documentation'] = $request->file('documentation')->store('production-docs', 'public');
        }

        $process->update($data);
        $process->updateProgress();

        return redirect()
            ->route('production.processes.show', $process)
            ->with('success', 'Detail proses produksi berhasil diperbarui.');
    }

    public function showOrder(Order $order)
    {
        $order->load(['user:id,name,email', 'orderDetails.product:id,name,sku', 'payment:id,order_id,payment_status', 'productionProcesses:id,order_id,stage,status']);
        return view('production.orders.show', compact('order'));
    }

    public function destroy(ProductionProcess $process): RedirectResponse
    {
        $orderId = $process->order_id;

        if ($process->documentation) {
            Storage::disk('public')->delete($process->documentation);
        }

        $process->delete();

        return redirect()
            ->route('production.monitoring.index', $orderId)
            ->with('success', 'Data proses produksi berhasil dihapus secara permanen.');
    }
}