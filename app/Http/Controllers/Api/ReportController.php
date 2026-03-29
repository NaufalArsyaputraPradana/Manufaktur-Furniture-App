<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\ProductionProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get sales report data
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->subDays(30));
        $endDate = $request->query('end_date', Carbon::now());

        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [
            'labels' => $sales->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $sales->pluck('count'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $sales->pluck('total'),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ]
            ]
        ];

        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'average_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])->avg('total_amount'),
            'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'type' => 'sales',
            'data' => $chartData,
            'stats' => $stats,
        ]);
    }

    /**
     * Get production report data
     */
    public function productionReport(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->subDays(30));
        $endDate = $request->query('end_date', Carbon::now());

        $production = ProductionProcess::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $chartData = [
            'labels' => $production->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s))),
            'data' => $production->pluck('count'),
        ];

        $stats = [
            'total_processes' => ProductionProcess::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => ProductionProcess::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'in_progress' => ProductionProcess::whereBetween('created_at', [$startDate, $endDate])->where('status', 'in_progress')->count(),
            'pending' => ProductionProcess::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'type' => 'production',
            'data' => $chartData,
            'stats' => $stats,
        ]);
    }

    /**
     * Get inventory report data
     */
    public function inventoryReport(Request $request)
    {
        $products = Product::select('name', 'stock_quantity', 'reorder_point')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $chartData = [
            'labels' => $products->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Current Stock',
                    'data' => $products->pluck('stock_quantity'),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Reorder Point',
                    'data' => $products->pluck('reorder_point'),
                    'backgroundColor' => '#ef4444',
                ]
            ]
        ];

        $stats = [
            'total_products' => Product::count(),
            'low_stock' => Product::whereColumn('stock_quantity', '<', 'reorder_point')->count(),
            'total_value' => Product::selectRaw('SUM(stock_quantity * price) as value')->value('value') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'type' => 'inventory',
            'data' => $chartData,
            'stats' => $stats,
        ]);
    }

    /**
     * Get financial report data
     */
    public function financialReport(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->subDays(30));
        $endDate = $request->query('end_date', Carbon::now());

        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $stats = [
            'total_revenue' => Payment::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'average_payment' => Payment::whereBetween('created_at', [$startDate, $endDate])->avg('amount'),
        ];

        return response()->json([
            'success' => true,
            'type' => 'financial',
            'data' => $payments->groupBy('status'),
            'stats' => $stats,
        ]);
    }

    /**
     * Index - list all reports
     */
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Store - create new report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sales,production,inventory,financial,customer,custom',
            'description' => 'nullable|string',
            'filters' => 'nullable|array',
            'is_scheduled' => 'boolean',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly',
        ]);

        $report = Report::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Report created successfully',
            'data' => $report,
        ], 201);
    }

    /**
     * Show - get single report
     */
    public function show(Report $report)
    {
        if ($report->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Update report
     */
    public function update(Request $request, Report $report)
    {
        if ($report->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'filters' => 'nullable|array',
            'is_scheduled' => 'boolean',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly',
        ]);

        $report->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Report updated successfully',
            'data' => $report,
        ]);
    }

    /**
     * Destroy - delete report
     */
    public function destroy(Report $report)
    {
        if ($report->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully',
        ]);
    }

    /**
     * Export report
     */
    public function export(Report $report, Request $request)
    {
        if ($report->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $format = $request->query('format', 'pdf');

        // TODO: Implement export logic for PDF, Excel, CSV
        // For now, return the report data

        return response()->json([
            'success' => true,
            'message' => 'Export prepared',
            'data' => $report,
            'format' => $format,
        ]);
    }
}
