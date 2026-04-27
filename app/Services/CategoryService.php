<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service untuk menangani logika bisnis Category
 */
class CategoryService
{
    /**
     * Membuat kategori baru dengan image upload
     */
    public function createCategory(array $data, ?object $image = null): Category
    {
        try {
            if ($image) {
                $data['image'] = $image->store('categories', 'public');
            }

            $data['slug'] = Str::slug($data['name']);
            $data['is_active'] = $data['is_active'] ?? true;

            return Category::create($data);

        } catch (\Exception $e) {
            if (isset($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            throw $e;
        }
    }

    /**
     * Memperbarui kategori dengan image upload
     */
    public function updateCategory(Category $category, array $data, ?object $newImage = null): Category
    {
        try {
            if ($newImage) {
                // Delete old image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                $data['image'] = $newImage->store('categories', 'public');
            }

            $data['is_active'] = $data['is_active'] ?? $category->is_active;

            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = Str::slug($data['name']);
            }

            $category->update($data);
            return $category->fresh();

        } catch (\Exception $e) {
            if (isset($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            throw $e;
        }
    }

    /**
     * Menghapus kategori dengan image cleanup
     */
    public function deleteCategory(Category $category): bool
    {
        try {
            // Delete image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            return $category->delete();

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get categories dengan filtering, sorting dan pagination
     */
    public function getCategoriesWithFilters(
        ?string $search = null,
        ?bool $isActive = null,
        ?string $sortBy = 'latest',
        ?string $sortOrder = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Category::with(['parent'])->withCount(['products', 'children']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Apply sorting
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'products':
                $query->orderByRaw("(SELECT COUNT(*) FROM products WHERE products.category_id = categories.id) {$sortOrder}");
                break;
            default:
                $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get active root categories
     */
    public function getActiveRootCategories()
    {
        return Category::active()->root()->orderBy('name')->get();
    }
}
