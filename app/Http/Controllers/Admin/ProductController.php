<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $products = $this->productService->getProductsWithFilters(
            search: $request->get('search'),
            categoryId: $request->get('category_id'),
            isActive: $request->filled('is_active') ? (bool) $request->get('is_active') : null,
            perPage: 10
        );

        $categories = $this->productService->getActiveCategories();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk.
     */
    public function create(): View
    {
        $categories = $this->productService->getActiveCategories();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $images = $request->hasFile('images') ? $request->file('images') : null;

            $product = $this->productService->createProduct($validated, $images);

            return redirect()->route('admin.products.index')
                ->with('success', "Produk '{$product->name}' berhasil ditambahkan!");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Detail produk.
     */
    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = $this->productService->getActiveCategories();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $newImages = $request->hasFile('images') ? $request->file('images') : null;

            $this->productService->updateProduct($product, $validated, $newImages);

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderDetails()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus produk yang sudah memiliki riwayat pesanan!');
        }

        try {
            $this->productService->deleteProduct($product);

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}