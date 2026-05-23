<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders
     */
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can view a specific order
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view all orders
        if ($user->role?->name === 'admin') {
            return true;
        }

        // Customer can only view their own orders
        if ($user->role?->name === 'customer') {
            return $order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create orders
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can update an order
     */
    public function update(User $user, Order $order): bool
    {
        // Only admin can update orders
        if ($user->role?->name !== 'admin') {
            return false;
        }

        // Cannot update completed or cancelled orders
        return !in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED]);
    }

    /**
     * Determine if the user can delete an order
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admin can delete orders, and only pending/cancelled ones
        return $user->role?->name === 'admin' && 
               in_array($order->status, [OrderStatus::PENDING, OrderStatus::CANCELLED]);
    }

    /**
     * Determine if the user can cancel an order
     */
    public function cancel(User $user, Order $order): bool
    {
        if ($user->role?->name === 'admin') {
            return !in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED]);
        }

        // Customer can only cancel their own pending orders
        if ($user->role?->name === 'customer') {
            return $order->user_id === $user->id && $order->status === OrderStatus::PENDING;
        }

        return false;
    }

    /**
     * Determine if the user can update order status
     */
    public function updateStatus(User $user, Order $order): bool
    {
        if ($user->role?->name !== 'admin') {
            return false;
        }

        return !in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED], true);
    }

    /**
     * Determine if the user can update shipping information
     */
    public function updateShipping(User $user, Order $order): bool
    {
        return $user->role?->name === 'admin';
    }
}
