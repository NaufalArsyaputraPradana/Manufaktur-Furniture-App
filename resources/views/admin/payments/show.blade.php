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

        @php
            $isFullPending = $payment->payment_status === \App\Models\Payment::STATUS_FULL_PENDING;
            $isDpPaid = $payment->payment_status === \App\Models\Payment::STATUS_DP_PAID;
            $calculatedTotal = $payment->order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
            $dpAmount = round($calculatedTotal * 50 / 100, 2);
            $remainingPayment = $calculatedTotal - $dpAmount;
        @endphp

        {{-- Status Badge --}}
        @if ($isFullPending)
            <div class="alert alert-warning border-2 border-warning mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-hourglass-split me-3" style="font-size: 1.5rem;" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">Menunggu Konfirmasi Pelunasan</h5>
                        <p class="mb-0 small">Bukti pelunasan sudah diterima. Verifikasi kelengkapan bukti dan kesesuaian jumlah transfer dengan pesanan.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Proof Images --}}
                @if ($isFullPending)
                    {{-- Show both DP and Pelunasan proofs for FULL_PENDING --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-success"></i>Bukti Transfer DP (50%)</h6>
                        </div>
                        <div class="card-body p-4 text-center" style="min-height: 300px;">
                            @php
                                $dpProofPath = null;
                                if ($payment->payment_proof_dp) {
                                    $proof = $payment->payment_proof_dp;
                                    if (str_starts_with($proof, 'storage/')) {
                                        $dpProofPath = asset($proof);
                                    } elseif (str_starts_with($proof, '/')) {
                                        $dpProofPath = asset('storage' . $proof);
                                    } else {
                                        $dpProofPath = asset('storage/' . $proof);
                                    }
                                }
                            @endphp
                            @if ($dpProofPath)
                                <img src="{{ $dpProofPath }}" alt="Bukti DP" class="img-fluid rounded border"
                                    style="max-height: 350px; cursor:pointer;" onclick="showImageModal('{{ $dpProofPath }}', 'Bukti Transfer DP')">
                                <div class="mt-2">
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Terverifikasi</span>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti DP tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-warning"></i>Bukti Transfer Pelunasan (50%) - PENDING VERIFIKASI</h6>
                        </div>
                        <div class="card-body p-4 text-center" style="min-height: 300px;">
                            @php
                                $fullProofPath = null;
                                if ($payment->payment_proof_full) {
                                    $proof = $payment->payment_proof_full;
                                    if (str_starts_with($proof, 'storage/')) {
                                        $fullProofPath = asset($proof);
                                    } elseif (str_starts_with($proof, '/')) {
                                        $fullProofPath = asset('storage' . $proof);
                                    } else {
                                        $fullProofPath = asset('storage/' . $proof);
                                    }
                                } elseif ($payment->payment_proof) {
                                    $proof = $payment->payment_proof;
                                    if (str_starts_with($proof, 'storage/')) {
                                        $fullProofPath = asset($proof);
                                    } elseif (str_starts_with($proof, '/')) {
                                        $fullProofPath = asset('storage' . $proof);
                                    } else {
                                        $fullProofPath = asset('storage/' . $proof);
                                    }
                                }
                            @endphp
                            @if ($fullProofPath)
                                <img src="{{ $fullProofPath }}" alt="Bukti Pelunasan" class="img-fluid rounded border"
                                    style="max-height: 350px; cursor:pointer;" onclick="showImageModal('{{ $fullProofPath }}', 'Bukti Transfer Pelunasan')">
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi</span>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti pelunasan tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Regular payment proof display --}}
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
                @endif
            </div>

            <div class="col-lg-4">
                {{-- Payment Details --}}
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
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

                        @if ($isFullPending)
                            {{-- Detailed breakdown for FULL_PENDING --}}
                            <hr>
                            <h6 class="fw-bold mb-3 text-primary">Rincian Pembayaran</h6>
                            
                            <div class="mb-3 p-3 bg-light rounded-2 border">
                                <small class="text-muted fw-bold d-block mb-2">TOTAL PESANAN</small>
                                <div class="fs-5 fw-bold text-dark">
                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mb-3 p-3 bg-light rounded-2 border border-success">
                                <small class="text-success fw-bold d-block mb-2">✓ DP DIBAYAR (50%) - TERVERIFIKASI</small>
                                <div class="fs-5 fw-bold text-success">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-2 border border-warning">
                                <small class="text-warning fw-bold d-block mb-2">⏳ PELUNASAN DIBAYAR (50%) - PENDING VERIFIKASI</small>
                                <div class="fs-5 fw-bold text-warning">
                                    Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                </div>
                            </div>
                        @elseif ($isDpPaid)
                            {{-- Detailed breakdown for DP_PAID --}}
                            <hr>
                            <h6 class="fw-bold mb-3 text-primary">Rincian Pembayaran DP</h6>
                            
                            <div class="mb-3 p-3 bg-light rounded-2 border">
                                <small class="text-muted fw-bold d-block mb-2">TOTAL PESANAN</small>
                                <div class="fs-5 fw-bold text-dark">
                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-2 border border-success">
                                <small class="text-success fw-bold d-block mb-2">✓ DP DIBAYAR (50%) - SUDAH TERVERIFIKASI</small>
                                <div class="fs-5 fw-bold text-success">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>
                        @else
                            {{-- Regular amount display for PENDING (initial DP) --}}
                            <hr>
                            <h6 class="fw-bold mb-3 text-primary">Rincian Pembayaran DP</h6>
                            
                            <div class="mb-3 p-3 bg-light rounded-2 border">
                                <small class="text-muted fw-bold d-block mb-2">TOTAL PESANAN</small>
                                <div class="fs-5 fw-bold text-dark">
                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-2 border border-warning">
                                <small class="text-warning fw-bold d-block mb-2">⏳ DP DIBAYAR (50%) - PENDING VERIFIKASI</small>
                                <div class="fs-5 fw-bold text-warning">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        <div class="mb-0 pt-3 border-top">
                            <label class="small text-muted">Total Order</label>
                            <div class="fw-bold">Rp {{ number_format($payment->order->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                @if (in_array($payment->payment_status, [\App\Models\Payment::STATUS_PENDING, \App\Models\Payment::STATUS_DP_PAID], true) && $payment->payment_proof)
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
                @elseif ($isFullPending)
                    {{-- FULL_PENDING actions --}}
                    <div class="card shadow-sm border-warning border-2 rounded-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-check2-square me-2 text-warning"></i>Verifikasi Pelunasan</h6>
                            <p class="small text-muted mb-3">Periksa kelengkapan bukti dan pastikan jumlah transfer sesuai dengan rincian pembayaran di atas.</p>
                            
                            <form action="{{ route('admin.payments.confirm-final', $payment) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 fw-bold" onclick="return confirm('Konfirmasi pelunasan sebagai LUNAS?')">
                                    <i class="bi bi-check-circle me-1"></i>Konfirmasi Pelunasan → LUNAS
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Tolak Pembayaran
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 small rounded-3 border-0">
                        <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
                        <strong>Checklist Verifikasi:</strong>
                        <ul class="mb-0 mt-2 ps-4">
                            <li>✓ Bukti DP sudah terverifikasi</li>
                            <li>Periksa bukti pelunasan jelas dan terbaca</li>
                            <li>Pastikan jumlah sesuai: Rp {{ number_format($remainingPayment, 0, ',', '.') }}</li>
                            <li>Verifikasi nama pengirim & rekening tujuan</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0">
                    <h6 class="modal-title fw-bold" id="modalTitle">Bukti Pembayaran</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <img id="modalImage" src="" alt="Bukti" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                @csrf
                <div class="modal-content rounded-3">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title fw-bold">Tolak Pembayaran</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="notes" class="form-control rounded-2" rows="4" required
                                placeholder="Jelaskan alasan penolakan agar pelanggan dapat memperbaiki..."></textarea>
                            <small class="text-muted">Contoh: Bukti tidak jelas, nominal tidak sesuai, nama rekening berbeda</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary rounded-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-2 fw-bold">Tolak Pembayaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showImageModal(imageSrc, title) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = title;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }
    </script>
@endsection