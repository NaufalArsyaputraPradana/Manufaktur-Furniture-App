<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Order Repository Implementation
 * 
 * Handles all order-related database operations with optimized queries
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all orders with relationships and filters
     */
    public function getAllWithRelations(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['user', 'orderDetails.product', 'payment'])
            ->latest();

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Search by order number, customer name, or email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        if (!empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * Find order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model
            ->with(['user', 'orderDetails.product', 'payment', 'productionProcesses'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Get orders by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model
            ->with(['user', 'orderDetails.product'])
            ->where('status', $status)
            ->latest()
            ->get();
    }

    /**
     * Get customer orders with filters
     */
    public function getCustomerOrders(int $userId, array $filters = []): LengthAwarePaginator
    {
        $filters['user_id'] = $userId;
        return $this->getAllWithRelations($filters);
    }

    /**
     * Get pending orders (pending + confirmed)
     */
    public function getPendingOrders(): Collection
    {
        return $this->model
            ->with(['user', 'orderDetails.product'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->get();
    }

    /**
     * Get completed orders
     */
    public function getCompletedOrders(int $limit = 10): Collection
    {
        return $this->model
            ->with(['user', 'orderDetails.product'])
            ->where('status', 'completed')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get order statistics for dashboard
     */
    public function getOrderStatistics(): array
    {
        $stats = $this->model
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = "in_production" THEN 1 ELSE 0 END) as in_production,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status IN ("confirmed", "in_production") THEN total ELSE 0 END) as pending_revenue
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'confirmed' => $stats->confirmed ?? 0,
            'in_production' => $stats->in_production ?? 0,
            'completed' => $stats->completed ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
            'pending_revenue' => $stats->pending_revenue ?? 0,
        ];
    }

    /**
     * Create order with order details (transactional)
     */
    public function createWithDetails(array $orderData, array $orderDetails): Order
    {
        DB::beginTransaction();

        try {
            // Create order
            $order = $this->create($orderData);

            // Create order details
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }

            DB::commit();

            // Return fresh model with relationships
            return $order->fresh(['user', 'orderDetails.product']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel order with reason
     */
    public function cancelOrder(int $orderId, ?string $reason = null): bool
    {
        $order = $this->findOrFail($orderId);
        
        $updateData = ['status' => 'cancelled'];
        
        if ($reason) {
            $currentNotes = $order->admin_notes ?? '';
            $updateData['admin_notes'] = $currentNotes . "\n[" . now()->format('Y-m-d H:i:s') . "] Cancelled: " . $reason;
        }

        return $order->update($updateData);
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        $order = $this->findOrFail($orderId);
        return $order->update(['status' => $status]);
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model
            ->with(['user', 'orderDetails.product'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Confirm order and deduct materials
     */
    public function confirmOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->findOrFail($orderId);
            
            // Check material availability first
            $availability = $this->checkMaterialAvailability($orderId);
            if (!$availability['available']) {
                DB::rollBack();
                return false;
            }

            // Deduct materials from stock
            foreach ($order->orderDetails as $detail) {
                if (!$detail->product_id) continue;
                
                $boms = \App\Models\BillOfMaterial::where('product_id', $detail->product_id)->get();

                foreach ($boms as $bom) {
                    $requiredQuantity = $bom->quantity_required * $detail->quantity;
                    $currentStock = $bom->material->getCurrentStock();
                    $newStock = $currentStock - $requiredQuantity;

                    \App\Models\MaterialStock::create([
                        'material_id' => $bom->material_id,
                        'quantity' => $requiredQuantity,
                        'current_stock' => $newStock,
                        'transaction_type' => 'out',
                        'notes' => "Used for order {$order->order_number}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // Update order status
            $estimatedDays = $order->orderDetails->first()?->product?->estimated_production_days ?? 14;
            $order->update([
                'status' => 'confirmed',
                'expected_completion_date' => now()->addDays($estimatedDays),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order confirmation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Start production for order
     */
    public function startProduction(int $orderId): bool
    {
        try {
            $order = $this->findOrFail($orderId);
            return $order->update(['status' => 'in_production']);
        } catch (\Exception $e) {
            \Log::error('Start production failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete order
     */
    public function completeOrder(int $orderId): bool
    {
        try {
            $order = $this->findOrFail($orderId);
            return $order->update([
                'status' => 'completed',
                'actual_completion_date' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Complete order failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check material availability for order
     */
    public function checkMaterialAvailability(int $orderId): array
    {
        $order = $this->findOrFail($orderId);
        $insufficient = [];

        foreach ($order->orderDetails as $detail) {
            if (!$detail->product_id) continue;
            
            $boms = \App\Models\BillOfMaterial::where('product_id', $detail->product_id)->get();

            foreach ($boms as $bom) {
                $requiredQuantity = $bom->quantity_required * $detail->quantity;
                $currentStock = $bom->material->getCurrentStock();

                if ($currentStock < $requiredQuantity) {
                    $insufficient[] = [
                        'material' => $bom->material->name,
                        'required' => $requiredQuantity,
                        'available' => $currentStock,
                        'shortage' => $requiredQuantity - $currentStock,
                    ];
                }
            }
        }

        return [
            'available' => empty($insufficient),
            'insufficient_materials' => $insufficient,
        ];
    }
}
