<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan statistik lengkap.
     */
    public function index(): View
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonth = $now->copy()->subMonth();

        $stats = Cache::remember('dashboard.stats.main', 300, function () use ($currentMonth, $currentYear, $lastMonth) {
            // Consolidate multiple count and sum queries into single database query
            $orderStats = Order::select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders'),
                DB::raw('SUM(CASE WHEN status IN ("confirmed", "in_production") THEN 1 ELSE 0 END) as process_orders'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_revenue'),
                DB::raw('SUM(CASE WHEN status = "completed" AND MONTH(created_at) = ' . $currentMonth . ' AND YEAR(created_at) = ' . $currentYear . ' THEN total ELSE 0 END) as revenue_current_month'),
                DB::raw('SUM(CASE WHEN status = "completed" AND MONTH(created_at) = ' . $lastMonth->month . ' AND YEAR(created_at) = ' . $lastMonth->year . ' THEN total ELSE 0 END) as revenue_last_month')
            )->first();

            $revenueCurrentMonth = $orderStats->revenue_current_month ?? 0;
            $revenueLastMonth = $orderStats->revenue_last_month ?? 0;

            $growthPercentage = 0;
            if ($revenueLastMonth > 0) {
                $growthPercentage = (($revenueCurrentMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
            } elseif ($revenueCurrentMonth > 0) {
                $growthPercentage = 100;
            }

            return [
                'total_orders'    => $orderStats->total_orders ?? 0,
                'pending_orders'  => $orderStats->pending_orders ?? 0,
                'process_orders'  => $orderStats->process_orders ?? 0,
                'completed_orders' => $orderStats->completed_orders ?? 0,
                'total_products'  => Product::count(),
                'total_categories' => Category::count(),
                'total_revenue'   => $orderStats->total_revenue ?? 0,
                'revenue_month'   => $revenueCurrentMonth,
                'revenue_growth'  => round($growthPercentage, 1),
                'total_customers' => User::whereHas('role', fn($q) => $q->where('name', 'customer'))->count(),
            ];
        });

        $chartData = Cache::remember('dashboard.chart.revenue', 1800, function () use ($currentYear) {
            $revenueData = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
                ->where('status', 'completed')
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $labels = [];
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('M');
                $data[]   = $revenueData[$i] ?? 0;
            }

            return ['labels' => $labels, 'data' => $data];
        });

        $recentOrders = Order::with(['user:id,name,email', 'orderDetails:id,order_id,product_name,quantity', 'payment:id,order_id,payment_status'])
            ->latest()
            ->limit(5)
            ->get();

        $topProducts = Cache::remember('dashboard.top.products', 3600, function () {
            return DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(order_details.quantity) as total_sold'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        });

        return view('admin.dashboard', compact('stats', 'chartData', 'recentOrders', 'topProducts'));
    }
}