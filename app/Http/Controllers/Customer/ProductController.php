<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Menampilkan katalog produk dengan query yang dioptimalkan.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->select(
                'id',
                'category_id',
                'name',
                'slug',
                'description',
                'base_price',
                'sku',
                'is_active',
                'images',
                'dimensions'
            );

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        match ($request->get('sort', 'latest')) {
            'price_low' => $query->orderBy('base_price', 'asc'),
            'price_high' => $query->orderBy('base_price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Cache::remember(
            'active_categories_with_count',
            3600,
            fn() => Category::where('is_active', true)
                ->withCount(['products' => fn($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get()
        );

        return view('customer.products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan detail produk secara spesifik (eager loading yang dioptimalkan).
     */
    public function show(Product $product): View
    {
        $product->load(['category:id,name,slug']);

        $cacheKey = "related_products_" . ($product->category_id ?? 'all') . "_{$product->id}";
        $relatedProducts = Cache::remember(
            $cacheKey,
            1800,
            fn() => Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->with(['category:id,name,slug'])
                ->select('id', 'category_id', 'name', 'slug', 'base_price', 'sku', 'is_active', 'images')
                ->latest()
                ->take(4)
                ->get()
        );

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Mengestimasi harga berdasarkan dimensi kustom pelanggan via AJAX.
     */
    public function estimatePrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'width' => 'required|numeric|min:10',
            'height' => 'required|numeric|min:10',
            'depth' => 'nullable|numeric|min:10',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $width = (float) $validated['width'];
        $height = (float) $validated['height'];
        $depth = (float) ($validated['depth'] ?? 50);

        $estimatedPrice = $this->calculatePriceFromVolume($product, $width, $height, $depth);

        return response()->json([
            'success' => true,
            'estimated_price' => $estimatedPrice,
            'formatted_price' => 'Rp ' . number_format($estimatedPrice, 0, ',', '.'),
            'dimensions' => compact('width', 'height', 'depth'),
            'calculation_method' => 'volume',
        ]);
    }

    /**
     * Kalkulasi harga dari rasio volume (fallback ketika tidak ada relasi BOM).
     */
    private function calculatePriceFromVolume(Product $product, float $width, float $height, float $depth): float
    {
        $baseDimensionStr = $product->dimensions ?? '100x100x50';
        $base = $this->parseBaseDimensions($baseDimensionStr);

        $baseVolume = ($base['width'] * $base['height'] * $base['depth']);

        if ($baseVolume <= 0) {
            $baseVolume = 100 * 100 * 50;
        }

        $volumeRatio = ($width * $height * $depth) / $baseVolume;

        return round($product->base_price * $volumeRatio * 1.2, 0);
    }

    /**
     * Parse string dimensi berformat "WxHxD" menjadi array asosiatif.
     */
    private function parseBaseDimensions(string $dimensions): array
    {
        $parts = explode('x', strtolower($dimensions));

        return [
            'width' => (float) ($parts[0] ?? 100),
            'height' => (float) ($parts[1] ?? 100),
            'depth' => (float) ($parts[2] ?? 50),
        ];
    }
}