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
            $revenueCurrentMonth = Order::where('status', 'completed')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total');

            $revenueLastMonth = Order::where('status', 'completed')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('total');

            $growthPercentage = 0;
            if ($revenueLastMonth > 0) {
                $growthPercentage = (($revenueCurrentMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
            } elseif ($revenueCurrentMonth > 0) {
                $growthPercentage = 100;
            }

            return [
                'total_orders'    => Order::count(),
                'pending_orders'  => Order::where('status', 'pending')->count(),
                'process_orders'  => Order::whereIn('status', ['confirmed', 'in_production'])->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'total_products'  => Product::count(),
                'total_categories' => Category::count(),
                'total_revenue'   => Order::where('status', 'completed')->sum('total'),
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

        $recentOrders = Order::with(['user', 'orderDetails.product'])
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