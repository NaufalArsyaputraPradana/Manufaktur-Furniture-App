<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $savedImages = [];

        try {
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $savedImages[] = $file->store('products', 'public');
                }
            }

            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
            $validated['images'] = !empty($savedImages) ? $savedImages : null;
            $validated['is_customizable'] = $request->boolean('is_customizable');
            $validated['is_active'] = $request->boolean('is_active');

            Product::create($validated);

            return redirect()->route('admin.products.index')
                ->with('success', "Produk '{$validated['name']}' berhasil ditambahkan!");

        } catch (\Exception $e) {
            foreach ($savedImages as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }

            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Detail produk.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        try {
            $currentImages = $product->images ?? [];

            if ($request->hasFile('images')) {
                foreach ($currentImages as $oldImg) {
                    if (Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }
                }

                $newImages = [];
                foreach ($request->file('images') as $file) {
                    $newImages[] = $file->store('products', 'public');
                }
                $validated['images'] = $newImages;
            } else {
                $validated['images'] = $currentImages;
            }

            if ($product->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
            }

            $validated['is_customizable'] = $request->boolean('is_customizable');
            $validated['is_active'] = $request->boolean('is_active');

            $product->update($validated);

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product)
    {
        if ($product->orderDetails()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus produk yang sudah memiliki riwayat pesanan!');
        }

        try {
            $imagesToDelete = $product->images ?? [];

            $product->delete();

            foreach ($imagesToDelete as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}