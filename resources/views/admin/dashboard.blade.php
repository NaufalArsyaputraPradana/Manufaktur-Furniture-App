@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; --text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); }
        .text-shadow { text-shadow: var(--text-shadow); }
        .ls-1 { letter-spacing: 1px; }
        .backdrop-blur { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        .hover-scale { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .hover-scale:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important; }
        .avatar-circle { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; }
        .hover-bg-primary:hover { background-color: var(--admin-primary) !important; color: white !important; }
        .hover-bg-info:hover { background-color: #36b9cc !important; color: white !important; }
        .hover-bg-dark:hover { background-color: #5a5c69 !important; color: white !important; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!-- 1. WELCOME CARD -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden position-relative"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 end-0 opacity-10"
                style="font-size: 12rem; margin-top: -3rem; margin-right: -2rem;">
                <i class="bi bi-speedometer2 text-white"></i>
            </div>

            <div class="card-body text-white position-relative p-4" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-white bg-opacity-25 p-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 60px; height: 60px;">
                                <i class="bi bi-emoji-smile fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-shadow">Halo, {{ auth()->user()->name }}! 👋</h3>
                                <p class="mb-0 text-white text-opacity-75">Selamat datang kembali di panel administrasi sistem.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-inline-flex flex-column gap-1 text-start text-lg-end bg-white bg-opacity-10 p-3 rounded-3 backdrop-blur border border-white border-opacity-10">
                            <div class="d-flex align-items-center justify-content-lg-end gap-2">
                                <i class="bi bi-calendar-event"></i>
                                <span class="fw-semibold small">{{ now()->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-lg-end gap-2">
                                <i class="bi bi-clock"></i>
                                <span id="realtimeClock" class="fw-bold font-monospace">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. STATISTICS CARDS ROW 1 -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body p-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 d-inline-block mb-3">
                            <i class="bi bi-cart-fill fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 count-up">{{ $stats['total_orders'] ?? 0 }}</h2>
                        <p class="text-muted small mb-0 text-uppercase fw-bold ls-1">Total Pesanan</p>
                    </div>
                    <div class="progress h-1 mt-auto mx-3 mb-3" style="background-color: #e9ecef;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body p-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 d-inline-block mb-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 count-up">{{ $stats['pending_orders'] ?? 0 }}</h2>
                        <p class="text-muted small mb-0 text-uppercase fw-bold ls-1">Menunggu</p>
                    </div>
                    <div class="progress h-1 mt-auto mx-3 mb-3">
                        <div class="progress-bar bg-warning"
                            style="width: {{ $stats['total_orders'] > 0 ? ($stats['pending_orders'] / $stats['total_orders']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body p-3">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-3 d-inline-block mb-3">
                            <i class="bi bi-gear-wide-connected fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 count-up">{{ $stats['process_orders'] ?? 0 }}</h2>
                        <p class="text-muted small mb-0 text-uppercase fw-bold ls-1">Diproses</p>
                    </div>
                    <div class="progress h-1 mt-auto mx-3 mb-3">
                        <div class="progress-bar bg-info"
                            style="width: {{ $stats['total_orders'] > 0 ? ($stats['process_orders'] / $stats['total_orders']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body p-3">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 d-inline-block mb-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 count-up">{{ $stats['completed_orders'] ?? 0 }}</h2>
                        <p class="text-muted small mb-0 text-uppercase fw-bold ls-1">Selesai</p>
                    </div>
                    <div class="progress h-1 mt-auto mx-3 mb-3">
                        <div class="progress-bar bg-success"
                            style="width: {{ $stats['total_orders'] > 0 ? ($stats['completed_orders'] / $stats['total_orders']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. STATISTICS CARDS ROW 2 -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-2">Total Produk</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total_products'] ?? 0 }}</h3>
                            <small class="text-success"><i class="bi bi-box-seam me-1"></i>Katalog Aktif</small>
                        </div>
                        <div class="vr mx-3 opacity-10"></div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-2">Total Kategori</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total_categories'] ?? 0 }}</h3>
                            <small class="text-muted">Master Data</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold small mb-2">Total Pelanggan</h6>
                            <h3 class="fw-bold mb-1">{{ $stats['total_customers'] ?? 0 }}</h3>
                            <small class="text-muted">User Registrasi</small>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xl-4">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white text-opacity-75 text-uppercase fw-bold small mb-2">Pendapatan Bulan Ini</h6>
                                <h3 class="fw-bold mb-1">Rp {{ number_format($stats['revenue_month'] ?? 0, 0, ',', '.') }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                <i class="bi bi-wallet2 fs-4 text-white"></i>
                            </div>
                        </div>
                        <div class="mt-3 d-flex align-items-center gap-2">
                            @php $growth = $stats['revenue_growth'] ?? 0; @endphp
                            <span class="badge bg-white {{ $growth >= 0 ? 'text-success' : 'text-danger' }} rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-{{ $growth >= 0 ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"></i>
                                {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                            </span>
                            <small class="text-white text-opacity-75">vs bulan lalu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. MAIN CONTENT (CHART & TABLES) -->
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- CHART -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Tren Pendapatan {{ date('Y') }}</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; width: 100%;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- RECENT ORDERS -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-warning me-2"></i>Pesanan Terbaru</h5>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4 py-3 text-secondary small text-uppercase">No. Order</th>
                                    <th class="border-0 py-3 text-secondary small text-uppercase">Pelanggan</th>
                                    <th class="border-0 py-3 text-secondary small text-uppercase">Total</th>
                                    <th class="border-0 py-3 text-secondary small text-uppercase text-center">Status</th>
                                    <th class="border-0 py-3 text-secondary small text-uppercase text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">#{{ $order->order_number }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2 bg-primary bg-opacity-10 text-primary small shadow-sm">
                                                    {{ strtoupper(substr($order->user?->name ?? 'G', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ Str::limit($order->user?->name ?? 'Guest', 15) }}</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $order->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} rounded-pill px-3 fw-normal">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pesanan masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- ACTIONS -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark">Aksi Cepat</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.create') }}"
                                class="btn btn-outline-primary text-start p-3 d-flex align-items-center justify-content-between hover-bg-primary shadow-sm rounded-3">
                                <span><i class="bi bi-plus-circle me-2"></i> Tambah Produk</span><i class="bi bi-chevron-right small"></i>
                            </a>
                            <a href="{{ route('admin.categories.index') }}"
                                class="btn btn-outline-info text-start p-3 d-flex align-items-center justify-content-between hover-bg-info shadow-sm rounded-3">
                                <span><i class="bi bi-tags me-2"></i> Kelola Kategori</span><i class="bi bi-chevron-right small"></i>
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-outline-dark text-start p-3 d-flex align-items-center justify-content-between hover-bg-dark shadow-sm rounded-3">
                                <span><i class="bi bi-people me-2"></i> Data Pelanggan</span><i class="bi bi-chevron-right small"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TOP PRODUCTS -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy text-warning me-2"></i>Produk Terlaris</h6>
                    </div>
                    <div class="card-body pt-0">
                        @forelse ($topProducts as $index => $product)
                            <div class="list-group-item px-0 d-flex align-items-center border-0 mb-3">
                                <div class="me-3 position-relative">
                                    <div class="bg-light rounded p-2 text-center shadow-sm" style="width: 45px; height: 45px;">
                                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                                    </div>
                                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-{{ $index < 3 ? 'warning' : 'secondary' }} border border-white">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 text-dark small fw-bold text-truncate">{{ $product->name }}</h6>
                                    <small class="text-muted">{{ $product->total_sold }} unit terjual</small>
                                </div>
                                <div class="ms-2"><i class="bi bi-graph-up text-success small"></i></div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Belum ada data.</div>
                        @endforelse
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
            const clockEl = document.getElementById('realtimeClock');
            setInterval(() => clockEl.innerText = new Date().toLocaleTimeString('id-ID'), 1000);

            document.querySelectorAll('.count-up').forEach(counter => {
                const target = +counter.innerText;
                if (target === 0) return;
                const step = (timestamp, start, duration) => {
                    if (!start) start = timestamp;
                    const progress = Math.min((timestamp - start) / duration, 1);
                    counter.innerText = Math.floor(progress * target);
                    if (progress < 1) window.requestAnimationFrame((t) => step(t, start, duration));
                    else counter.innerText = target;
                };
                window.requestAnimationFrame((t) => step(t, null, 1000));
            });

            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                const chartData = @json($chartData);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [{
                            label: 'Pendapatan',
                            data: chartData.data || [],
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            borderColor: '#4e73df',
                            pointBackgroundColor: '#4e73df',
                            pointBorderColor: '#fff',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (c) => 'Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: (v) => v >= 1000000 ? 'Rp ' + (v / 1000000) + 'jt' : 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush