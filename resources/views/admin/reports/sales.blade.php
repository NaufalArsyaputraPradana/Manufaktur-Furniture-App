@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
        }

        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .modern-table tbody tr {
            transition: background-color 0.2s;
        }

        .modern-table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-graph-up-arrow fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Laporan & Analitik</h2>
                                <p class="mb-0 opacity-90 small">Analisis performa penjualan, tren harian, dan produk
                                    terpopuler.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button class="btn btn-light shadow-sm text-primary fw-bold" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.index') }}">
                            <i class="bi bi-wallet2 me-2"></i>Laporan Keuangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.sales') }}">
                            <i class="bi bi-graph-up-arrow me-2"></i>Laporan Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.production') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.production') }}">
                            <i class="bi bi-gear-wide-connected me-2"></i>Laporan Produksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.inventory') }}">
                            <i class="bi bi-box-seam me-2"></i>Inventori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.profitability') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.profitability') }}">
                            <i class="bi bi-pie-chart me-2"></i>Profitabilitas
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filter -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-funnel me-1"></i>Filter Periode Penjualan</h6>
                <form method="GET" action="{{ route('admin.reports.sales') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control border-primary-subtle"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control border-primary-subtle"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-search me-1"></i> Tampilkan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10 text-primary stats-card">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted mb-2">Total Pendapatan</h6>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10 text-info stats-card">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted mb-2">Volume Pesanan</h6>
                        <h4 class="fw-bold mb-0">{{ number_format($totalOrders) }} Unit</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10 text-success stats-card">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted mb-2">Selesai (Completed)</h6>
                        <h4 class="fw-bold mb-0">{{ number_format($completedOrders) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10 text-danger stats-card">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted mb-2">Dibatalkan (Cancelled)</h6>
                        <h4 class="fw-bold mb-0">{{ number_format($cancelledOrders) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4 g-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-activity me-2 text-primary"></i>Tren Penjualan
                            Harian</h6>
                        <span class="badge bg-light text-dark border small">Periode Aktif</span>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Produk
                            Terlaris</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div style="height: 300px;">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Transaction Table -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Rincian Transaksi
                    Penjualan</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Ekspor CSV</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 modern-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold">No. Order</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Tanggal</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Customer</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold text-center">Items</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Total Nilai</th>
                            <th class="pe-4 py-3 text-secondary small text-uppercase fw-bold text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $sale->order_number }}</td>
                                <td class="small text-dark">{{ $sale->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $sale->user?->name ?? 'Guest' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $sale->orderDetails->count() }}
                                        unit</span>
                                </td>
                                <td class="fw-bold text-dark">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">
                                    <span
                                        class="badge bg-{{ $sale->status_color }} bg-opacity-10 text-{{ $sale->status_color }} border border-{{ $sale->status_color }} rounded-pill px-3">
                                        {{ $sale->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    Tidak ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Standard Currency Formatter
            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            // 1. Sales Trend Chart (Line)
            const salesCtx = document.getElementById('salesTrendChart');
            if (salesCtx) {
                new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: @json($salesChartLabels),
                        datasets: [{
                            label: 'Pendapatan',
                            data: @json($salesChartData),
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.08)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                padding: 12,
                                callbacks: {
                                    label: (context) => ' ' + currencyFormatter.format(context.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: (value) => {
                                        if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Top Products Chart (Doughnut)
            const productCtx = document.getElementById('topProductsChart');
            if (productCtx) {
                new Chart(productCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($topProductLabels),
                        datasets: [{
                            data: @json($topProductData),
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                                '#e74a3b'
                            ],
                            hoverOffset: 15,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => ` ${context.label}: ${context.parsed} unit`
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
