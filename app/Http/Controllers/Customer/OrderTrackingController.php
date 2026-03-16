<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\ProductionProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderTrackingController extends Controller
{
    /**
     * Menampilkan daftar riwayat pesanan pelanggan.
     */
    public function index(): View
    {
        $orders = Order::with(['orderDetails.product', 'payment'])
            ->when(auth()->user()?->role?->name === 'customer', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail spesifik dari satu pesanan.
     */
    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load([
            'user',
            'orderDetails.product.category',
            'payment',
            'productionProcesses' => fn($q) => $q->with([
                'orderDetail.product',
                'assignedTo',
                'logs' => fn($q) => $q->with(['user.role'])->orderByDesc('created_at'),
            ])->orderBy('created_at'),
        ]);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Melacak pesanan berdasarkan nomor order (Akses Publik).
     */
    public function track(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('customer.orders.track');
        }

        $validated = $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::with(['orderDetails.product', 'productionProcesses', 'user'])
            ->where('order_number', $validated['order_number'])
            ->whereHas('user', fn($q) => $q->where('email', $validated['email']))
            ->first();

        if (!$order) {
            return back()
                ->with('error', 'Pesanan tidak ditemukan. Pastikan nomor order dan alamat email Anda sudah benar.')
                ->withInput();
        }

        return view('customer.orders.track-result', compact('order'));
    }

    /**
     * Membatalkan pesanan (Hanya jika status pending dan belum dibayar).
     */
    public function cancel(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return back()->with(
                'error',
                'Pesanan hanya dapat dibatalkan jika statusnya masih "Menunggu Pembayaran". Status saat ini: ' . ucfirst($order->status)
            );
        }

        if ($order->payment?->payment_status === 'paid') {
            return back()->with(
                'error',
                'Pesanan yang sudah dibayar tidak dapat dibatalkan secara sistem. Silakan hubungi admin untuk bantuan.'
            );
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('customer.orders.index')
            ->with('success', "Pesanan {$order->order_number} berhasil dibatalkan. ❌");
    }

    /**
     * Menampilkan form untuk membuat pesanan custom.
     */
    public function customOrderForm(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $products = Product::where('is_active', true)
            ->with('category:id,name')
            ->select('id', 'name', 'sku', 'base_price', 'category_id')
            ->get();

        return view('customer.orders.custom', compact('categories', 'products'));
    }

    /**
     * Memproses dan menyimpan pesanan custom.
     */
    public function storeCustomOrder(Request $request): RedirectResponse
    {
        $rules = [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'nullable|integer|exists:products,id',
            'products.*.product_name' => 'required|string|max:255',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.is_custom' => 'nullable|boolean',
            'products.*.customizations' => 'nullable|array',
            'products.*.customizations.description' => 'nullable|string',
            'products.*.customizations.dimensions' => 'nullable|string|max:255',
            'products.*.customizations.material_type' => 'nullable|string|max:255',
            'products.*.customizations.color_finishing' => 'nullable|string|max:255',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
        ];

        $productsInput = $request->input('products', []);
        foreach ($productsInput as $idx => $item) {
            $rules["products.$idx.unit_price"] = empty($item['is_custom'])
                ? 'required|numeric|min:0'
                : 'nullable|numeric|min:0';

            if ($request->hasFile("products.$idx.customizations.design_image")) {
                $rules["products.$idx.customizations.design_image"] = 'file|mimes:jpg,jpeg,png,pdf|max:2048';
            }
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['products'] as $idx => $item) {
                $isCustom = !empty($item['is_custom']);
                $productId = $isCustom ? null : ($item['product_id'] ?? null);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $itemSubtotal = $unitPrice * $item['quantity'];

                $subtotal += $itemSubtotal;

                $customSpecs = $item['customizations'] ?? null;

                if ($isCustom && $request->hasFile("products.$idx.customizations.design_image")) {
                    $customSpecs['design_image'] = $request->file("products.$idx.customizations.design_image")
                        ->store('custom_designs', 'public');
                }

                $orderItems[] = [
                    'product_id' => $productId,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'is_custom' => $isCustom,
                    'custom_specifications' => $customSpecs,
                ];
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'shipping_address' => $validated['shipping_address'],
                'customer_notes' => $validated['notes'] ?? null,
            ]);

            foreach ($orderItems as $item) {
                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'custom_specifications' => $item['custom_specifications'],
                    'is_custom' => $item['is_custom'],
                ]);

                if ($item['product_id'] && !$item['is_custom']) {
                    ProductionProcess::create([
                        'production_code' => ProductionProcess::generateProductionCode(),
                        'order_id' => $order->id,
                        'order_detail_id' => $orderDetail->id,
                        'status' => 'pending',
                        'progress_percentage' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan custom berhasil dibuat! 🎨 Tim kami akan segera meninjau dan memberikan estimasi harga.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal membuat pesanan custom! ❌ ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan halaman upload pembayaran.
     */
    public function showPayment(Order $order): View|RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status === 'cancelled') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Pembayaran ditolak! Pesanan ini telah dibatalkan.');
        }

        if ($order->payment?->payment_status === 'paid') {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah berhasil diproses. ✅');
        }

        if ($order->payment?->payment_proof && $order->payment->payment_status === 'unpaid') {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Bukti pembayaran Anda sudah diupload. Tim kami akan memverifikasi dalam 1-2 hari kerja.');
        }

        return view('customer.payment.index', compact('order'));
    }

    /**
     * Memproses konfirmasi dan bukti pembayaran.
     */
    public function processPayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'payment_method' => 'required|string|in:transfer,cash,credit_card',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'payment_proof.mimes' => 'Format bukti pembayaran harus: JPEG, PNG, atau JPG.',
            'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
        ]);

        try {
            $paymentProofPath = $request->hasFile('payment_proof')
                ? $request->file('payment_proof')->store('payment-proofs', 'public')
                : null;

            $paymentData = [
                'payment_method' => $validated['payment_method'],
                'amount' => $order->total,
                'payment_proof' => $paymentProofPath,
            ];

            if ($paymentProofPath) {
                $paymentData['payment_status'] = 'unpaid';
                $paymentData['payment_date'] = null;
            } else {
                $paymentData['payment_status'] = 'paid';
                $paymentData['payment_date'] = now();
            }

            if ($order->payment) {
                $order->payment->update($paymentData);
            } else {
                $order->payment()->create($paymentData);
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pembayaran berhasil dikonfirmasi! 💳 Tim kami akan segera memverifikasinya.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memproses pembayaran! ❌ ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Memastikan hanya pemilik pesanan (Customer) atau Admin yang bisa mengakses detail order.
     */
    private function authorizeOrder(Order $order): void
    {
        $user = auth()->user();

        if ($user && $user->role?->name === 'customer' && $order->user_id !== $user->id) {
            abort(403, 'Akses ditolak! Anda tidak memiliki izin untuk melihat pesanan ini.');
        }
    }
}