<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Enrich cart dengan data product dari database
        $enrichedCart = [];
        foreach ($cart as $itemKey => $item) {
            $enrichedCart[$itemKey] = $item;
            
            // Ambil product dari database untuk mendapat images dan description terbaru
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    // Ambil images
                    if ($product->images) {
                        $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                        if (!empty($images) && isset($images[0])) {
                            $enrichedCart[$itemKey]['image'] = $images[0];
                        }
                    }
                    
                    // Ambil description
                    if ($product->description) {
                        $enrichedCart[$itemKey]['description'] = $product->description;
                    }
                    
                    // Ambil dimensions
                    if ($product->dimensions) {
                        $enrichedCart[$itemKey]['dimensions'] = $product->dimensions;
                    }
                }
            }
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $total = $subtotal;

        return view('customer.checkout.index', compact('enrichedCart', 'subtotal', 'total'))
            ->with('cart', $enrichedCart);
    }

    /**
     * Memproses checkout dan membuat record pesanan (Order).
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Sesi keranjang telah berakhir atau kosong.');
        }

        DB::beginTransaction();

        try {
            $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
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

            session()->forget('cart');

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', "🎉 Pesanan berhasil dibuat! Nomor order Anda: {$order->order_number}");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat memproses pesanan: ' . $e->getMessage())
                ->withInput();
        }
    }
}