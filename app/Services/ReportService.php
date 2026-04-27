<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionProcess;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service untuk menangani logika bisnis Report
 */
class ReportService
{
    /**
     * Get laporan bulanan (keuangan)
     */
    public function getMonthlyReport(int $month, int $year): array
    {
        $ordersQuery = Order::whereMonth('created_at', $month)->whereYear('created_at', $year);

        $jumlahPesanan = (clone $ordersQuery)->count();
        $totalTransaksi = (clone $ordersQuery)->sum('total');

        $pembayaranSukses = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'paid'))->count();
        $pembayaranGagal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('payment_status', 'failed'))->count();
        $belumDibayar = $jumlahPesanan - $pembayaranSukses - $pembayaranGagal;

        // Get monthly revenue for chart
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

        return [
            'orders' => $orders,
            'jumlahPesanan' => $jumlahPesanan,
            'totalTransaksi' => $totalTransaksi,
            'pembayaranSukses' => $pembayaranSukses,
            'pembayaranGagal' => $pembayaranGagal,
            'belumDibayar' => $belumDibayar,
            'chartData' => $chartData,
        ];
    }

    /**
     * Get sales report
     */
    public function getSalesReport(string $startDate, string $endDate, ?int $categoryId = null, int $perPage = 20)
    {
        $query = OrderDetail::with(['product.category', 'order'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($categoryId) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        $details = $query->latest()->paginate($perPage)->withQueryString();

        // Summary statistics
        $totalItems = OrderDetail::whereBetween('created_at', [$startDate, $endDate])
            ->sum('quantity');

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // Order statistics
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();

        // Get all orders for sales table
        $sales = Order::with(['user', 'payment', 'orderDetails'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Top products by quantity
        $topProducts = OrderDetail::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($categoryId, fn($q) => $q->whereHas('product', fn($p) => $p->where('category_id', $categoryId)))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Sales trend data (daily revenue)
        $salesTrend = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesChartLabels = $salesTrend->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'))->toArray();
        $salesChartData = $salesTrend->pluck('total')->toArray();

        $topProductLabels = $topProducts->pluck('product_name')->toArray();
        $topProductData = $topProducts->pluck('total_qty')->toArray();

        return [
            'details' => $details,
            'sales' => $sales,
            'totalItems' => $totalItems,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'topProducts' => $topProducts,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'topProductLabels' => $topProductLabels,
            'topProductData' => $topProductData,
        ];
    }

    /**
     * Get production report
     */
    public function getProductionReport(string $startDate, string $endDate, ?string $status = null, int $perPage = 20)
    {
        $query = ProductionProcess::with(['orderDetail.product', 'orderDetail.order', 'logs'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        $processes = $query->latest()->paginate($perPage)->withQueryString();

        // Summary statistics
        $totalProcesses = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $completedProcesses = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        
        $inProgressProcesses = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $avgProgressPercentage = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])
            ->average('progress_percentage');

        // Calculate efficiency (completion rate)
        $efficiency = $totalProcesses > 0 ? round(($completedProcesses / $totalProcesses) * 100) : 0;

        // Get status distribution for chart
        $pendingProcesses = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->count();

        $chartData = [
            $pendingProcesses,      // Pending
            $inProgressProcesses,   // In Progress
            $completedProcesses     // Completed
        ];

        return [
            'processes' => $processes,
            'totalProcesses' => $totalProcesses,
            'completed' => $completedProcesses,
            'completedProcesses' => $completedProcesses,
            'inProgress' => $inProgressProcesses,
            'inProgressProcesses' => $inProgressProcesses,
            'avgProgressPercentage' => round($avgProgressPercentage, 2),
            'efficiency' => $efficiency,
            'chartData' => $chartData,
        ];
    }

    /**
     * Get customer report
     */
    public function getCustomerReport(string $startDate, string $endDate, int $perPage = 20)
    {
        $customers = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('AVG(orders.total) as avg_order_value'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->orderByDesc('total_spent')
            ->paginate($perPage)
            ->withQueryString();

        $totalCustomers = $customers->total();
        $totalSpent = collect($customers->items())->sum('total_spent');

        return [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'totalSpent' => $totalSpent,
        ];
    }

    /**
     * Get detailed order report for PDF
     */
    public function getOrdersForExport(string $startDate, string $endDate)
    {
        return Order::with(['user', 'orderDetails', 'payment'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
    }
}
