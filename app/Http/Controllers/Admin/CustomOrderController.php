<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomOrderController extends Controller
{
    /**
     * Single source of truth untuk harga kayu per grade.
     */
    private function getWoodPrices(): array
    {
        return [
            'A' => 50000000, // Rp 50jt/m3
            'B' => 30000000, // Rp 30jt/m3
            'C' => 12000000, // Rp 12jt/m3
        ];
    }

    /**
     * Menampilkan daftar item order yang bersifat custom.
     */
    public function index(Request $request): View
    {
        $currentTab = $request->query('tab', 'orders');

        // Query berdasarkan tab
        $query = OrderDetail::with(['order.user'])
            ->where('is_custom', true);

        // Filter berdasarkan order_id jika ada
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Statistik total (sebelum filter tab)
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

        // Filter berdasarkan tab
        if ($currentTab === 'history') {
            // Tab Riwayat: hanya yang sudah dihitung
            $query->where('unit_price', '>', 0);
        } else {
            // Tab Orders (Default): hanya yang belum dihitung
            $query->where('unit_price', 0);
        }

        $customOrders = $query->latest()->paginate(10)->withQueryString();

        return view('admin.custom_orders.index', compact(
            'customOrders',
            'currentTab',
            'totalCount',
            'pendingCount',
            'processedCount'
        ));
    }

    /**
     * Tampilkan form kalkulator harga (BOM) atau tab riwayat perhitungan (?tab=history).
     */
    public function calculate(Request $request, OrderDetail $orderDetail): View|RedirectResponse
    {
        if (! $orderDetail->is_custom) {
            return back()->with('error', 'Item ini bukan merupakan produk custom.');
        }

        if ($orderDetail->order && $orderDetail->order->status === 'cancelled') {
            return back()->with('error', 'Tidak dapat menghitung harga untuk pesanan yang sudah dibatalkan.');
        }

        $orderDetail->load('order.user');
        $specs = $orderDetail->custom_specifications ?? [];
        $existingBom = $specs['bom'] ?? null;

        $tab = $request->query('tab', 'calculate');
        if (! in_array($tab, ['calculate', 'history'], true)) {
            $tab = 'calculate';
        }

        $bomHistoryRaw = $specs['bom_history'] ?? [];
        $bomHistory = collect($bomHistoryRaw)
            ->sortByDesc(fn ($entry) => $entry['archived_at'] ?? '')
            ->values()
            ->all();

        $historyUserNames = [];
        foreach ($bomHistory as $entry) {
            $uid = $entry['archived_by'] ?? null;
            if ($uid && ! isset($historyUserNames[$uid])) {
                $historyUserNames[$uid] = User::query()->whereKey($uid)->value('name') ?? '—';
            }
        }

        $woodPrices = $this->getWoodPrices();

        return view('admin.custom_orders.calculate', compact(
            'orderDetail',
            'existingBom',
            'tab',
            'bomHistory',
            'historyUserNames',
            'woodPrices'
        ));
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
            $woodPrices = $this->getWoodPrices();

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
            $previousBom = $specs['bom'] ?? null;
            if ($previousBom) {
                $history = $specs['bom_history'] ?? [];
                $history[] = [
                    'bom_data'            => $previousBom,
                    'unit_price_snapshot' => (float) $orderDetail->unit_price,
                    'subtotal_snapshot'   => (float) $orderDetail->subtotal,
                    'archived_at'         => now()->toIso8601String(),
                    'archived_by'         => auth()->id(),
                ];
                $specs['bom_history'] = array_slice($history, -50);
            }
            $specs['bom'] = $bomData;

            $orderDetail->update([
                'unit_price'           => (float) $sellingPrice,
                'subtotal'             => (float) ($sellingPrice * $orderDetail->quantity),
                'custom_specifications' => $specs,
            ]);

            // 7. Sinkronisasi Total Harga di Order Induk
            $this->recalculateOrderTotal($orderDetail->order);

            DB::commit();

            return redirect()
                ->route('admin.custom-orders.index', ['tab' => 'history'])
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
        $subtotal = (float) $order->orderDetails()->sum('subtotal');

        // Total = Subtotal (Karena tidak ada pajak/biaya tambahan di level order)
        $order->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);
    }
}