@extends('layouts.admin')

@section('title', 'Laporan Produksi')

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
                                <i class="bi bi-gear-wide-connected fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Laporan & Analitik</h2>
                                <p class="mb-0 opacity-90 small">Monitoring efisiensi, status, dan output produksi pabrik.
                                </p>
                            </div>
                        </div>
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
                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-funnel me-1"></i>Filter Periode Produksi</h6>
                <form method="GET" action="{{ route('admin.reports.production') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <x-form-input
                            name="start_date"
                            label="Dari Tanggal"
                            type="date"
                            :value="$startDate"
                            class="border-primary-subtle"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input
                            name="end_date"
                            label="Sampai Tanggal"
                            type="date"
                            :value="$endDate"
                            class="border-primary-subtle"
                        />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="bi bi-search me-1"></i> Tampilkan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-primary text-white stats-card">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-white-50 mb-2">Total Proses</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($totalProcesses) }}</h2>
                        <small class="text-white-50">Siklus Kerja</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-success text-white stats-card">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-white-50 mb-2">Selesai Realisasi</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($completed) }}</h2>
                        <small class="text-white-50">Target Tercapai</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-info text-white stats-card">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-white-50 mb-2">Sedang Berjalan</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($inProgress) }}</h2>
                        <small class="text-white-50">Dalam Antrean</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-dark text-white stats-card">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-white-50 mb-2">Efisiensi Output</h6>
                        <h2 class="fw-bold mb-0">{{ $efficiency }}%</h2>
                        <small class="text-white-50">Rasio Penyelesaian</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-4">
            <!-- Chart Status -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribusi
                            Status Produksi</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div style="height: 250px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Process Table -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Log Proses
                            Terbaru</h6>
                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>CSV</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 modern-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-3 text-secondary small text-uppercase fw-bold">Kode Prod.</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Order</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Item Utama</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold text-center">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($processes as $process)
                                        <tr>
                                            <td class="ps-3 fw-bold text-dark">
                                                {{ $process->production_code ?? 'PRD-' . $process->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $process->order_id) }}"
                                                    class="text-decoration-none fw-semibold">
                                                    #{{ $process->order?->order_number ?? '-' }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 180px;"
                                                    title="{{ $process->order?->orderDetails?->first()?->product_name ?? 'Custom Item' }}">
                                                    {{ $process->order?->orderDetails?->first()?->product_name ?? 'Custom Item' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($process->status == 'completed')
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success px-3">Selesai</span>
                                                @elseif($process->status == 'in_progress')
                                                    <span
                                                        class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3">Proses</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 text-dark">Antre</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                                                Belum ada data produksi untuk periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('statusChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Antre (Pending)', 'Dalam Proses', 'Selesai'],
                        datasets: [{
                            data: @json($chartData),
                            backgroundColor: [
                                '#f6c23e', // Warning (Pending)
                                '#4e73df', // Primary (In Progress)
                                '#1cc88a' // Success (Completed)
                            ],
                            hoverOffset: 10,
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
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) *
                                            100) : 0;
                                        return ` ${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
