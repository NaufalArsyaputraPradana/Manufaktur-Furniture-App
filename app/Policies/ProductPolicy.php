<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view any products
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view product list
    }

    /**
     * Determine if the user can view a product
     */
    public function view(User $user, Product $product): bool
    {
        // Admin can view any product
        if ($user->role?->name === 'admin') {
            return true;
        }

        // Customer can only view active products
        if ($user->role?->name === 'customer') {
            return $product->is_active;
        }

        return $product->is_active;
    }

    /**
     * Determine if the user can create products
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can update a product
     */
    public function update(User $user, Product $product): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can delete a product
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->role?->name === 'admin';
    }
}
