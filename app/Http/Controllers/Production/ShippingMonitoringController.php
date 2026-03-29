<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\StoreOrderShippingLogRequest;
use App\Models\Order;
use App\Models\OrderShippingLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShippingMonitoringController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->whereIn('status', ['confirmed', 'in_production', 'completed'])
            ->with([
                'user:id,name,email',
                'orderDetails:id,order_id,product_name,quantity',
            ])
            ->withCount('shippingLogs')
            ->with(['shippingLogs' => function ($q) {
                $q->latest()->limit(1)->with('recordedBy:id,name');
            }])
            ->orderByDesc('id')
            ->get();

        return view('production.shipping.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        if (! in_array($order->status, ['confirmed', 'in_production', 'completed'], true)) {
            abort(404);
        }

        $order->load([
            'user:id,name,email,phone',
            'orderDetails.product:id,name,sku',
            'payment:id,order_id,payment_status',
        ]);

        $logs = OrderShippingLog::query()
            ->where('order_id', $order->id)
            ->with('recordedBy:id,name')
            ->orderByDesc('created_at')
            ->get();

        $stageLabels = OrderShippingLog::stageLabels();
        $stageIcons = OrderShippingLog::stageIcons();

        return view('production.shipping.show', compact('order', 'logs', 'stageLabels', 'stageIcons'));
    }

    public function storeLog(StoreOrderShippingLogRequest $request, Order $order): RedirectResponse
    {
        if (! in_array($order->status, ['confirmed', 'in_production', 'completed'], true)) {
            abort(404);
        }

        $data = $request->validated();
        $path = null;

        if ($request->hasFile('documentation')) {
            $path = $request->file('documentation')->store('shipping-docs', 'public');
        }

        DB::transaction(function () use ($order, $data, $path, $request) {
            OrderShippingLog::create([
                'order_id' => $order->id,
                'stage' => $data['stage'],
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
                'documentation' => $path,
                'courier_note' => $data['courier_note'] ?? null,
                'tracking_note' => $data['tracking_note'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);

            $o = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->syncOrderShippingState($o, $data['stage']);

            if (! empty($data['courier_note'])) {
                $o->courier = $data['courier_note'];
            }
            if (! empty($data['tracking_note'])) {
                $o->tracking_number = $data['tracking_note'];
            }
            if ($o->isDirty(['courier', 'tracking_number'])) {
                $o->save();
            }
        });

        return redirect()
            ->route('production.shipping.show', $order)
            ->with('success', 'Catatan pengiriman berhasil ditambahkan.');
    }

    public function updateCourier(Request $request, Order $order): RedirectResponse
    {
        if (! in_array($order->status, ['confirmed', 'in_production', 'completed'], true)) {
            abort(404);
        }

        $validated = $request->validate([
            'courier' => 'nullable|string|max:120',
            'tracking_number' => 'nullable|string|max:120',
        ]);

        $order->fill([
            'courier' => $validated['courier'] ?? $order->courier,
            'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
        ]);
        $order->save();

        return redirect()
            ->route('production.shipping.show', $order)
            ->with('success', 'Data kurir & resi diperbarui.');
    }

    private function syncOrderShippingState(Order $order, string $stage): void
    {
        $updates = [];

        switch ($stage) {
            case OrderShippingLog::STAGE_LOADING:
                if ($order->shipping_status !== 'delivered') {
                    $updates['shipping_status'] = 'processing';
                }
                break;
            case OrderShippingLog::STAGE_HANDOVER:
            case OrderShippingLog::STAGE_IN_TRANSIT:
            case OrderShippingLog::STAGE_OUT_FOR_DELIVERY:
                $updates['shipping_status'] = 'shipped';
                if (! $order->shipped_at) {
                    $updates['shipped_at'] = now();
                }
                break;
            case OrderShippingLog::STAGE_DELIVERED:
                $updates['shipping_status'] = 'delivered';
                $updates['delivered_at'] = now();
                if (! $order->shipped_at) {
                    $updates['shipped_at'] = $order->shipped_at ?? now();
                }
                break;
            case OrderShippingLog::STAGE_ISSUE:
                break;
        }

        if ($updates !== []) {
            $order->fill($updates);
            $order->save();
        }
    }
}
