<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Order Repository Interface
 * 
 * Defines contract for order-related database operations
 */
interface OrderRepositoryInterface
{
    /**
     * Get all orders with relationships and filters
     */
    public function getAllWithRelations(array $filters = []): LengthAwarePaginator;

    /**
     * Find order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Order;

    /**
     * Get orders by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get customer's orders
     */
    public function getCustomerOrders(int $userId, array $filters = []): LengthAwarePaginator;

    /**
     * Get pending orders (pending + confirmed)
     */
    public function getPendingOrders(): Collection;

    /**
     * Get completed orders
     */
    public function getCompletedOrders(int $limit = 10): Collection;

    /**
     * Get order statistics
     */
    public function getOrderStatistics(): array;

    /**
     * Create order with details in transaction
     */
    public function createWithDetails(array $orderData, array $orderDetails): Order;

    /**
     * Cancel order with reason
     */
    public function cancelOrder(int $orderId, string $reason): bool;

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool;

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Confirm order and deduct materials
     */
    public function confirmOrder(int $orderId): bool;

    /**
     * Start production for order
     */
    public function startProduction(int $orderId): bool;

    /**
     * Complete order
     */
    public function completeOrder(int $orderId): bool;

    /**
     * Check material availability for order
     */
    public function checkMaterialAvailability(int $orderId): array;
}
