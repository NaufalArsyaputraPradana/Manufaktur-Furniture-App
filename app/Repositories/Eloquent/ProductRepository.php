<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Product Repository Implementation
 * 
 * Handles all product-related database operations with optimized queries
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all products with filters and pagination
     */
    public function getAllWithRelations(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['category', 'images'])
            ->latest();

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Search by name, SKU, or description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range filters
        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        // Sorting
        if (!empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * Get all active products
     */
    public function getAllActive(): Collection
    {
        return $this->model
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->model
            ->with(['category', 'images', 'billOfMaterials.material'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->model
            ->with(['category', 'images'])
            ->where('sku', $sku)
            ->first();
    }

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, array $filters = []): LengthAwarePaginator
    {
        $filters['category_id'] = $categoryId;
        $filters['is_active'] = $filters['is_active'] ?? true;
        
        return $this->getAllWithRelations($filters);
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 8): Collection
    {
        return $this->model
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->limit($limit)
            ->get();
    }

    /**
     * Search products by name or description
     */
    public function search(string $query, array $filters = []): LengthAwarePaginator
    {
        $filters['search'] = $query;
        $filters['is_active'] = $filters['is_active'] ?? true;
        
        return $this->getAllWithRelations($filters);
    }

    /**
     * Get products with low stock
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->model
            ->with(['category'])
            ->where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * Get product statistics
     */
    public function getProductStatistics(): array
    {
        $stats = $this->model
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
                SUM(stock) as total_stock,
                AVG(base_price) as average_price,
                MIN(base_price) as min_price,
                MAX(base_price) as max_price
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'active' => $stats->active ?? 0,
            'inactive' => $stats->inactive ?? 0,
            'featured' => $stats->featured ?? 0,
            'total_stock' => $stats->total_stock ?? 0,
            'average_price' => round($stats->average_price ?? 0, 2),
            'min_price' => $stats->min_price ?? 0,
            'max_price' => $stats->max_price ?? 0,
        ];
    }

    /**
     * Update product stock
     */
    public function updateStock(int $productId, int $quantity): bool
    {
        try {
            $product = $this->findOrFail($productId);
            $newStock = $product->stock + $quantity;
            
            return $product->update(['stock' => max(0, $newStock)]);
        } catch (\Exception $e) {
            \Log::error('Update stock failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle product active status
     */
    public function toggleActive(int $productId): bool
    {
        try {
            $product = $this->findOrFail($productId);
            return $product->update(['is_active' => !$product->is_active]);
        } catch (\Exception $e) {
            \Log::error('Toggle active failed: ' . $e->getMessage());
            return false;
        }
    }
}
