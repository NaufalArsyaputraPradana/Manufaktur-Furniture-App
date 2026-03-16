@extends('layouts.production')

@section('title', 'Dashboard Produksi')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Produksi</h4>
            <p class="text-muted mb-0 small">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
                &nbsp;•&nbsp;
                <i class="bi bi-clock me-1"></i><span id="dashClock">{{ now()->format('H:i') }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-boxes me-1"></i>Daftar Order
            </a>
            <a href="{{ route('staff.production.schedules.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle me-1"></i>Jadwal Baru
            </a>
        </div>
    </div>

            {{-- Stats Cards --}}
            <div class="row g-4 mb-3">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-hourglass-split text-warning fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Menunggu</h6>
                                    <h3 class="fw-bold mb-0">{{ number_format($stats['pending'] ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-gear-wide-connected text-info fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Dalam Proses</h6>
                                    <h3 class="fw-bold mb-0">{{ number_format($stats['in_progress'] ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-check2-circle text-success fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Selesai</h6>
                                    <h3 class="fw-bold mb-0">{{ number_format($stats['completed'] ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="bi bi-person-check-fill text-primary fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Tugas Saya</h6>
                                    <h3 class="fw-bold mb-0">{{ number_format($stats['my_tasks'] ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Orders Ready for Production --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Order Siap Produksi</h5>
                            <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if ($recentOrders->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3">No. Order</th>
                                                <th>Customer</th>
                                                <th>Jumlah Item</th>
                                                <th>Status</th>
                                                <th>Tanggal Masuk</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentOrders as $order)
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">#{{ $order->order_number }}</td>
                                                    <td>{{ $order->user?->name ?? 'Guest' }}</td>
                                                    <td>{{ $order->orderDetails?->count() ?? 0 }} item</td>
                                                    <td>
                                                        <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                                    </td>
                                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                    <p class="text-muted mt-2">Tidak ada order siap produksi saat ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="row g-4">
                {{-- Left Column: Antrian Produksi --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
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
                                                        @if ($production->assignedUser)
                                                            <span class="badge bg-light text-dark"><i class="bi bi-person-circle me-1"></i>{{ explode(' ', trim($production->assignedUser->name))[0] }}</span>
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
                <div class="col-lg-4">
                    {{-- Aksi Cepat --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
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
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>Jadwal Mendatang</h5>
                            <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse ($upcomingSchedules as $schedule)
                                <a href="{{ route('staff.production.schedules.show', $schedule) }}" class="list-group-item list-group-item-action p-3">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-primary">{{ $schedule->title }}</strong>
                                        <small class="text-muted">{{ $schedule->start_datetime->format('H:i') }}</small>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar me-1"></i>{{ $schedule->start_datetime->translatedFormat('d M Y') }}
                                    </small>
                                    @if ($schedule->location)
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $schedule->location }}</small>
                                    @endif
                                </a>
                            @empty
                                <div class="list-group-item text-center py-3 text-muted">
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
                                <a href="{{ route('staff.production.todos.show', $todo) }}" class="list-group-item list-group-item-action p-3">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-dark">{{ $todo->title }}</strong>
                                        <span class="{{ $todo->status_badge_class }}">{{ $todo->status_label }}</span>
                                    </div>
                                    @if ($todo->deadline)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock me-1"></i>Deadline: {{ $todo->deadline->translatedFormat('d M Y H:i') }}
                                            @if ($todo->isOverdue())
                                                <span class="text-danger"><i class="bi bi-exclamation-triangle-fill ms-1"></i>Terlambat</span>
                                            @endif
                                        </small>
                                    @endif
                                </a>
                            @empty
                                <div class="list-group-item text-center py-3 text-muted">
                                    <i class="bi bi-check2-circle d-block mb-2 fs-3"></i>
                                    Tidak ada tugas pending
                                </div>
                            @endforelse
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