@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran - #' . $payment->order->order_number)

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Verifikasi Pembayaran</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.pending') }}">Pembayaran</a></li>
                        <li class="breadcrumb-item active">#{{ $payment->order->order_number }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.orders.show', $payment->order) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Ke Detail Order
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>Bukti Pembayaran</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        @if ($payment->payment_proof)
                            <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran"
                                class="img-fluid rounded border" style="max-height: 500px; cursor:pointer;"
                                onclick="showImageModal('{{ asset('storage/' . $payment->payment_proof) }}', 'Bukti Pembayaran')">
                        @else
                            <p class="text-muted">Tidak ada bukti pembayaran</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Detail Pembayaran</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="small text-muted">Order</label>
                            <div><a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->order_number }}</a></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Pelanggan</label>
                            <div class="fw-bold">{{ $payment->order->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->order->user->email ?? '' }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Metode</label>
                            <div>{{ $payment->payment_method_name ?? ucfirst($payment->payment_method ?? '-') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Nominal</label>
                            <div class="fs-5 fw-bold text-success">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted">Total Order</label>
                            <div class="fw-bold">Rp {{ number_format($payment->order->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                @if ($payment->payment_status === 'unpaid' && $payment->payment_proof)
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Tindakan</h6>
                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i>Setujui Pembayaran
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Tolak Pembayaran
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Tolak Pembayaran</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="notes" class="form-control" rows="4" required
                                placeholder="Masukkan alasan penolakan agar pelanggan dapat memperbaiki..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection