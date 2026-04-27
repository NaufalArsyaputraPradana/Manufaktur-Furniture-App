<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service untuk menangani logika bisnis Product
 */
class ProductService
{
    /**
     * Membuat produk baru dengan file uploads
     */
    public function createProduct(array $data, ?array $images = null): Product
    {
        $savedImages = [];

        try {
            // Handle file uploads
            if ($images) {
                foreach ($images as $file) {
                    $savedImages[] = $file->store('products', 'public');
                }
            }

            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
            $data['images'] = !empty($savedImages) ? $savedImages : null;
            $data['is_customizable'] = $data['is_customizable'] ?? false;
            $data['is_active'] = $data['is_active'] ?? true;

            return Product::create($data);

        } catch (\Exception $e) {
            // Cleanup uploaded files on error
            foreach ($savedImages as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
            throw $e;
        }
    }

    /**
     * Memperbarui produk dengan file uploads
     */
    public function updateProduct(Product $product, array $data, ?array $newImages = null): Product
    {
        $savedImages = [];

        try {
            // Handle new file uploads
            if ($newImages) {
                foreach ($newImages as $file) {
                    $savedImages[] = $file->store('products', 'public');
                }
            }

            // Merge with existing images if not replacing
            if ($savedImages && !isset($data['replace_images'])) {
                $currentImages = is_array($product->images) ? $product->images : ($product->images ? json_decode($product->images, true) : []);
                $data['images'] = array_merge($currentImages, $savedImages);
            } elseif ($savedImages) {
                $data['images'] = $savedImages;
            }

            $data['is_customizable'] = $data['is_customizable'] ?? $product->is_customizable;
            $data['is_active'] = $data['is_active'] ?? $product->is_active;

            $product->update($data);
            return $product->fresh();

        } catch (\Exception $e) {
            // Cleanup uploaded files on error
            foreach ($savedImages as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
            throw $e;
        }
    }

    /**
     * Menghapus produk dengan image cleanup
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            // Delete associated images
            if ($product->images) {
                $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                foreach ((array) $images as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }
            }

            return $product->delete();

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Menghapus image tertentu dari produk
     */
    public function deleteProductImage(Product $product, string $imagePath): void
    {
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
        $images = array_filter($images, fn($img) => $img !== $imagePath);
        
        $product->update(['images' => array_values($images)]);
    }

    /**
     * Get products dengan filtering dan pagination
     */
    public function getProductsWithFilters(
        ?string $search = null,
        ?int $categoryId = null,
        ?bool $isActive = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Product::with('category:id,name')
            ->select('id', 'category_id', 'sku', 'name', 'base_price', 'is_active', 'images', 'created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return Category::where('is_active', true)->orderBy('name')->get();
    }
}
