@extends('layouts.admin')

@section('title', 'Kubikasi')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15); }
        .opacity-90 { opacity: 0.9; }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .modern-table tbody tr { transition: background-color 0.2s; }
        .modern-table tbody tr:hover { background-color: rgba(78, 115, 223, 0.05); }

        /* Navigation Tabs Styling */
        .nav-pills .nav-link {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
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
                <div class="position-absolute top-0 end-0 opacity-10"
                    style="font-size: 8rem; margin-top: -1rem; margin-right: -1rem;">
                    <i class="bi bi-lightbulb"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3"
                                style="width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="bi bi-calculator fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Kubikasi</h2>
                                <p class="mb-0 opacity-90 small">Kelola perhitungan dan harga pokok produksi custom.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-lg-end">
                        <a href="{{ route('admin.orders.index') }}"
                            class="btn btn-light shadow-sm hover-lift text-primary fw-bold">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3">
                @php
                    $currentTab = request('tab', 'orders');
                @endphp
                <ul class="nav nav-pills nav-fill gap-2">
                    <li class="nav-item">
                        <a class="nav-link rounded-3 {{ $currentTab === 'orders' ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.custom-orders.index', array_filter(array_merge(request()->except('page'), ['tab' => 'orders']), fn($v) => $v !== null && $v !== '')) }}">
                            <i class="bi bi-list-ul me-2"></i>Daftar Kubikasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-3 {{ $currentTab === 'history' ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.custom-orders.index', array_filter(array_merge(request()->except('page'), ['tab' => 'history']), fn($v) => $v !== null && $v !== '')) }}">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Perhitungan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- History Tab Content --}}
        @if ($currentTab === 'history')
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Riwayat Perhitungan BOM</h6>
                            <small class="text-muted">Menampilkan item custom yang sudah memiliki riwayat perhitungan (arsip BOM).</small>
                        </div>
                        @if($customOrders->count() > 0)
                            <span class="badge bg-info rounded-pill">{{ $customOrders->total() }} item</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($customOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 modern-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold">Order #</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Customer</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Item Custom</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold">Spesifikasi</th>
                                        <th class="py-3 text-secondary small text-uppercase fw-bold text-center">Harga Saat Ini</th>
                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customOrders as $item)
                                        <tr>
                                            <td class="px-4">
                                                <a href="{{ route('admin.orders.show', $item->order_id) }}"
                                                    class="fw-bold text-decoration-none">
                                                    #{{ $item->order?->order_number ?? 'N/A' }}
                                                </a>
                                                <div class="small text-muted">
                                                    {{ $item->order?->created_at->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border shadow-sm"
                                                        style="width: 35px; height: 35px;">
                                                        <span class="fw-bold text-secondary small">
                                                            {{ strtoupper(substr($item->order?->user?->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $item->order?->user?->name ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                                <div class="small text-muted">{{ $item->quantity }} pcs</div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $item->custom_specifications['description'] ?? '' }}">
                                                    {{ $item->custom_specifications['description'] ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill border border-success">
                                                    <i class="bi bi-check-circle me-1"></i>Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-4 text-end">
                                                <a href="{{ route('admin.custom-orders.calculate', $item->id) }}"
                                                    class="btn btn-sm btn-primary shadow-sm px-3">
                                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted opacity-50 mb-2">
                                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="text-muted">Belum ada riwayat perhitungan</h6>
                                                <p class="text-muted small">Riwayat perhitungan akan muncul setelah Anda menyimpan perhitungan BOM untuk item custom.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($customOrders->hasPages())
                            <div class="card-footer bg-white border-top py-3">
                                {{ $customOrders->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted opacity-50 mb-2">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-muted">Belum ada riwayat perhitungan</h6>
                            <p class="text-muted small mb-3">Riwayat perhitungan akan muncul setelah Anda menyimpan perhitungan BOM untuk item custom.</p>
                            <a href="{{ route('admin.custom-orders.index', array_filter(array_merge(request()->all(), ['tab' => 'orders']), fn($v) => $v !== null && $v !== '')) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 h-100 hover-lift shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Menunggu Hitung</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ $pendingCount }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="bi bi-hourglass-split fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 h-100 hover-lift shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Harga Selesai</p>
                                <h3 class="mb-0 fw-bold text-success">{{ $processedCount }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="bi bi-check-circle-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 h-100 hover-lift shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Total Request</p>
                                <h3 class="mb-0 fw-bold text-info">{{ $totalCount }}</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                                <i class="bi bi-list-ul fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (request()->boolean('has_history'))
            <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-funnel fs-5"></i>
                <div>
                    <strong>Filter aktif:</strong> hanya item yang memiliki riwayat perhitungan (arsip BOM).
                    <a href="{{ route('admin.custom-orders.index', array_filter(request()->only(['order_id', 'search']))) }}" class="alert-link">Hapus filter</a>
                </div>
            </div>
        @endif

        <!-- Main Table -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-table me-2"></i>Daftar Item Custom</h5>

                    <form class="d-flex gap-2 align-items-end" method="GET" action="{{ route('admin.custom-orders.index') }}">
                        @if($currentTab)
                            <input type="hidden" name="tab" value="{{ $currentTab }}">
                        @endif
                        @if(request('order_id'))
                            <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                        @endif
                        <x-search-input 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari produk/customer..."
                            icon="bi-search"
                        />
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 modern-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold">Order #</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">Customer</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">Item Custom</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">Spesifikasi</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-center">Status Harga</th>
                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customOrders as $item)
                                <tr>
                                    <td class="px-4">
                                        <a href="{{ route('admin.orders.show', $item->order_id) }}"
                                            class="fw-bold text-decoration-none">
                                            #{{ $item->order?->order_number ?? 'N/A' }}
                                        </a>
                                        <div class="small text-muted">
                                            {{ $item->order?->created_at->format('d M Y') }}
                                            @if ($item->order?->status === 'cancelled')
                                                <span class="badge bg-danger ms-1">Dibatalkan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border shadow-sm"
                                                style="width: 35px; height: 35px;">
                                                <span class="fw-bold text-secondary small">
                                                    {{ strtoupper(substr($item->order?->user?->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $item->order?->user?->name ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                        <div class="small text-muted">{{ $item->quantity }} pcs</div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;"
                                            title="{{ $item->custom_specifications['description'] ?? '' }}">
                                            {{ $item->custom_specifications['description'] ?? '-' }}
                                        </div>
                                        @if (!empty($item->custom_specifications['design_image']))
                                            <a href="javascript:void(0)" onclick="showImageModal('{{ asset('storage/' . $item->custom_specifications['design_image']) }}', 'Desain Custom')"
                                                class="badge bg-info text-decoration-none mt-1 shadow-sm">
                                                <i class="bi bi-image me-1"></i>Lihat Desain
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->unit_price > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill border border-success">
                                                <i class="bi bi-check-circle me-1"></i>Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill border border-warning">
                                                <i class="bi bi-calculator me-1"></i>Perlu Dihitung
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-end">
                                        @if ($item->order?->status === 'cancelled')
                                            <span class="btn btn-sm btn-secondary shadow-sm px-3 disabled" title="Order dibatalkan">
                                                <i class="bi bi-ban me-1"></i>Tidak dapat dihitung
                                            </span>
                                        @else
                                            <a href="{{ route('admin.custom-orders.calculate', $item->id) }}"
                                                class="btn btn-sm btn-primary shadow-sm px-3">
                                                <i class="bi bi-calculator-fill me-1"></i>
                                                {{ $item->unit_price > 0 ? 'Edit Harga' : 'Hitung Harga' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-2">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        </div>
                                        <h6 class="text-muted">Tidak ada permintaan order custom yang ditemukan</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($customOrders->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $customOrders->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection