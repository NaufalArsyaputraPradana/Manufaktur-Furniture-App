<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ProcessOrderPaymentRequest;
use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\ProductionProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['orderDetails.product', 'payment'])
            ->when(Auth::user()?->role?->name === 'customer', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load([
            'user',
            'orderDetails.product.category',
            'payment',
            'productionProcesses' => fn ($q) => $q->with([
                'orderDetail.product',
                'assignedTo',
                'logs' => fn ($q) => $q->with(['user.role'])->orderByDesc('created_at'),
            ])->orderBy('created_at'),
        ]);

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return back()->with(
                'error',
                'Pesanan hanya dapat dibatalkan jika statusnya masih "Menunggu Pembayaran". Status saat ini: ' . ucfirst($order->status)
            );
        }

        $st = $order->payment?->payment_status;
        if (in_array($st, [Payment::STATUS_PAID, Payment::STATUS_DP_PAID], true)) {
            return back()->with(
                'error',
                'Pesanan yang sudah memiliki pembayaran terverifikasi tidak dapat dibatalkan dari halaman ini. Hubungi admin.'
            );
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('customer.orders.index')
            ->with('success', "Pesanan {$order->order_number} berhasil dibatalkan.");
    }

    public function customOrderForm(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $products = Product::where('is_active', true)
            ->with('category:id,name')
            ->select('id', 'name', 'sku', 'base_price', 'category_id')
            ->get();

        return view('customer.orders.custom', compact('categories', 'products'));
    }

    public function storeCustomOrder(Request $request): RedirectResponse
    {
        $rules = [
            'products' => 'required|array|min:1',
            'products.*.product_id' => ['nullable'],
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
            $isCatalogCustom = ($item['product_id'] ?? null) === 'custom';
            $rules["products.$idx.unit_price"] = (!empty($item['is_custom']) || $isCatalogCustom)
                ? 'nullable|numeric|min:0'
                : 'required|numeric|min:0';

            if ($request->hasFile("products.$idx.customizations.design_image")) {
                $rules["products.$idx.customizations.design_image"] = 'file|mimes:jpg,jpeg,png,pdf,webp|max:2048';
            }
        }

        $validated = $request->validate($rules);

        foreach ($validated['products'] as $idx => $item) {
            $pid = $item['product_id'] ?? null;
            if ($pid !== null && $pid !== '' && $pid !== 'custom' && is_numeric($pid)) {
                if (!Product::whereKey((int) $pid)->exists()) {
                    return back()
                        ->withErrors(["products.$idx.product_id" => 'Produk yang dipilih tidak valid.'])
                        ->withInput();
                }
            }
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['products'] as $idx => $item) {
                $isCustom = !empty($item['is_custom']) || ($item['product_id'] ?? null) === 'custom';
                $productId = $isCustom ? null : (is_numeric($item['product_id'] ?? null) ? (int) $item['product_id'] : null);
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
                'user_id' => Auth::id(),
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
                ->with('success', 'Pesanan custom berhasil dibuat! Tim kami akan segera meninjau estimasi harga.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Gagal membuat pesanan custom: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showPayment(Order $order): View|RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status === 'cancelled') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Pesanan ini telah dibatalkan.');
        }

        if ($order->payment?->payment_status === Payment::STATUS_PAID) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah lunas.');
        }

        $proof = $order->payment?->payment_proof;
        $st = $order->payment?->payment_status;

        if ($proof && $st === Payment::STATUS_PENDING) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Bukti pembayaran Anda menunggu verifikasi admin.');
        }

        if ($proof && $st === Payment::STATUS_DP_PAID) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Bukti pelunasan Anda menunggu verifikasi admin.');
        }

        $order->loadMissing('orderDetails.product', 'payment');

        // Get active bank accounts from database
        $bankAccounts = \App\Models\BankAccount::active()->get();
        
        // For backward compatibility, if no bank accounts in DB, use config
        $bank = $bankAccounts->count() > 0 ? [
            'name' => $bankAccounts->first()->bank_name,
            'holder' => $bankAccounts->first()->account_holder,
            'account' => $bankAccounts->first()->account_number,
        ] : config('orders.bank', []);

        return view('customer.payment.index', [
            'order' => $order,
            'dpAmount' => round((float) $order->total * (float) config('orders.down_payment_percent', 30) / 100, 2),
            'bank' => $bank,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function processPayment(ProcessOrderPaymentRequest $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Pesanan dibatalkan.');
        }

        $validated = $request->validated();
        $channel = $validated['payment_channel'];
        $total = (float) $order->total;

        $existing = $order->payment;

        if ($channel === Payment::CHANNEL_MANUAL_DP) {
            if ($existing && $existing->payment_status === Payment::STATUS_DP_PAID) {
                if (!$request->hasFile('payment_proof')) {
                    return back()->with('error', 'Unggah bukti pelunasan.');
                }
            } elseif ($existing && $existing->payment_status === Payment::STATUS_PENDING && $existing->payment_proof) {
                return redirect()->route('customer.orders.show', $order)
                    ->with('info', 'Bukti Anda sedang diverifikasi.');
            }
        }

        try {
            $proofPath = $request->hasFile('payment_proof')
                ? $request->file('payment_proof')->store('payment-proofs', 'public')
                : null;

            $dpAmount = round($total * (float) config('orders.down_payment_percent', 30) / 100, 2);

            if ($channel === Payment::CHANNEL_MANUAL_DP) {
                if ($existing && $existing->payment_status === Payment::STATUS_DP_PAID) {
                    if ($existing->payment_proof && Storage::disk('public')->exists($existing->payment_proof)) {
                        Storage::disk('public')->delete($existing->payment_proof);
                    }
                    $existing->update([
                        'payment_method'       => Payment::METHOD_BANK_TRANSFER,
                        'payment_channel'      => Payment::CHANNEL_MANUAL_DP,
                        'payment_proof'        => $proofPath,
                        'payment_status'       => Payment::STATUS_DP_PAID,
                        'amount'               => $total,
                        'expected_dp_amount'   => $existing->expected_dp_amount ?? $dpAmount,
                    ]);
                } else {
                    Payment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'amount'                 => $total,
                            'amount_paid'            => 0,
                            'expected_dp_amount'     => $dpAmount,
                            'payment_method'         => Payment::METHOD_BANK_TRANSFER,
                            'payment_channel'        => Payment::CHANNEL_MANUAL_DP,
                            'payment_status'         => Payment::STATUS_PENDING,
                            'payment_proof'          => $proofPath,
                            'transaction_id'         => Payment::generateTransactionId(),
                        ]
                    );
                }
            } elseif ($channel === Payment::CHANNEL_MANUAL_FULL) {
                if ($existing?->payment_proof && Storage::disk('public')->exists($existing->payment_proof)) {
                    Storage::disk('public')->delete($existing->payment_proof);
                }
                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'amount'             => $total,
                        'amount_paid'        => 0,
                        'expected_dp_amount' => null,
                        'payment_method'     => Payment::METHOD_BANK_TRANSFER,
                        'payment_channel'    => Payment::CHANNEL_MANUAL_FULL,
                        'payment_status'     => Payment::STATUS_PENDING,
                        'payment_proof'      => $proofPath,
                        'transaction_id'     => $existing?->transaction_id ?? Payment::generateTransactionId(),
                    ]
                );
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Bukti pembayaran berhasil diunggah. Tim kami akan memverifikasi.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function authorizeOrder(Order $order): void
    {
        $user = Auth::user();

        if ($user && $user->role?->name === 'customer' && $order->user_id !== $user->id) {
            abort(403, 'Akses ditolak! Anda tidak memiliki izin untuk melihat pesanan ini.');
        }
    }
}
