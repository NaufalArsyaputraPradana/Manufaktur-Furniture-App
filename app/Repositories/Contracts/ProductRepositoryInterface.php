<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Product Repository Interface
 * 
 * Defines contract for product-related database operations
 */
interface ProductRepositoryInterface
{
    /**
     * Get all products with filters and pagination
     */
    public function getAllWithRelations(array $filters = []): LengthAwarePaginator;

    /**
     * Get all active products
     */
    public function getAllActive(): Collection;

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product;

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product;

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, array $filters = []): LengthAwarePaginator;

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 8): Collection;

    /**
     * Search products by name or description
     */
    public function search(string $query, array $filters = []): LengthAwarePaginator;

    /**
     * Get products with low stock
     */
    public function getLowStock(int $threshold = 10): Collection;

    /**
     * Get product statistics
     */
    public function getProductStatistics(): array;

    /**
     * Update product stock
     */
    public function updateStock(int $productId, int $quantity): bool;

    /**
     * Toggle product active status
     */
    public function toggleActive(int $productId): bool;
}
