@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

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
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
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
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Laporan & Analitik</h2>
                                <p class="mb-0 opacity-90 small">Pusat data keuangan, penjualan, dan efisiensi produksi.</p>
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

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body py-3">
                @php
                    $monthOptions = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $monthOptions[$m] = DateTime::createFromFormat('!m', $m)->format('F');
                    }
                    $yearOptions = [];
                    for ($y = date('Y'); $y >= 2020; $y--) {
                        $yearOptions[$y] = (string) $y;
                    }
                @endphp
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <x-form-input
                            name="month"
                            type="select"
                            label="Periode Bulan"
                            :options="$monthOptions"
                            :value="$month"
                            class="border-primary-subtle"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input
                            name="year"
                            type="select"
                            label="Tahun"
                            :options="$yearOptions"
                            :value="$year"
                            class="border-primary-subtle"
                        />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="bi bi-filter me-1"></i>Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 h-100 shadow-sm border-start border-primary border-4 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Total Transaksi</p>
                                <h3 class="mb-0 fw-bold text-primary">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}
                                </h3>
                                <small class="text-muted fw-semibold">{{ $jumlahPesanan }} Pesanan Diproses</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 h-100 shadow-sm border-start border-success border-4 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Pembayaran Sukses</p>
                                <h3 class="mb-0 fw-bold text-success">{{ $pembayaranSukses }}</h3>
                                <small class="text-muted fw-semibold">Dana Terverifikasi</small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="bi bi-check-circle-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 h-100 shadow-sm border-start border-warning border-4 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Belum Dibayar</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ $belumDibayar }}</h3>
                                <small class="text-muted fw-semibold">Pesanan Tertunda</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 h-100 shadow-sm border-start border-danger border-4 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Gagal/Batal</p>
                                <h3 class="mb-0 fw-bold text-danger">{{ $pembayaranGagal }}</h3>
                                <small class="text-muted fw-semibold">Transaksi Bermasalah</small>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                                <i class="bi bi-x-circle-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-line me-2"></i>Tren Pendapatan Tahun
                    {{ $year }}</h6>
            </div>
            <div class="card-body">
                <canvas id="chartPendapatan" style="max-height: 350px; width: 100%;"></canvas>
            </div>
        </div>

        <!-- Table -->
        @php
            $selectedMonth = DateTime::createFromFormat('!m', $month)->format('F');
        @endphp
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Detail Pesanan Periode
                    {{ $selectedMonth }} {{ $year }}</h6>
                <button class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Ekspor CSV</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No. Order</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Buat</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $order->order_number }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $order->user?->name ?? 'Guest' }}</div>
                                    <small class="text-muted">{{ $order->user?->email ?? '-' }}</small>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} border border-{{ $order->status_color }} rounded-pill px-3 py-2">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary rounded-circle" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    Tidak ada data pesanan untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top bg-light">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartPendapatan');
            if (ctx) {
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];
                const chartDataObj = @json($chartData);
                
                // Convert object to sequential array
                const chartData = [];
                for (let i = 1; i <= 12; i++) {
                    chartData.push(chartDataObj[i] || 0);
                }

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthNames,
                        datasets: [{
                            label: 'Pendapatan',
                            data: chartData,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#4e73df',
                            pointHoverBackgroundColor: '#4e73df',
                            pointHoverBorderColor: '#fff',
                            pointRadius: 4,
                            pointHoverRadius: 6
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
                                    label: function(context) {
                                        let val = context.parsed.y;
                                        return ' Pendapatan: Rp ' + new Intl.NumberFormat('id-ID')
                                            .format(val);
                                    }
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
                                    callback: function(value) {
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
        });
    </script>
@endpush
