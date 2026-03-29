<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderShippingRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderDetails.product', 'payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $orderStatuses = ['pending', 'confirmed', 'in_production', 'completed', 'cancelled', 'on_hold'];

        return view('admin.orders.index', compact('orders', 'orderStatuses'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->select('id', 'name', 'base_price', 'sku')->orderBy('name')->get();
        $customers = User::whereHas('role', fn($q) => $q->where('name', 'customer'))->orderBy('name')->get();

        return view('admin.orders.create', compact('products', 'customers'));
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($validated['products'] as $item) {
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $subtotal += ($unitPrice * (int) $item['quantity']);
            }

            $order = Order::create([
                'order_number'             => Order::generateOrderNumber(),
                'user_id'                  => $validated['user_id'],
                'order_date'                => $validated['order_date'],
                'expected_completion_date'  => $validated['estimated_delivery_date'] ?? null,
                'shipping_address'          => $validated['shipping_address'],
                'customer_notes'            => $validated['notes'] ?? null,
                'status'                    => 'pending',
                'subtotal'                   => $subtotal,
                'total'                      => $subtotal,
            ]);

            foreach ($validated['products'] as $index => $item) {
                $isCustom = filter_var($item['is_custom'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $customSpecs = $item['customizations'] ?? null;
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $quantity = (int) $item['quantity'];

                if ($isCustom && $request->hasFile("products.{$index}.customizations.design_image")) {
                    $file = $request->file("products.{$index}.customizations.design_image");
                    $path = $file->store('custom_designs', 'public');
                    $customSpecs['design_image'] = $path;
                }

                OrderDetail::create([
                    'order_id'               => $order->id,
                    'product_id'             => $isCustom ? null : ($item['product_id'] ?? null),
                    'product_name'           => $item['product_name'],
                    'quantity'               => $quantity,
                    'unit_price'             => $unitPrice,
                    'subtotal'                => $unitPrice * $quantity,
                    'is_custom'               => $isCustom,
                    'custom_specifications'   => $customSpecs,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! #' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderDetails.product', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = User::whereHas('role', fn($q) => $q->where('name', 'customer'))->orderBy('name')->get();

        return view('admin.orders.edit', compact('order', 'customers'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        $order->update([
            'user_id'                  => $validated['user_id'],
            'order_date'                => $validated['order_date'],
            'expected_completion_date'  => $validated['estimated_delivery_date'] ?? null,
            'shipping_address'          => $validated['shipping_address'],
            'customer_notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Update berhasil.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $validated = $request->validated();

        $order->status = $validated['status'];

        if (!empty($validated['notes'])) {
            $ts = now()->format('d/m/Y H:i');
            $order->admin_notes .= "\n[{$ts} - {$validated['status']}] {$validated['notes']}";
        }

        if ($validated['status'] === 'completed') {
            $order->actual_completion_date = now();
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status diubah.');
    }

    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Gagal membatalkan. Status pesanan sudah Selesai atau Dibatalkan.');
        }

        $order->status = 'cancelled';

        if ($request->filled('reason')) {
            $order->admin_notes .= "\n[Cancelled] Reason: " . $request->reason;
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan dibatalkan.');
    }

    public function updateShipping(UpdateOrderShippingRequest $request, Order $order)
    {
        $v = $request->validated();

        if (array_key_exists('shipping_status', $v)) {
            $order->shipping_status = $v['shipping_status'] ?: null;
            if ($order->shipping_status === 'shipped' && !$order->shipped_at) {
                $order->shipped_at = now();
            }
            if ($order->shipping_status === 'delivered' && !$order->delivered_at) {
                $order->delivered_at = now();
            }
        }

        if (array_key_exists('courier', $v)) {
            $c = $v['courier'] !== null ? trim((string) $v['courier']) : '';
            $order->courier = $c !== '' ? $c : null;
        }

        if (array_key_exists('tracking_number', $v)) {
            $t = $v['tracking_number'] !== null ? trim((string) $v['tracking_number']) : '';
            $order->tracking_number = $t !== '' ? $t : null;
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Data pengiriman diperbarui.');
    }

    public function destroy(Order $order)
    {
        if (!in_array($order->status, ['pending', 'cancelled'], true)) {
            return back()->with('error', 'Hanya pesanan berstatus pending atau cancelled yang dapat dihapus.');
        }

        $st = $order->payment?->payment_status;
        if (in_array($st, [Payment::STATUS_PAID, Payment::STATUS_DP_PAID], true)) {
            return back()->with('error', 'Tidak dapat menghapus pesanan dengan pembayaran dp_paid atau paid.');
        }

        DB::transaction(function () use ($order) {
            $order->productionProcesses()->delete();
            $order->orderDetails()->delete();
            $order->payment()->delete();
            $order->delete();
        });

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan dihapus.');
    }
}