<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    /**
     * Menampilkan daftar kategori.
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getCategoriesWithFilters(
            search: $request->get('search'),
            isActive: $request->filled('is_active') ? (bool) $request->get('is_active') : null,
            sortBy: $request->get('sort_by', 'latest'),
            sortOrder: $request->get('sort_order', 'desc'),
            perPage: 10
        );

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori baru.
     */
    public function create(): View
    {
        $parents = $this->categoryService->getActiveRootCategories();

        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Simpan kategori baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            $image = $request->hasFile('image') ? $request->file('image') : null;
            $category = $this->categoryService->createCategory($validated, $image);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail kategori.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children'])->loadCount('products');
        $products = $category->products()->latest()->paginate(10);

        return view('admin.categories.show', compact('category', 'products'));
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category): View
    {
        $parents = Category::active()
            ->root()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update kategori di database.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id'   => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            $newImage = $request->hasFile('image') ? $request->file('image') : null;
            $this->categoryService->updateCategory($category, $validated, $newImage);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kategori dari database.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki produk! Silakan hapus atau pindahkan produk terlebih dahulu.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang memiliki sub-kategori.');
        }

        try {
            $this->categoryService->deleteCategory($category);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}