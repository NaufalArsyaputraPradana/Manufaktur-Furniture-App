<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * CartController
 * 
 * Handles shopping cart operations for customers.
 * Uses CartService for all business logic.
 */
class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
        //
    }

    /**
     * Display shopping cart page
     *
     * @return View
     */
    public function index(): View
    {
        $cart = $this->cartService->getEnrichedCart();
        $total = $this->cartService->getTotal();

        return view('customer.cart.index', compact('cart', 'total'));
    }

    /**
     * Add product to cart
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:100',
            'image' => 'nullable|string',
            'custom_dimensions' => 'nullable|array',
        ]);

        try {
            $this->cartService->addItem(
                $validated['product_id'],
                $validated['product_name'],
                $validated['price'],
                $validated['quantity'],
                $validated['image'] ?? null,
                $validated['custom_dimensions'] ?? null
            );

            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan produk ke keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Update item quantity in cart (AJAX)
     *
     * @param Request $request
     * @param string $itemKey
     * @return JsonResponse
     */
    public function update(Request $request, string $itemKey): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        try {
            $this->cartService->updateItemQuantity($itemKey, $validated['quantity']);

            return response()->json([
                'success' => true,
                'message' => 'Jumlah keranjang berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove item from cart
     *
     * @param string $itemKey
     * @return RedirectResponse
     */
    public function remove(string $itemKey): RedirectResponse
    {
        try {
            $this->cartService->removeItem($itemKey);

            return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clear entire cart
     *
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();

        return redirect()->route('customer.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
