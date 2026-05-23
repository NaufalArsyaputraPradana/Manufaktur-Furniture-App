<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    /**
     * Menampilkan halaman checkout.
     */
    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Use CartService for enrichment (consistent with CartController)
    $enrichedCart = $this->cartService->getEnrichedCart();
    $cart = $enrichedCart;
        $subtotal = $this->cartService->getTotal();
        $total = $subtotal;

    return view('customer.checkout.index', compact('enrichedCart', 'cart', 'subtotal', 'total'));
    }

    /**
     * Memproses checkout dan membuat record pesanan (Order).
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|regex:/^(\+62|0)[0-9]{9,12}$/',
            'customer_notes' => 'nullable|string|max:1000',
        ], [
            'phone.regex' => 'Nomor telepon harus format Indonesia (cth: 0812345678 atau +6212345678).',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Sesi keranjang telah berakhir atau kosong.');
        }

        try {
            $order = DB::transaction(function () use ($validated, $cart) {
                $productIds = collect($cart)->pluck('product_id')->filter()->unique()->all();
                $dbProducts = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

                $subtotal = 0;
                foreach ($cart as &$item) {
                    if (empty($item['product_id']) || empty($item['name'])) {
                        throw new \RuntimeException('Data produk di keranjang tidak lengkap. Silakan ulangi proses checkout.');
                    }

                    $dbProduct = $dbProducts[$item['product_id']] ?? null;
                    if (!$dbProduct) {
                        throw new \RuntimeException('Produk di keranjang sudah tidak tersedia. Silakan perbarui keranjang.');
                    }

                    // Use database price only when it's available; keep cart price for custom or legacy items.
                    if ($dbProduct->base_price !== null) {
                        $item['price'] = (float) $dbProduct->base_price;
                    } else {
                        $item['price'] = (float) ($item['price'] ?? 0);
                    }

                    $item['quantity'] = (int) ($item['quantity'] ?? 0);
                    if ($item['quantity'] < 1) {
                        throw new \RuntimeException('Jumlah produk tidak valid. Silakan perbarui keranjang.');
                    }

                    $subtotal += $item['price'] * $item['quantity'];
                }
                unset($item);

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'shipping_address' => $validated['shipping_address'],
                    'phone' => $validated['phone'],
                    'customer_notes' => $validated['customer_notes'] ?? null,
                    'expected_completion_date' => now()->addDays(14),
                ]);

                $orderDetails = [];

                foreach ($cart as $item) {
                    $hasCustom = !empty($item['custom_dimensions']);
                    $customSpecs = null;

                    if ($hasCustom) {
                        $customSpecs = is_array($item['custom_dimensions'])
                            ? $item['custom_dimensions']
                            : json_decode($item['custom_dimensions'], true);
                    }

                    $orderDetails[] = [
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'is_custom' => $hasCustom,
                        'custom_specifications' => $customSpecs ? json_encode($customSpecs) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                OrderDetail::insert($orderDetails);
                
                return $order;
            });
            
            // Clear session AFTER transaction commits
            session()->forget('cart');

            return redirect()->route('customer.orders.payment', $order)
                ->with('success', "🎉 Pesanan berhasil dibuat! Nomor order Anda: {$order->order_number}");

        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'user_id' => Auth::id(),
                'exception' => $e,
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat memproses pesanan. Silakan coba lagi.')
                ->withInput();
        }
    }
}