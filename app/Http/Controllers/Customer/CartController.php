<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        
        // Enrich cart dengan data product dari database
        $enrichedCart = [];
        foreach ($cart as $itemKey => $item) {
            $enrichedCart[$itemKey] = $item;
            
            // Ambil product dari database untuk mendapat images terbaru
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product && $product->images) {
                    // Jika product punya images, ambil dari sana
                    $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                    if (!empty($images) && isset($images[0])) {
                        $enrichedCart[$itemKey]['image'] = $images[0];
                    }
                }
            }
        }
        
        $total = collect($enrichedCart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('customer.cart.index', compact('total'))
            ->with('cart', $enrichedCart);
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'        => 'required|exists:products,id',
            'product_name'      => 'required|string',
            'price'             => 'required|numeric|min:0',
            'quantity'          => 'required|integer|min:1',
            'image'             => 'nullable|string',
            'custom_dimensions' => 'nullable|array',
        ]);

        $cart = session()->get('cart', []);
        $productId = $validated['product_id'];

        $itemKey = (string) $productId;
        if (!empty($validated['custom_dimensions'])) {
            $itemKey .= '_' . md5(json_encode($validated['custom_dimensions']));
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $validated['quantity'];
            // Update image jika ada perubahan
            if (!empty($validated['image'])) {
                $cart[$itemKey]['image'] = $validated['image'];
            }
        } else {
            $cart[$itemKey] = [
                'product_id'        => $productId,
                'name'              => $validated['product_name'],
                'price'             => $validated['price'],
                'quantity'          => $validated['quantity'],
                'image'             => $validated['image'] ?? null,
                'custom_dimensions' => $validated['custom_dimensions'] ?? null,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Memperbarui jumlah item di keranjang (via AJAX).
     */
    public function update(Request $request, string $itemKey): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$itemKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan di dalam keranjang.',
            ], 404);
        }

        $cart[$itemKey]['quantity'] = $validated['quantity'];
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah keranjang berhasil diperbarui.',
        ]);
    }

    /**
     * Menghapus item spesifik dari keranjang.
     */
    public function remove(string $itemKey): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$itemKey])) {
            return back()->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        unset($cart[$itemKey]);
        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Mengosongkan seluruh isi keranjang.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('customer.cart.index')
            ->with('success', 'Keranjang Anda telah berhasil dikosongkan.');
    }
}