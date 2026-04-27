@extends('layouts.production')

@section('title', 'Detail Order #' . $order->order_number)

@push('styles')
<style>
    :root { --prod-primary: #10b981; --prod-secondary: #059669; --prod-success: #1cc88a; }
    .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .modern-table tbody tr { transition: background-color 0.2s ease; }
    .modern-table tbody tr:hover { background-color: rgba(78, 115, 223, 0.05); }
    .stat-card { padding: 1.5rem; border-radius: 1rem; background: white; border: 1px solid #f0f0f0; }
    .stat-label { color: #6c757d; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; display: block; margin-bottom: 0.5rem; }
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
                <h2 class="fw-bold mb-2 text-shadow">Detail Order Produksi</h2>
                <p class="text-white text-opacity-90 mb-0">
                    <i class="bi bi-box-seam me-1"></i>Order #{{ $order->order_number }} · 
                    <span class="badge bg-light text-dark ms-2">{{ $order->status_label }}</span>
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-lg-end">
                <a href="{{ route('production.monitoring.orders') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Informasi Order --}}
<div class="card border-0 shadow-sm rounded-4 mb-5 hover-lift">
    <div class="card-header bg-white border-0 py-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Order</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">No. Order</small>
                <strong class="text-primary fs-5 d-block mt-2">#{{ $order->order_number }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">Tanggal Order</small>
                <strong class="d-block mt-2">{{ $order->order_date ? $order->order_date->format('d M Y') : '—' }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">Customer</small>
                <strong class="d-block mt-2">{{ $order->user?->name ?? 'Guest' }}</strong>
                @if ($order->user?->email)
                    <small class="text-muted d-block">{{ $order->user->email }}</small>
                @endif
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">Status</small>
                <div class="mt-2">
                    <span class="badge bg-{{ $order->status_color }} px-3 py-2">{{ $order->status_label }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">Estimasi Selesai</small>
                <strong class="d-block mt-2">{{ $order->expected_completion_date ? $order->expected_completion_date->format('d M Y') : '—' }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block fw-bold text-uppercase">Total Harga</small>
                <strong class="text-primary d-block mt-2">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
            @if ($order->shipping_address)
                <div class="col-12">
                    <small class="text-muted d-block fw-bold text-uppercase"><i class="bi bi-geo-alt me-1"></i>Alamat Pengiriman</small>
                    <div class="mt-2 p-3 rounded-3 bg-light border border-secondary border-opacity-25">
                        {{ $order->shipping_address }}
                    </div>
                </div>
            @endif
            @if ($order->customer_notes)
                <div class="col-12">
                    <small class="text-muted d-block fw-bold text-uppercase"><i class="bi bi-chat-left-text me-1"></i>Catatan Pelanggan</small>
                    <div class="mt-2 p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                        {{ $order->customer_notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Daftar Item --}}
<div class="card border-0 shadow-sm rounded-4 mb-5 hover-lift">
    <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-bag-check text-primary me-2"></i>Item Pesanan</h5>
        <span class="badge bg-secondary">{{ $order->orderDetails->count() }} item</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle modern-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3">Produk</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end pe-4">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $item->product_name }}</div>
                            @if ($item->is_custom && !empty($item->custom_specifications['description']))
                                <small class="text-muted d-block">{{ Str::limit($item->custom_specifications['description'], 50) }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($item->is_custom)
                                <span class="badge bg-warning text-dark">Custom</span>
                            @else
                                <span class="badge bg-info">Katalog</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end pe-4 fw-bold text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="4" class="text-end fw-bold py-3 ps-4">Total</td>
                    <td class="text-end pe-4 fw-bold fs-5 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-5 text-center">
    <a href="{{ route('production.monitoring.order', $order) }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm fw-bold" style="transition: all 0.2s;">
        <i class="bi bi-gear me-2"></i>Mulai Process Sekarang
    </a>
</div>
@endsection