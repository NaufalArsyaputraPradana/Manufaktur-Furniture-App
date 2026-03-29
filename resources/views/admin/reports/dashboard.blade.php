@extends('layouts.app')

@section('title', 'Dashboard - Reports & Analytics')

@section('content')
<div class="container-fluid py-2 px-3 md:py-4 md:px-4">
    <!-- Page Header - Mobile Optimized -->
    <div class="row mb-3 md:mb-4">
        <div class="col-12">
            <div class="d-flex flex-column gap-2 justify-content-md-between align-items-md-center sm:flex-row">
                <h1 class="h4 md:h3 mb-0 fw-bold">
                    <i class="bi bi-graph-up"></i> Reports & Analytics
                </h1>
                <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm md:btn align-self-start md:align-self-auto">
                    <i class="bi bi-plus-circle"></i> <span class="d-none sm:inline">Create Report</span><span class="d-sm-none">Create</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Date Range Filter - Mobile Optimized -->
    <div class="row mb-3 md:mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 md:p-4">
                    <div class="row g-2 md:g-3 align-items-end">
                        <!-- Start Date - Full width on mobile -->
                        <div class="col-12 sm:col-6 md:col-3">
                            <label for="startDate" class="form-label small md:normal">Start Date</label>
                            <input type="date" id="startDate" class="form-control form-control-sm md:form-control" 
                                   value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <!-- End Date - Full width on mobile -->
                        <div class="col-12 sm:col-6 md:col-3">
                            <label for="endDate" class="form-label small md:normal">End Date</label>
                            <input type="date" id="endDate" class="form-control form-control-sm md:form-control" 
                                   value="{{ date('Y-m-d') }}">
                        </div>
                        <!-- Report Type - Full width on mobile, half on tablet -->
                        <div class="col-12 md:col-3">
                            <label for="reportType" class="form-label small md:normal">Report Type</label>
                            <select id="reportType" class="form-select form-select-sm md:form-select">
                                <option value="all">All Reports</option>
                                <option value="sales">Sales</option>
                                <option value="production">Production</option>
                                <option value="inventory">Inventory</option>
                                <option value="financial">Financial</option>
                            </select>
                        </div>
                        <!-- Filter Button - Full width on mobile -->
                        <div class="col-12 md:col-3">
                            <button id="filterBtn" class="btn btn-primary btn-sm w-100 md:btn">
                                <i class="bi bi-funnel"></i> <span class="d-none sm:inline">Filter</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row - Stack on mobile, 2 cols on tablet, 4 cols on desktop -->
    <div class="row mb-3 md:mb-4" id="statsContainer">
        <div class="col-12 sm:col-6 lg:col-3 mb-2 md:mb-3">
            <x-charts.stat-card 
                title="Total Orders"
                value="0"
                id="stat-orders"
                color="blue"
            />
        </div>
        <div class="col-12 sm:col-6 lg:col-3 mb-2 md:mb-3">
            <x-charts.stat-card 
                title="Total Revenue"
                value="Rp 0"
                id="stat-revenue"
                color="green"
            />
        </div>
        <div class="col-12 sm:col-6 lg:col-3 mb-2 md:mb-3">
            <x-charts.stat-card 
                title="Avg Order Value"
                value="Rp 0"
                id="stat-average"
                color="orange"
            />
        </div>
        <div class="col-12 sm:col-6 lg:col-3 mb-2 md:mb-3">
            <x-charts.stat-card 
                title="Completed Orders"
                value="0"
                id="stat-completed"
                color="teal"
            />
        </div>
    </div>

    <!-- Charts Row - Stack on mobile, responsive on larger screens -->
    <div class="row mb-3 md:mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-12 lg:col-8 mb-3 md:mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-2 md:py-3">
                    <h6 class="mb-0 fw-bold text-truncate">Sales Trend</h6>
                </div>
                <div class="card-body p-2 md:p-4">
                    <div id="salesChartContainer">
                        <x-charts.line-chart 
                            id="salesChart"
                            :labels="[]"
                            :datasets="[]"
                            title="Daily Sales"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- Production Status Chart -->
        <div class="col-12 lg:col-4 mb-3 md:mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-2 md:py-3">
                    <h6 class="mb-0 fw-bold text-truncate">Production Status</h6>
                </div>
                <div class="card-body p-2 md:p-4">
                    <div id="productionChartContainer" style="max-height: 300px;">
                        <x-charts.pie-chart 
                            id="productionChart"
                            :labels="[]"
                            :data="[]"
                            title="Status Distribution"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts - Stack on mobile, side-by-side on desktop -->
    <div class="row mb-3 md:mb-4">
        <!-- Inventory Overview Chart -->
        <div class="col-12 lg:col-6 mb-3 md:mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-2 md:py-3">
                    <h6 class="mb-0 fw-bold text-truncate">Inventory Overview</h6>
                </div>
                <div class="card-body p-2 md:p-4">
                    <div id="inventoryChartContainer" style="max-height: 350px; overflow-x: auto;">
                        <x-charts.bar-chart 
                            id="inventoryChart"
                            :labels="[]"
                            :datasets="[]"
                            title="Top 10 Products by Stock"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- Financial Summary - Responsive Table -->
        <div class="col-12 lg:col-6 mb-3 md:mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-2 md:py-3">
                    <h6 class="mb-0 fw-bold">Financial Summary</h6>
                </div>
                <div class="card-body p-2 md:p-4">
                    <!-- Mobile-optimized Financial Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="py-2 text-start">
                                        <span class="font-weight-bold text-sm">Total Revenue</span>
                                    </td>
                                    <td class="py-2 text-end">
                                        <span class="text-success fw-bold" id="fin-revenue">Rp 0</span>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="py-2 text-start">
                                        <span class="font-weight-bold text-sm">Pending Payments</span>
                                    </td>
                                    <td class="py-2 text-end">
                                        <span class="text-warning fw-bold" id="fin-pending">Rp 0</span>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="py-2 text-start">
                                        <span class="font-weight-bold text-sm">Failed Payments</span>
                                    </td>
                                    <td class="py-2 text-end">
                                        <span class="text-danger fw-bold" id="fin-failed">0</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-start">
                                        <span class="font-weight-bold text-sm">Avg Payment</span>
                                    </td>
                                    <td class="py-2 text-end">
                                        <span class="fw-bold" id="fin-average">Rp 0</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Section - Mobile Optimized -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-2 md:py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold">Recent Reports</h6>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-2 md:p-4">
                    <!-- Responsive table wrapper for mobile -->
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="reportsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-sm">Name</th>
                                    <th class="text-sm d-none sm:table-cell">Type</th>
                                    <th class="text-sm d-none md:table-cell">Status</th>
                                    <th class="text-sm d-none lg:table-cell">Created By</th>
                                    <th class="text-sm d-none md:table-cell">Generated</th>
                                    <th class="text-sm text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        Loading reports...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiBaseUrl = '/api';
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const reportTypeSelect = document.getElementById('reportType');
    const filterBtn = document.getElementById('filterBtn');

    // Load initial data
    loadDashboardData();

    // Filter button click
    filterBtn.addEventListener('click', loadDashboardData);

    // Enter key in date inputs
    startDateInput.addEventListener('keypress', (e) => e.key === 'Enter' && loadDashboardData());
    endDateInput.addEventListener('keypress', (e) => e.key === 'Enter' && loadDashboardData());

    async function loadDashboardData() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const reportType = reportTypeSelect.value;

        try {
            // Load Sales Report
            if (reportType === 'all' || reportType === 'sales') {
                const salesResponse = await fetch(`${apiBaseUrl}/reports/sales?start_date=${startDate}&end_date=${endDate}`);
                const salesData = await salesResponse.json();
                
                if (salesData.success) {
                    updateSalesChart(salesData.data);
                    updateStatistics(salesData.stats);
                }
            }

            // Load Production Report
            if (reportType === 'all' || reportType === 'production') {
                const prodResponse = await fetch(`${apiBaseUrl}/reports/production?start_date=${startDate}&end_date=${endDate}`);
                const prodData = await prodResponse.json();
                
                if (prodData.success) {
                    updateProductionChart(prodData.data);
                }
            }

            // Load Inventory Report
            if (reportType === 'all' || reportType === 'inventory') {
                const invResponse = await fetch(`${apiBaseUrl}/reports/inventory`);
                const invData = await invResponse.json();
                
                if (invData.success) {
                    updateInventoryChart(invData.data);
                }
            }

            // Load Financial Report
            if (reportType === 'all' || reportType === 'financial') {
                const finResponse = await fetch(`${apiBaseUrl}/reports/financial?start_date=${startDate}&end_date=${endDate}`);
                const finData = await finResponse.json();
                
                if (finData.success) {
                    updateFinancialSummary(finData.stats);
                }
            }

            // Load Recent Reports
            await loadRecentReports();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            showErrorNotification('Failed to load dashboard data');
        }
    }

    function updateSalesChart(data) {
        const ctx = document.getElementById('salesChart');
        if (ctx && window.Chart) {
            const existingChart = Chart.helpers.instances.find(inst => inst.canvas === ctx);
            if (existingChart) existingChart.destroy();
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: data.datasets || []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }
    }

    function updateProductionChart(data) {
        const ctx = document.getElementById('productionChart');
        if (ctx && window.Chart) {
            const existingChart = Chart.helpers.instances.find(inst => inst.canvas === ctx);
            if (existingChart) existingChart.destroy();
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.data || [],
                        backgroundColor: ['#3b82f6', '#ef4444', '#10b981', '#f59e0b']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
    }

    function updateInventoryChart(data) {
        const ctx = document.getElementById('inventoryChart');
        if (ctx && window.Chart) {
            const existingChart = Chart.helpers.instances.find(inst => inst.canvas === ctx);
            if (existingChart) existingChart.destroy();
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels || [],
                    datasets: data.datasets || []
                },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }
    }

    function updateStatistics(stats) {
        if (stats) {
            const formatCurrency = (val) => 'Rp ' + (val || 0).toLocaleString('id-ID');
            
            document.getElementById('stat-orders').textContent = (stats.total_orders || 0).toLocaleString('id-ID');
            document.getElementById('stat-revenue').textContent = formatCurrency(stats.total_revenue);
            document.getElementById('stat-average').textContent = formatCurrency(stats.average_order_value);
            document.getElementById('stat-completed').textContent = (stats.completed_orders || 0).toLocaleString('id-ID');
        }
    }

    function updateFinancialSummary(stats) {
        const formatCurrency = (val) => 'Rp ' + (val || 0).toLocaleString('id-ID');
        
        if (stats) {
            document.getElementById('fin-revenue').textContent = formatCurrency(stats.total_revenue);
            document.getElementById('fin-pending').textContent = formatCurrency(stats.pending_payments);
            document.getElementById('fin-failed').textContent = (stats.failed_payments || 0).toLocaleString('id-ID');
            document.getElementById('fin-average').textContent = formatCurrency(stats.average_payment);
        }
    }

    async function loadRecentReports() {
        try {
            const response = await fetch(`${apiBaseUrl}/reports?limit=5`);
            const data = await response.json();
            
            if (data.success && data.data.data) {
                updateReportsTable(data.data.data);
            }
        } catch (error) {
            console.error('Error loading reports:', error);
        }
    }

    function updateReportsTable(reports) {
        const tbody = document.querySelector('#reportsTable tbody');
        
        if (!reports || reports.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No reports found</td></tr>';
            return;
        }

        tbody.innerHTML = reports.map(report => `
            <tr>
                <td><strong>${report.name}</strong></td>
                <td><span class="badge bg-info">${report.type}</span></td>
                <td>
                    ${getStatusBadge(report.status)}
                </td>
                <td>${report.user?.name || 'Unknown'}</td>
                <td>${report.generated_at ? new Date(report.generated_at).toLocaleDateString('id-ID') : '-'}</td>
                <td>
                    <a href="/admin/reports/${report.id}" class="btn btn-sm btn-primary">
                        View
                    </a>
                </td>
            </tr>
        `).join('');
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': 'secondary',
            'processing': 'info',
            'completed': 'success',
            'failed': 'danger'
        };
        const color = badges[status] || 'secondary';
        return `<span class="badge bg-${color}">${status}</span>`;
    }

    function showErrorNotification(message) {
        // Create simple alert - can be enhanced with better notification system
        console.error(message);
    }
});
</script>
@endpush
