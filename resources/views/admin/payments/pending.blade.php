@extends('layouts.admin')

@section('title', 'Pembayaran Menunggu Verifikasi')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <div class="card-body text-white py-4">
                <h2 class="mb-1 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i>Pembayaran Menunggu Verifikasi</h2>
                <p class="mb-0 opacity-75 small">Daftar pembayaran dengan bukti transfer yang perlu diverifikasi</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pembayaran</h5>
                <span class="badge bg-primary rounded-pill">{{ $payments->total() }} item</span>
            </div>
            <div class="card-body p-0">
                @if ($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Order</th>
                                    <th>Pelanggan</th>
                                    <th>Metode</th>
                                    <th>Nominal</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-bold text-primary text-decoration-none">
                                                #{{ $payment->order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $payment->order->user->name ?? '-' }}</td>
                                        <td>{{ $payment->payment_method_name ?? ucfirst($payment->payment_method ?? '-') }}</td>
                                        <td class="fw-bold text-success">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>Verifikasi
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">{{ $payments->links('pagination::bootstrap-5') }}</div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Tidak ada pembayaran menunggu verifikasi</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection