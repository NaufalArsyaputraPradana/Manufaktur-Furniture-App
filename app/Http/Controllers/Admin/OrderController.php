<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderShippingRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $orders = $this->orderService->getOrdersWithFilters(
            status: $request->get('status'),
            search: $request->get('search'),
            perPage: 15
        );

        $orderStatuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'orderStatuses'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'base_price', 'sku')
            ->orderBy('name')
            ->get();
        $customers = User::with('role:id,name')
            ->whereHas('role', fn($q) => $q->where('name', 'customer'))
            ->orderBy('name')
            ->select('id', 'name', 'email', 'phone')
            ->get();

        return view('admin.orders.create', compact('products', 'customers'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Prepare items with file uploads
            $items = [];
            $uploadedFiles = [];
            foreach ($validated['products'] as $index => $item) {
                $items[$index] = [
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                    'is_custom' => filter_var($item['is_custom'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'custom_specifications' => $item['customizations'] ?? null,
                ];

                // Collect file uploads
                if ($request->hasFile("products.{$index}.customizations.design_image")) {
                    $uploadedFiles[$index] = $request->file("products.{$index}.customizations.design_image");
                }
            }

            // Create order through service
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $validated['user_id'],
                'order_date' => $validated['order_date'],
                'expected_completion_date' => $validated['estimated_delivery_date'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'customer_notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Add order details with file uploads via service
            $this->orderService->addOrderDetails($order, $items, !empty($uploadedFiles) ? $uploadedFiles : null);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! #' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);
        
        $order->load(['user', 'orderDetails.product', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $this->authorize('update', $order);
        
        $customers = User::with('role:id,name')
            ->whereHas('role', fn($q) => $q->where('name', 'customer'))
            ->orderBy('name')
            ->select('id', 'name', 'email', 'phone')
            ->get();

        return view('admin.orders.edit', compact('order', 'customers'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);
        
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

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('updateStatus', $order);
        
        $validated = $request->validated();
        $this->orderService->updateOrderStatus($order, $validated['status'], $validated['notes'] ?? null);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status diubah.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);
        
        if (in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED])) {
            return back()->with('error', 'Gagal membatalkan. Status pesanan sudah Selesai atau Dibatalkan.');
        }

        $order->status = OrderStatus::CANCELLED;

        if ($request->filled('reason')) {
            $order->admin_notes .= "\n[Cancelled] Reason: " . $request->reason;
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan dibatalkan.');
    }

    public function updateShipping(UpdateOrderShippingRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('updateShipping', $order);
        
        $this->orderService->updateShipping($order, $request->validated());

        return redirect()->route('admin.orders.show', $order)->with('success', 'Data pengiriman diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);
        
        if (!in_array($order->status, [OrderStatus::PENDING, OrderStatus::CANCELLED], true)) {
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