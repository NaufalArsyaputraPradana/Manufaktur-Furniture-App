@extends('layouts.production')

@section('title', 'Dashboard Produksi')

@push('styles')
    <style>
        :root { --prod-primary: #10b981; --prod-secondary: #059669; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .stat-card-primary { border-left: 4px solid var(--prod-primary); }
        .stat-card-hover { transition: all 0.3s ease; cursor: pointer; }
        .stat-card-hover:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 1.5rem rgba(78, 115, 223, 0.15) !important; }
        .icon-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .modern-table tbody tr { transition: background-color 0.2s; }
        .modern-table tbody tr:hover { background-color: rgba(16, 185, 129, 0.05); }
    </style>
@endpush

@section('content')
<div class="production-dashboard">
    <div class="container-fluid px-3 px-md-4">
    {{-- Page Header with Gradient --}}
    <div class="card border-0 shadow-sm mb-5 overflow-hidden" style="background: linear-gradient(135deg, var(--prod-primary) 0%, var(--prod-secondary) 100%);">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.1); z-index: 1;"></div>
        <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                    <h2 class="fw-bold mb-2 text-shadow">Dashboard Produksi</h2>
                    <p class="text-white text-opacity-90 mb-0">
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
                        &nbsp;•&nbsp;
                        <i class="bi bi-clock me-1"></i><span id="dashClock">{{ now()->format('H:i') }}</span>
                    </p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <a href="{{ route('production.monitoring.orders') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                            <i class="bi bi-boxes me-1"></i>Daftar Order
                        </a>
                        <a href="{{ route('staff.production.schedules.create') }}" class="btn btn-success shadow-sm fw-bold">
                            <i class="bi bi-plus-circle me-1"></i>Jadwal Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards dengan Design Modern --}}
    <div class="row g-3 mb-5">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift stat-card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning bg-opacity-10 me-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Menunggu</p>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['pending'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift stat-card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-info bg-opacity-10 me-3">
                            <i class="bi bi-gear-wide-connected text-info fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Dalam Proses</p>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['in_progress'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift stat-card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success bg-opacity-10 me-3">
                            <i class="bi bi-check2-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Selesai</p>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['completed'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift stat-card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary bg-opacity-10 me-3">
                            <i class="bi bi-person-check-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tugas Saya</p>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['my_tasks'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders Ready for Production --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Order Siap Produksi</h5>
                    <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentOrders->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 modern-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold">No. Order</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Customer</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Jumlah Item</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Status</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Tanggal Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentOrders as $order)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">#{{ $order->order_number }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $order->user?->name ?? 'Guest' }}</div>
                                                @if ($order->user?->email)
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $order->orderDetails?->count() ?? 0 }} item</span></td>
                                            <td>
                                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                            </td>
                                            <td><small class="text-muted">{{ $order->created_at->format('d M Y') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada order siap produksi saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="row g-4">
        {{-- Left Column: Antrian Produksi --}}
        <div class="col-12 col-xxl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-task text-primary me-2"></i>Antrian Produksi</h5>
                    <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($productions->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">No. Order</th>
                                        <th>Produk</th>
                                        <th style="min-width:130px;">Progress</th>
                                        <th>Status</th>
                                        <th>Petugas</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productions as $production)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">#{{ $production->order?->order_number ?? 'N/A' }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width:200px;" title="{{ $production->orderDetail?->product?->name ?? ($production->order?->orderDetails?->first()?->product_name ?? 'Custom Item') }}">
                                                    {{ $production->orderDetail?->product?->name ?? ($production->order?->orderDetails?->first()?->product_name ?? 'Custom Item') }}
                                                </div>
                                                @if ($production->orderDetail?->quantity)
                                                    <small class="text-muted">Qty: {{ $production->orderDetail->quantity }} Unit</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress mb-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $production->status_color }}" style="width: {{ $production->progress_percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $production->progress_percentage }}%</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $production->status_color }}">{{ $production->status_label }}</span>
                                            </td>
                                            <td>
                                                @if ($production->assignedTo)
                                                    <span class="badge bg-light text-dark"><i class="bi bi-person-circle me-1"></i>{{ explode(' ', trim($production->assignedTo->name))[0] }}</span>
                                                @else
                                                    <span class="text-muted fst-italic small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('production.tracking.show', $production) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-2">Belum ada proses produksi yang sedang aktif.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Aksi Cepat, Jadwal, dan To Do --}}
        <div class="col-12 col-xxl-4">
            <div class="d-flex flex-column gap-4">
                {{-- Aksi Cepat --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('production.monitoring.orders') }}" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-boxes me-2"></i>Daftar Order</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-outline-info text-start d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-calendar3 me-2"></i>Jadwal Produksi</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('staff.production.todos.index') }}" class="btn btn-outline-success text-start d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-list-check me-2"></i>To Do List</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Jadwal Mendatang --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>Jadwal Mendatang</h5>
                        <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($upcomingSchedules as $schedule)
                            <a href="{{ route('staff.production.schedules.show', $schedule) }}" class="list-group-item list-group-item-action p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <strong class="text-primary d-block">{{ $schedule->title }}</strong>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar me-1"></i>{{ $schedule->start_datetime->translatedFormat('d M Y') }}
                                        </small>
                                        @if ($schedule->location)
                                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $schedule->location }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted text-nowrap">{{ $schedule->start_datetime->format('H:i') }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center py-4 text-muted">
                                <i class="bi bi-calendar-x d-block mb-2 fs-3"></i>
                                Tidak ada jadwal mendatang
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- To Do Pending --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-list-check text-primary me-2"></i>To Do Pending</h5>
                        <a href="{{ route('staff.production.todos.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($pendingTodos as $todo)
                            <a href="{{ route('staff.production.todos.show', $todo) }}" class="list-group-item list-group-item-action p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <strong class="text-dark d-block">{{ $todo->title }}</strong>
                                        @if ($todo->deadline)
                                            <small class="text-muted d-block">
                                                <i class="bi bi-clock me-1"></i>{{ $todo->deadline->translatedFormat('d M Y H:i') }}
                                                @if ($todo->isOverdue())
                                                    <span class="text-danger"><i class="bi bi-exclamation-triangle-fill ms-1"></i>Terlambat</span>
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    <span class="{{ $todo->status_badge_class }} text-nowrap">{{ $todo->status_label }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center py-4 text-muted">
                                <i class="bi bi-check2-circle d-block mb-2 fs-3"></i>
                                Tidak ada tugas pending
                            </div>
                        @endforelse
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
    const clockEl = document.getElementById('dashClock');
    if (clockEl) {
        setInterval(() => {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }, 1000);
    }
});
</script>
@endpush