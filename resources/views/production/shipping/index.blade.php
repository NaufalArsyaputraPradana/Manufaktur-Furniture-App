@extends('layouts.production')

@section('title', 'Monitoring Pengiriman')

@push('styles')
    <style>
        :root { --prod-primary: #10b981; --prod-secondary: #059669; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .modern-table tbody tr { transition: background-color 0.2s; }
        .modern-table tbody tr:hover { background-color: rgba(16, 185, 129, 0.05); }
        .shipping-stage-badge { font-size: 0.85rem; }
    </style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4">
    {{-- Page Header --}}
    <div class="card border-0 shadow-sm mb-5 overflow-hidden" style="background: linear-gradient(135deg, var(--prod-primary) 0%, var(--prod-secondary) 100%);">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.1); z-index: 1;"></div>
        <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                    <h2 class="fw-bold mb-2 text-shadow">Monitoring Pengiriman</h2>
                    <p class="text-white text-opacity-90 mb-0">Lacak tahapan muat, serah ke kurir, dan dokumentasi pengiriman per pesanan</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <a href="{{ route('production.dashboard') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-truck text-primary me-2"></i>Order Aktif Pengiriman
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $orders->count() }} order</span>
        </div>

        @if ($orders->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 modern-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold">#</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">No. Order</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Pelanggan</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Barang</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Status Order</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Status Pengiriman</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Update Terakhir</th>
                            <th class="text-end pe-4 py-3 text-secondary small text-uppercase fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $i => $order)
                            @php
                            $latest = $order->shippingLogs->first();
                            $shipBadge = match ($order->shipping_status) {
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'processing' => 'info',
                            default => 'secondary',
                            };
                            $shipIcon = match ($order->shipping_status) {
                            'shipped' => 'bi-box-seam',
                            'delivered' => 'bi-check2-circle',
                            'processing' => 'bi-hourglass-split',
                            default => 'bi-question-circle',
                            };
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small fw-bold">{{ $i + 1 }}</td>
                                <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->user?->name ?? '—' }}</div>
                                    @if ($order->user?->email)
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    @endif
                                </td>
                                <td>
                                    @forelse($order->orderDetails?->take(2) ?? [] as $detail)
                                        <div class="text-truncate" style="max-width:200px;" title="{{ $detail->product_name ?? 'Item' }}">
                                            <span class="fw-semibold">{{ $detail->product_name ?? 'Item' }}</span>
                                        </div>
                                    @empty
                                        <span class="text-muted fst-italic">—</span>
                                    @endforelse
                                    @if (($order->orderDetails?->count() ?? 0) > 2)
                                        <small class="text-muted">+{{ $order->orderDetails->count() - 2 }} lainnya</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $shipBadge }} shipping-stage-badge">
                                        <i class="bi {{ $shipIcon }} me-1"></i>{{ $order->shipping_status_label }}
                                    </span>
                                    @if ($order->shipping_logs_count > 0)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-journal-text me-1"></i><strong>{{ $order->shipping_logs_count }}</strong> catatan
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($latest)
                                        <div class="small fw-semibold text-dark">{{ $latest->stage_label }}</div>
                                        <div class="text-muted small">
                                            {{ $latest->created_at->format('d M Y, H:i') }}
                                            @if ($latest->recordedBy)
                                                <br><i class="bi bi-person me-1"></i>{{ $latest->recordedBy->name }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Belum ada log</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('production.shipping.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill d-inline-flex align-items-center shadow-sm" title="Monitor Pengiriman">
                                        <i class="bi bi-clipboard-data me-1"></i>Monitor
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
                <p class="text-muted mt-2 mb-3">Belum ada order yang memerlukan monitoring pengiriman.</p>
                <a href="{{ route('production.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
