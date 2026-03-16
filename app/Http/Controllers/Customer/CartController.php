<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('customer.cart.index', compact('cart', 'total'));
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
        } else {
            $cart[$itemKey] = [
                'product_id'        => $productId,
                'name'              => $validated['product_name'],
                'price'             => $validated['price'],
                'quantity'          => $validated['quantity'],
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