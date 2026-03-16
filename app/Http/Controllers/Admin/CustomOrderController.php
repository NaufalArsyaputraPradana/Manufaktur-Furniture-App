<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomOrderController extends Controller
{
    /**
     * Menampilkan daftar item order yang bersifat custom.
     */
    public function index(Request $request): View
    {
        $query = OrderDetail::with(['order.user'])
            ->where('is_custom', true);

        // Filter berdasarkan order_id jika ada
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Statistik (berdasarkan query setelah filter)
        $statsQuery = clone $query;
        $totalCount = $statsQuery->count();
        $pendingCount = (clone $statsQuery)->where('unit_price', 0)->count();
        $processedCount = (clone $statsQuery)->where('unit_price', '>', 0)->count();

        // Filter Pencarian (Nama Produk, No Order, atau Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($o) use ($search) {
                        $o->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        // Filter Status Perhitungan
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('unit_price', 0);
            } elseif ($request->status === 'processed') {
                $query->where('unit_price', '>', 0);
            }
        }

        $customOrders = $query->latest()->paginate(10)->withQueryString();

        return view('admin.custom_orders.index', compact(
            'customOrders',
            'totalCount',
            'pendingCount',
            'processedCount'
        ));
    }

    /**
     * Tampilkan form kalkulator harga (BOM).
     */
    public function calculate(OrderDetail $orderDetail): View|RedirectResponse
    {
        if (!$orderDetail->is_custom) {
            return back()->with('error', 'Item ini bukan merupakan produk custom.');
        }

        // Cek apakah order terkait sudah dibatalkan
        if ($orderDetail->order && $orderDetail->order->status === 'cancelled') {
            return back()->with('error', 'Tidak dapat menghitung harga untuk pesanan yang sudah dibatalkan.');
        }

        $orderDetail->load('order.user');
        $specs = $orderDetail->custom_specifications ?? [];
        $existingBom = $specs['bom'] ?? null;

        return view('admin.custom_orders.calculate', compact('orderDetail', 'existingBom'));
    }

    /**
     * Simpan hasil perhitungan harga (BOM) ke dalam JSON spesifikasi.
     */
    public function store(Request $request, OrderDetail $orderDetail): RedirectResponse
    {
        // Validasi tambahan: pastikan order tidak dibatalkan
        if ($orderDetail->order && $orderDetail->order->status === 'cancelled') {
            return back()->with('error', 'Tidak dapat menyimpan harga untuk pesanan yang sudah dibatalkan.');
        }

        $validated = $request->validate([
            'grade'            => 'required|string|in:A,B,C',
            'komponen'         => 'required|array|min:1',
            'komponen.*.nama'  => 'required|string|max:255',
            'komponen.*.panjang' => 'required|numeric|min:0',
            'komponen.*.lebar'   => 'required|numeric|min:0',
            'komponen.*.tebal'   => 'required|numeric|min:0',
            'komponen.*.jumlah'  => 'required|integer|min:1',
            'production_cost'    => 'nullable|numeric|min:0',
            'rattan_cost'        => 'nullable|numeric|min:0',
            'foam_cost'          => 'nullable|numeric|min:0',
            'finishing_cost'     => 'nullable|numeric|min:0',
            'other_cost'         => 'nullable|numeric|min:0',
            'profit_percent'     => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            // 1. Hitung Kubikasi Kayu (Server-side)
            $totalVolume = 0;
            $komponenData = [];

            foreach ($validated['komponen'] as $k) {
                // Rumus: (P x L x T) / 1.000.000 untuk konversi cm3 ke m3
                $volPerPiece = ($k['panjang'] * $k['lebar'] * $k['tebal']) / 1000000;
                $totalVolItem = $volPerPiece * $k['jumlah'];
                $totalVolume += $totalVolItem;

                $komponenData[] = array_merge($k, [
                    'volume_per_pc' => $volPerPiece,
                    'total_volume'  => $totalVolItem,
                ]);
            }

            // 2. Tentukan Biaya Kayu per Grade (Mapping)
            $woodPrices = [
                'A' => 12000000, // Rp 12jt/m3
                'B' => 10000000, // Rp 10jt/m3
                'C' => 8000000,  // Rp 8jt/m3
            ];

            $woodPricePerM3 = $woodPrices[$validated['grade']];
            $woodCostTotal = $totalVolume * $woodPricePerM3;

            // 3. Kalkulasi Total Biaya Tambahan (Lain-lain)
            $additionalCosts = ($validated['production_cost'] ?? 0) +
                ($validated['rattan_cost'] ?? 0) +
                ($validated['foam_cost'] ?? 0) +
                ($validated['finishing_cost'] ?? 0) +
                ($validated['other_cost'] ?? 0);

            // 4. Hitung HPP dan Harga Jual
            $hpp = $woodCostTotal + $additionalCosts;
            $profitAmount = $hpp * ($validated['profit_percent'] / 100);
            $sellingPriceRaw = $hpp + $profitAmount;

            // Pembulatan ke atas ke ribuan terdekat (e.g., 1.500.200 -> 1.501.000)
            $sellingPrice = ceil($sellingPriceRaw / 1000) * 1000;

            // 5. Susun Metadata BOM
            $bomData = [
                'grade'           => $validated['grade'],
                'wood_price_m3'   => $woodPricePerM3,
                'total_volume'    => $totalVolume,
                'wood_cost_total' => $woodCostTotal,
                'komponen'        => $komponenData,
                'costs'           => [
                    'production' => $validated['production_cost'] ?? 0,
                    'rattan'     => $validated['rattan_cost'] ?? 0,
                    'foam'       => $validated['foam_cost'] ?? 0,
                    'finishing'  => $validated['finishing_cost'] ?? 0,
                    'other'      => $validated['other_cost'] ?? 0,
                ],
                'hpp'            => $hpp,
                'profit_percent' => $validated['profit_percent'],
                'profit_amount'  => $profitAmount,
                'calculated_at'  => now()->toDateTimeString(),
                'calculated_by'  => auth()->id(),
            ];

            // 6. Update Database (OrderDetail)
            $specs = $orderDetail->custom_specifications ?? [];
            $specs['bom'] = $bomData;

            $orderDetail->update([
                'unit_price'           => $sellingPrice,
                'subtotal'             => $sellingPrice * $orderDetail->quantity,
                'custom_specifications' => $specs,
            ]);

            // 7. Sinkronisasi Total Harga di Order Induk
            $this->recalculateOrderTotal($orderDetail->order);

            DB::commit();

            return redirect()
                ->route('admin.custom-orders.index')
                ->with('success', 'Harga berhasil dihitung! Harga jual: Rp ' . number_format($sellingPrice, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Custom Order BOM Calculation Error: ' . $e->getMessage(), [
                'order_detail_id' => $orderDetail->id,
                'trace'           => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan perhitungan harga: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi ulang total harga pada model Order induk.
     */
    private function recalculateOrderTotal(Order $order): void
    {
        // Hitung ulang jumlah subtotal dari semua item di dalam order tersebut
        $subtotal = $order->orderDetails()->sum('subtotal');

        // Total = Subtotal (Karena tidak ada pajak/biaya tambahan di level order)
        $order->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);
    }
}