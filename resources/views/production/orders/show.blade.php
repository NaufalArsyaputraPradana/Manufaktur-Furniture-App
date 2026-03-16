@extends('layouts.production')

@section('title', 'Detail Order #' . $order->order_number)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Order Produksi</h4>
            <p class="text-muted mb-0 small">No. Order: <span class="fw-semibold">#{{ $order->order_number }}</span></p>
        </div>
        <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

            {{-- Informasi Order --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Order</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">No. Order</label>
                            <div class="fw-bold">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Tanggal Order</label>
                            <div>{{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Customer</label>
                            <div>{{ $order->user?->name ?? 'Guest' }}</div>
                            @if ($order->user?->email)
                                <small class="text-muted">{{ $order->user->email }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Status Order</label>
                            <div><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Estimasi Selesai</label>
                            <div>{{ $order->expected_completion_date ? $order->expected_completion_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Alamat Pengiriman</label>
                            <div>{{ $order->shipping_address ?? '-' }}</div>
                        </div>
                        @if ($order->customer_notes)
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold">Catatan Pelanggan</label>
                                <div class="bg-light p-3 rounded-3">{{ $order->customer_notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Daftar Item --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-cart3 text-primary me-2"></i>Item Pesanan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
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
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-3">Total</td>
                                <td class="text-end pe-4 fw-bold fs-5 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center">
            <a href="{{ route('production.monitoring.index', $order) }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm d-inline-flex align-items-center" style="transition:background .2s;">
                <i class="bi bi-gear me-2"></i> Process Sekarang
            </a>
</div>
@endsection