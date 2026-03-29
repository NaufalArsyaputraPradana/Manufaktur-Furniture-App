@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15); }
        .opacity-90 { opacity: 0.9; }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .modern-table tbody tr { transition: background-color 0.2s; }
        .modern-table tbody tr:hover { background-color: rgba(78, 115, 223, 0.05); }
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
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3"
                                style="width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="bi bi-cart-check fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Manajemen Pesanan</h2>
                                <p class="mb-0 opacity-90 small">Kelola pesanan masuk, produksi, dan pengiriman.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-lg-end">
                        <a href="{{ route('admin.orders.create') }}"
                            class="btn btn-light btn-lg shadow-sm hover-lift text-primary fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>Buat Pesanan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <x-form-input
                            name="status"
                            label="Status"
                            type="select"
                            :options="collect(['' => 'Semua Status'])->union(collect($orderStatuses)->flip()->flip()->mapWithKeys(fn($status) => [$status => ucfirst(str_replace('_', ' ', $status))]))"
                            :value="request('status')"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-form-input
                            name="date_from"
                            label="Dari Tanggal"
                            type="date"
                            :value="request('date_from')"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-form-input
                            name="date_to"
                            label="Sampai Tanggal"
                            type="date"
                            :value="request('date_to')"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted d-block">Pencarian</label>
                        <x-search-input 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="No. Order / Nama..." 
                            icon="bi-search"
                        />
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pesanan</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                    Total: {{ $orders->total() }}
                </span>
            </div>
            <div class="card-body p-0">
                @if ($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-bottom-0 fw-bold text-secondary text-uppercase small" width="56"></th>
                                    <th class="ps-4 py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">No. Order</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">Pelanggan</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">Tanggal</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">Total</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small text-center">Status</th>
                                    <th class="pe-4 py-3 border-bottom-0 fw-bold text-secondary text-uppercase small text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $d0 = $order->orderDetails->first();
                                        $thumb = $d0 ? ($d0->customDesignImageUrl() ?: ($d0->product ? $d0->product->thumbnail : null)) : null;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            @if ($thumb)
                                                <img src="{{ $thumb }}" alt="" class="rounded border" width="40" height="40" style="object-fit:cover;">
                                            @else
                                                <span class="d-inline-flex rounded bg-light border align-items-center justify-content-center text-muted small" style="width:40px;height:40px;">—</span>
                                            @endif
                                        </td>
                                        <td class="ps-2">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                                class="fw-bold text-primary text-decoration-none">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border"
                                                    style="width: 35px; height: 35px;">
                                                    <span class="fw-bold text-secondary small">{{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ Str::limit($order->user->name ?? 'Guest', 20) }}</div>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">{{ $order->user->email ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark small">{{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} border border-{{ $order->status_color }} rounded-pill px-3">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                                class="btn btn-sm btn-info text-white shadow-sm" data-bs-toggle="tooltip" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if (!in_array($order->status, ['cancelled', 'completed'], true))
                                                <a href="{{ route('admin.orders.edit', $order) }}"
                                                    class="btn btn-sm btn-warning text-white shadow-sm" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if (in_array($order->status, ['pending', 'cancelled'], true) && !in_array($order->payment?->payment_status, [\App\Models\Payment::STATUS_PAID, \App\Models\Payment::STATUS_DP_PAID], true))
                                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top bg-light">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3 text-muted opacity-25">
                            <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted fw-bold">Belum ada pesanan</h5>
                        <p class="text-muted mb-4">Pesanan yang dibuat akan muncul di sini.</p>
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Buat Pesanan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    </script>
@endpush