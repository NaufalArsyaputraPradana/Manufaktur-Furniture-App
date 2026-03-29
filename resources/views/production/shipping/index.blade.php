@extends('layouts.production')

@section('title', 'Monitoring Pengiriman')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monitoring Pengiriman</h4>
            <p class="text-muted mb-0 small">
                Lacak tahapan muat, serah ke kurir, dan dokumentasi pengiriman per pesanan.
            </p>
        </div>
        <a href="{{ route('production.dashboard') }}" class="btn btn-sm btn-outline-secondary align-self-start">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-truck text-primary me-2"></i>Order aktif
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $orders->count() }} order</span>
        </div>

        @if ($orders->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Barang</th>
                            <th>Status order</th>
                            <th>Status pengiriman</th>
                            <th>Update terakhir</th>
                            <th class="text-end pe-4">Aksi</th>
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
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $order->user?->name ?? '—' }}</span>
                                    @if ($order->user?->email)
                                        <br><small class="text-muted">{{ $order->user->email }}</small>
                                    @endif
                                </td>
                                <td>
                                    @forelse($order->orderDetails?->take(2) ?? [] as $detail)
                                        <div class="text-truncate" style="max-width:200px;"
                                            title="{{ $detail->product_name ?? 'Item' }}">
                                            {{ $detail->product_name ?? 'Item' }}
                                        </div>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                    @if (($order->orderDetails?->count() ?? 0) > 2)
                                        <small class="text-muted">+{{ $order->orderDetails->count() - 2 }} lainnya</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $shipBadge }}">{{ $order->shipping_status_label }}</span>
                                    @if ($order->shipping_logs_count > 0)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-journal-text me-1"></i>{{ $order->shipping_logs_count }} catatan
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($latest)
                                        <div class="small fw-semibold text-dark">
                                            {{ $latest->stage_label }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $latest->created_at->format('d M Y, H:i') }}
                                            @if ($latest->recordedBy)
                                                · {{ $latest->recordedBy->name }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Belum ada log</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('production.shipping.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill d-inline-flex align-items-center shadow-sm">
                                        <i class="bi bi-clipboard-data me-1"></i>
                                        <span>Monitor</span>
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
                <p class="text-muted mt-2 mb-0">Belum ada order yang memerlukan monitoring pengiriman.</p>
            </div>
        @endif
    </div>
</div>
@endsection
