<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionProcess;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Laporan Utama (Keuangan Bulanan)
     */
    public function index(Request $request): View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $ordersQuery = Order::whereMonth('created_at', $month)->whereYear('created_at', $year);

        $jumlahPesanan = (clone $ordersQuery)->count();
        $totalTransaksi = (clone $ordersQuery)->sum('total');

        $pembayaranSukses = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'paid'))->count();
        $pembayaranGagal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'failed'))->count();
        $belumDibayar = $jumlahPesanan - $pembayaranSukses - $pembayaranGagal;

        $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('SUM(total) as total')
        )
            ->whereYear('created_at', $year)
            ->where('status', '!=', 'cancelled')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $monthlyRevenue[$i] ?? 0;
        }

        $orders = Order::with(['user', 'payment'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'orders',
            'month',
            'year',
            'totalTransaksi',
            'jumlahPesanan',
            'pembayaranSukses',
            'pembayaranGagal',
            'belumDibayar',
            'chartData'
        ));
    }

    /**
     * Laporan Penjualan (Sales)
     */
    public function sales(Request $request): View|RedirectResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalOrders = (clone $query)->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();
        $cancelledOrders = (clone $query)->where('status', 'cancelled')->count();
        $totalRevenue = (clone $query)->where('status', '!=', 'cancelled')->sum('total');
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topProducts = OrderDetail::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topProductLabels = $topProducts->pluck('product_name');
        $topProductData = $topProducts->pluck('total_qty');

        $dailySales = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $salesChartLabels = [];
        $salesChartData = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateStr = $date->format('Y-m-d');
            $salesChartLabels[] = $date->format('d M');
            $salesChartData[] = $dailySales[$dateStr] ?? 0;
        }

        if ($request->has('generate')) {
            $this->saveReport('sales', 'Laporan Penjualan', $startDate, $endDate, [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
            ]);
            return back()->with('success', 'Laporan hasil komputasi berhasil disimpan ke arsip.');
        }

        $sales = (clone $query)->with(['user', 'orderDetails'])->latest()->get();

        return view('admin.reports.sales', compact(
            'startDate',
            'endDate',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'cancelledOrders',
            'totalRevenue',
            'averageOrder',
            'sales',
            'topProductLabels',
            'topProductData',
            'salesChartLabels',
            'salesChartData'
        ));
    }

    /**
     * Laporan Produksi
     */
    public function production(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = ProductionProcess::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalProcesses = (clone $query)->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $efficiency = $totalProcesses > 0 ? round(($completed / $totalProcesses) * 100, 1) : 0;

        $processes = (clone $query)->with(['order.orderDetails.product'])->latest()->limit(50)->get();
        $chartLabels = ['Pending', 'In Progress', 'Completed'];
        $chartData = [$pending, $inProgress, $completed];

        return view('admin.reports.production', compact(
            'startDate',
            'endDate',
            'totalProcesses',
            'completed',
            'inProgress',
            'pending',
            'efficiency',
            'processes',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * Laporan Inventori
     */
    public function inventory(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $productSummary = OrderDetail::select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->get();

        return view('admin.reports.inventory', compact('startDate', 'endDate', 'productSummary'));
    }

    /**
     * Laporan Profitabilitas
     */
    public function profitability(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled');

        $totalRevenue = (clone $query)->sum('total');
        $orderCount = (clone $query)->count();
        $averageOrderValue = $orderCount > 0 ? round($totalRevenue / $orderCount, 2) : 0;

        $orders = (clone $query)->with(['user', 'orderDetails'])->latest()->limit(100)->get();

        return view('admin.reports.profitability', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'orderCount',
            'averageOrderValue',
            'orders'
        ));
    }

    /**
     * Export laporan (CSV) berdasarkan tipe
     */
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'report_type' => 'required|in:sales,production,inventory,profitability',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $type = $request->report_type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $filename = "laporan-{$type}-" . now()->format('YmdHis') . ".csv";

        return new StreamedResponse(function () use ($type, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Laporan: ' . ucfirst($type), "Periode: {$startDate} s/d {$endDate}"]);
            fputcsv($handle, []);

            switch ($type) {
                case 'sales':
                case 'profitability':
                    fputcsv($handle, ['Nomor Order', 'Tanggal', 'Customer', 'Total (Rp)', 'Status']);
                    $orders = Order::with('user')
                        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('status', '!=', 'cancelled')
                        ->latest()->get();
                    foreach ($orders as $o) {
                        fputcsv($handle, [$o->order_number, $o->created_at->format('Y-m-d H:i'), $o->user->name ?? '-', $o->total, $o->status]);
                    }
                    break;

                case 'production':
                    fputcsv($handle, ['ID Proses', 'ID Order', 'Status Produksi', 'Waktu Dibuat']);
                    $processes = ProductionProcess::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->latest()->get();
                    foreach ($processes as $p) {
                        fputcsv($handle, [$p->id, $p->order_id, $p->status, $p->created_at->format('Y-m-d H:i')]);
                    }
                    break;

                case 'inventory':
                    fputcsv($handle, ['Nama Produk', 'Total Unit Terjual']);
                    $summary = OrderDetail::select('product_name')
                        ->selectRaw('SUM(quantity) as total_sold')
                        ->whereHas('order', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                ->where('status', '!=', 'cancelled');
                        })
                        ->groupBy('product_name')
                        ->orderByDesc('total_sold')
                        ->get();
                    foreach ($summary as $row) {
                        fputcsv($handle, [$row->product_name ?: 'Produk Tidak Diketahui', $row->total_sold]);
                    }
                    break;
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Display dashboard with real-time data
     */
    public function dashboard(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Statistics
        $totalOrders = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total');
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Sales trend data
        $dailySales = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $salesTrendLabels = [];
        $salesTrendData = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateStr = $date->format('Y-m-d');
            $salesTrendLabels[] = $date->format('M d');
            $salesTrendData[] = $dailySales[$dateStr] ?? 0;
        }

        // Production status
        $productionQuery = ProductionProcess::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $productionPending = (clone $productionQuery)->where('status', 'pending')->count();
        $productionInProgress = (clone $productionQuery)->where('status', 'in_progress')->count();
        $productionCompleted = (clone $productionQuery)->where('status', 'completed')->count();

        // Top products inventory
        $topProducts = OrderDetail::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Financial summary
        $totalPending = (clone $query)->whereHas('payment', fn($q) => $q->where('payment_status', 'pending'))->sum('total');
        $totalFailed = (clone $query)->whereHas('payment', fn($q) => $q->where('payment_status', 'failed'))->sum('total');
        $totalSuccess = (clone $query)->whereHas('payment', fn($q) => $q->where('payment_status', 'paid'))->sum('total');

        // Recent reports
        $recentReports = Report::latest()->limit(5)->get();

        return view('admin.reports.dashboard', compact(
            'startDate',
            'endDate',
            'totalOrders',
            'totalRevenue',
            'completedOrders',
            'averageOrderValue',
            'salesTrendLabels',
            'salesTrendData',
            'productionPending',
            'productionInProgress',
            'productionCompleted',
            'topProducts',
            'totalPending',
            'totalFailed',
            'totalSuccess',
            'recentReports'
        ));
    }

    /**
     * List all reports
     */
    public function listReports(Request $request): View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Get reports with filters
        $query = Report::query();

        if ($request->has('type') && $request->type) {
            $query->where('report_type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // For backward compatibility, use 'orders' key
        $orders = $query->latest()->paginate(15)->withQueryString();
        
        // Get financial data for the selected month/year
        $ordersQuery = Order::whereMonth('created_at', $month)->whereYear('created_at', $year);

        $jumlahPesanan = (clone $ordersQuery)->count();
        $totalTransaksi = (clone $ordersQuery)->sum('total');

        $pembayaranSukses = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'paid'))->count();
        $pembayaranGagal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'failed'))->count();
        $belumDibayar = $jumlahPesanan - $pembayaranSukses - $pembayaranGagal;

        // Get monthly revenue data
        $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('SUM(total) as total')
        )
            ->whereYear('created_at', $year)
            ->where('status', '!=', 'cancelled')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $monthlyRevenue[$i] ?? 0;
        }

        return view('admin.reports.index', compact(
            'orders',
            'month',
            'year',
            'totalTransaksi',
            'jumlahPesanan',
            'pembayaranSukses',
            'pembayaranGagal',
            'belumDibayar',
            'chartData'
        ));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('admin.reports.create');
    }

    /**
     * Store new report
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|in:sales,production,inventory,financial,custom',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'filters' => 'nullable|array',
        ]);

        $validated['generated_by'] = auth()->id();
        $validated['status'] = 'completed';

        Report::create($validated);

        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Show single report
     */
    public function show(Report $report): View
    {
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Show edit form
     */
    public function edit(Report $report): View
    {
        return view('admin.reports.edit', compact('report'));
    }

    /**
     * Update report
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'report_type' => 'required|in:sales,production,inventory,financial,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'filters' => 'nullable|array',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Delete report
     */
    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Export report with multiple formats
     */
    public function exportReport(Request $request, Report $report): StreamedResponse|RedirectResponse
    {
        $format = $request->get('format', 'csv');

        if (!in_array($format, ['csv', 'pdf', 'xlsx'])) {
            return back()->with('error', 'Format export tidak didukung.');
        }

        switch ($format) {
            case 'pdf':
                return $this->exportPDF($report);
            case 'xlsx':
                return $this->exportExcel($report);
            default:
                return $this->exportCSV($report);
        }
    }

    /**
     * Export report as CSV
     */
    private function exportCSV(Report $report): StreamedResponse
    {
        $filename = "report-{$report->report_type}-" . now()->format('YmdHis') . ".csv";

        return new StreamedResponse(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [$report->title]);
            fputcsv($handle, ["Periode: {$report->start_date} s/d {$report->end_date}"]);
            fputcsv($handle, ["Tipe: " . ucfirst($report->report_type)]);
            fputcsv($handle, ["Dibuat: " . $report->generated_at->format('Y-m-d H:i')]);
            fputcsv($handle, []);

            if ($report->data) {
                fputcsv($handle, array_keys((array)$report->data));
                fputcsv($handle, array_values((array)$report->data));
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export report as PDF using dompdf
     */
    private function exportPDF(Report $report)
    {
        $filename = "report-{$report->report_type}-" . now()->format('YmdHis') . ".pdf";
        
        $html = view('admin.reports.exports.pdf', [
            'report' => $report,
        ])->render();

        return \PDF::loadHTML($html)->download($filename);
    }

    /**
     * Export report as Excel (placeholder for future library integration)
     */
    private function exportExcel(Report $report): StreamedResponse
    {
        // TODO: Implement with maatwebsite/excel library
        // For now, return CSV as alternative
        return $this->exportCSV($report);
    }

    /**
     * Helper simpan laporan ke arsip database
     */
    private function saveReport(string $type, string $title, string $start, string $end, array $data): void
    {
        Report::create([
            'report_type' => $type,
            'title' => $title,
            'start_date' => $start,
            'end_date' => $end,
            'data' => $data,
            'generated_by' => auth()->id(),
        ]);
    }
}