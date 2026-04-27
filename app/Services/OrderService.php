<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionProcess;
use Illuminate\Support\Collection;

/**
 * Service untuk menangani logika bisnis Order
 * 
 * Refactoring dari controller untuk meningkatkan code reusability dan maintainability
 */
class OrderService
{
    /**
     * Membuat order baru dengan order details
     */
    public function createOrder(array $data): Order
    {
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $data['user_id'] ?? auth()->id(),
            'status' => $data['status'] ?? 'pending',
            'shipping_address' => $data['shipping_address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'customer_notes' => $data['customer_notes'] ?? null,
            'order_date' => now(),
            'subtotal' => 0,
            'total' => 0,
        ]);

        if (isset($data['items'])) {
            $this->addOrderDetails($order, $data['items']);
        }

        return $order->fresh();
    }

    /**
     * Menambahkan detail items ke order dengan file upload support
     */
    public function addOrderDetails(Order $order, array $items, ?array $uploadedFiles = null): void
    {
        $subtotal = 0;

        foreach ($items as $index => $item) {
            $customSpecs = $item['custom_specifications'] ?? null;
            
            // Handle file upload untuk custom design
            if ($uploadedFiles && isset($uploadedFiles[$index])) {
                $file = $uploadedFiles[$index];
                $path = $file->store('custom_designs', 'public');
                if (!is_array($customSpecs)) {
                    $customSpecs = [];
                }
                $customSpecs['design_image'] = $path;
            }

            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'is_custom' => $item['is_custom'] ?? false,
                'custom_specifications' => $customSpecs,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);

            $subtotal += $orderDetail->subtotal;
        }

        // Update order totals
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal, // Ini bisa disesuaikan dengan pajak, diskon, dll
        ]);
    }

    /**
     * Memperbarui status order dengan notes
     */
    public function updateOrderStatus(Order $order, OrderStatus|string $status, ?string $notes = null): void
    {
        // Convert string to Enum if necessary
        if (is_string($status)) {
            $status = OrderStatus::tryFrom($status) ?? $status;
        }

        $order->status = $status;

        if (!empty($notes)) {
            $ts = now()->format('d/m/Y H:i');
            $statusLabel = is_string($status) ? $status : $status->label();
            $order->admin_notes = ($order->admin_notes ?? '') . "\n[{$ts} - {$statusLabel}] {$notes}";
        }

        if ($status === OrderStatus::COMPLETED) {
            $order->actual_completion_date = now()->toDateString();
        }

        $order->save();
    }

    /**
     * Membuat production processes dari order details
     */
    public function createProductionProcesses(Order $order): void
    {
        foreach ($order->orderDetails as $detail) {
            ProductionProcess::create([
                'production_code' => ProductionProcess::generateProductionCode(),
                'order_id' => $order->id,
                'order_detail_id' => $detail->id,
                'status' => 'pending',
                'progress_percentage' => 0,
            ]);
        }
    }

    /**
     * Memperbarui informasi pengiriman order
     */
    public function updateShipping(Order $order, array $data): void
    {
        if (array_key_exists('shipping_status', $data)) {
            $order->shipping_status = $data['shipping_status'] ?: null;
            if ($order->shipping_status?->value === 'shipped' && !$order->shipped_at) {
                $order->shipped_at = now();
            }
            if ($order->shipping_status?->value === 'delivered' && !$order->delivered_at) {
                $order->delivered_at = now();
            }
        }

        if (array_key_exists('courier', $data)) {
            $c = $data['courier'] !== null ? trim((string) $data['courier']) : '';
            $order->courier = $c !== '' ? $c : null;
        }

        if (array_key_exists('tracking_number', $data)) {
            $t = $data['tracking_number'] !== null ? trim((string) $data['tracking_number']) : '';
            $order->tracking_number = $t !== '' ? $t : null;
        }

        $order->save();
    }

    /**
     * Menghitung remaining payment amount untuk order
     */
    public function getRemainingPayableAmount(Order $order): float
    {
        return $order->remainingPayableAmount();
    }

    /**
     * Get orders dengan eager loading dan filtering
     * Optimized dengan selective column loading untuk reduce memory footprint
     */
    public function getOrdersWithFilters(
        ?string $status = null,
        ?int $userId = null,
        ?string $search = null,
        int $perPage = 15
    ) {
        $query = Order::query()
            ->with([
                'user:id,name,email,phone',
                'orderDetails:id,order_id,product_id,product_name,quantity,unit_price,subtotal,is_custom',
                'orderDetails.product:id,name,sku,images',
                'payment:id,order_id,payment_status,amount_paid,amount,transaction_id',
            ])
            ->latest('order_date');

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where('order_number', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Mendapatkan order summary untuk dashboard
     */
    public function getOrderSummary(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::PENDING)->count(),
            'in_production' => Order::where('status', OrderStatus::IN_PRODUCTION)->count(),
            'completed_orders' => Order::where('status', OrderStatus::COMPLETED)->count(),
            'total_revenue' => Order::where('status', OrderStatus::COMPLETED)->sum('total'),
        ];
    }

    /**
     * Get recently completed orders
     */
    public function getRecentlyCompletedOrders(int $limit = 5): Collection
    {
        return Order::where('status', 'completed')
            ->latest('completed_at')
            ->limit($limit)
            ->with(['user', 'orderDetails.product'])
            ->get();
    }
}
