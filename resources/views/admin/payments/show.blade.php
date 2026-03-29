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
            $isPending = $payment->payment_status === \App\Models\Payment::STATUS_PENDING;
            
            $isDP = in_array($payment->payment_channel, [\App\Models\Payment::CHANNEL_MANUAL_DP, \App\Models\Payment::CHANNEL_MIDTRANS]);
            $calculatedTotal = $payment->order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
            $dpAmount = round($calculatedTotal * 50 / 100, 2);
            $remainingPayment = $calculatedTotal - $dpAmount;
        @endphp

        {{-- Status Badge & Alert --}}
        @if ($isFullPending)
            <div class="alert alert-warning border-2 border-warning mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-hourglass-split me-3" style="font-size: 1.5rem;" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">⏳ Menunggu Konfirmasi Pelunasan (50% Sisa)</h5>
                        <p class="mb-2 small">DP sudah terverifikasi. Sekarang menunggu verifikasi pelunasan sisa cicilan.</p>
                        <small class="text-muted">• Verifikasi kelengkapan bukti pelunasan<br>• Pastikan jumlah transfer sesuai rincian (50% sisa)<br>• Periksa nama pengirim & rekening tujuan</small>
                    </div>
                </div>
            </div>
        @elseif ($isDpPaid)
            <div class="alert alert-info border-2 border-info mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle me-3" style="font-size: 1.5rem;" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">✓ DP Sudah Terverifikasi (50%)</h5>
                        <p class="mb-0 small">Pembayaran DP telah dikonfirmasi. Menunggu pelanggan menyelesaikan pelunasan sisa cicilan.</p>
                    </div>
                </div>
            </div>
        @elseif ($isPending && $isDP)
            <div class="alert alert-info border-2 border-info mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-hourglass-split me-3" style="font-size: 1.5rem;" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">⏳ Menunggu Verifikasi DP (50%)</h5>
                        <p class="mb-2 small">Pelanggan telah mengunggah bukti pembayaran DP (50% dari total pesanan).</p>
                        <small class="text-muted">• Verifikasi kelengkapan bukti transfer<br>• Pastikan nominal sesuai dengan rincian DP<br>• Periksa nama pengirim & rekening tujuan</small>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info border-2 border-info mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-hourglass-split me-3" style="font-size: 1.5rem;" aria-hidden="true"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">⏳ Menunggu Verifikasi Pembayaran Penuh (100%)</h5>
                        <p class="mb-2 small">Pelanggan telah mengunggah bukti pembayaran penuh untuk seluruh pesanan.</p>
                        <small class="text-muted">• Verifikasi kelengkapan bukti transfer<br>• Pastikan nominal sesuai dengan total pesanan<br>• Periksa nama pengirim & rekening tujuan</small>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Proof Images --}}
                @if ($isFullPending)
                    {{-- Show both DP and Pelunasan proofs for FULL_PENDING --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4 border-success border-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <span class="badge bg-success me-2"><i class="bi bi-check-circle me-1"></i>TERVERIFIKASI</span>
                                <i class="bi bi-image me-2 text-success"></i>Bukti Transfer DP (50%)
                            </h6>
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
                                    style="max-height: 350px; cursor:pointer;" onclick="showImageModal('{{ $dpProofPath }}', 'Bukti Transfer DP (50%)')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-success">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong></small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti DP tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 mb-4 border-warning border-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <span class="badge bg-warning text-dark me-2"><i class="bi bi-hourglass-split me-1"></i>PENDING VERIFIKASI</span>
                                <i class="bi bi-image me-2 text-warning"></i>Bukti Transfer Pelunasan (50% Sisa)
                            </h6>
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
                                    style="max-height: 350px; cursor:pointer;" onclick="showImageModal('{{ $fullProofPath }}', 'Bukti Transfer Pelunasan (50% Sisa)')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-warning">Rp {{ number_format($remainingPayment, 0, ',', '.') }}</strong></small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti pelunasan tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif ($isPending && $isDP)
                    {{-- DP Proof for PENDING (DP Mode) --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4 border-info border-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <span class="badge bg-info me-2"><i class="bi bi-hourglass-split me-1"></i>PENDING VERIFIKASI</span>
                                <i class="bi bi-image me-2 text-info"></i>Bukti Transfer DP (50% dari Total)
                            </h6>
                        </div>
                        <div class="card-body p-4 text-center" style="min-height: 350px;">
                            @if ($payment->payment_proof)
                                <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti DP"
                                    class="img-fluid rounded border" style="max-height: 400px; cursor:pointer;"
                                    onclick="showImageModal('{{ asset('storage/' . $payment->payment_proof) }}', 'Bukti Pembayaran DP (50%)')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-info">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong></small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti pembayaran DP tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif ($isDpPaid)
                    {{-- Show DP Proof (Verified) for DP_PAID --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4 border-success border-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <span class="badge bg-success me-2"><i class="bi bi-check-circle me-1"></i>TERVERIFIKASI</span>
                                <i class="bi bi-image me-2 text-success"></i>Bukti Transfer DP (50%)
                            </h6>
                        </div>
                        <div class="card-body p-4 text-center" style="min-height: 350px;">
                            @if ($payment->payment_proof_dp)
                                <img src="{{ asset('storage/' . $payment->payment_proof_dp) }}" alt="Bukti DP"
                                    class="img-fluid rounded border" style="max-height: 400px; cursor:pointer;"
                                    onclick="showImageModal('{{ asset('storage/' . $payment->payment_proof_dp) }}', 'Bukti Pembayaran DP (50%) - TERVERIFIKASI')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-success">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong></small>
                                </div>
                            @elseif ($payment->payment_proof)
                                <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti DP"
                                    class="img-fluid rounded border" style="max-height: 400px; cursor:pointer;"
                                    onclick="showImageModal('{{ asset('storage/' . $payment->payment_proof) }}', 'Bukti Pembayaran DP (50%) - TERVERIFIKASI')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-success">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong></small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti pembayaran DP tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info border-2 rounded-3 mt-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle me-3" style="font-size: 1.2rem;"></i>
                            <div>
                                <h6 class="fw-bold mb-2">Status: Menunggu Pelunasan</h6>
                                <p class="mb-2 small">Pembayaran DP sudah terverifikasi. Sekarang menunggu pelanggan melakukan pelunasan untuk sisa cicilan.</p>
                                <small class="text-muted d-block">Nominal pelunasan yang diharapkan: <strong>Rp {{ number_format($remainingPayment, 0, ',', '.') }}</strong></small>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Regular full payment proof display --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4 border-info border-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <span class="badge bg-info me-2"><i class="bi bi-hourglass-split me-1"></i>PENDING VERIFIKASI</span>
                                <i class="bi bi-image me-2 text-info"></i>Bukti Pembayaran Penuh (100%)
                            </h6>
                        </div>
                        <div class="card-body p-4 text-center" style="min-height: 350px;">
                            @if ($payment->payment_proof)
                                <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran"
                                    class="img-fluid rounded border" style="max-height: 400px; cursor:pointer;"
                                    onclick="showImageModal('{{ asset('storage/' . $payment->payment_proof) }}', 'Bukti Pembayaran Penuh (100%)')">
                                <div class="mt-3">
                                    <small class="text-muted">Nominal yang dibayar: <strong class="text-info">Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</strong></small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;" aria-hidden="true"></i>
                                    <p class="text-muted mt-3">Bukti pembayaran tidak ditemukan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                {{-- Payment Details Card --}}
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Detail Pembayaran</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="small text-muted">Order</label>
                            <div><a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-bold text-primary">#{{ $payment->order->order_number }}</a></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Pelanggan</label>
                            <div class="fw-bold">{{ $payment->order->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->order->user->email ?? '' }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Metode Pembayaran</label>
                            <div>{{ $payment->payment_method_name ?? ucfirst($payment->payment_method ?? '-') }}</div>
                        </div>
                        <hr>

                        {{-- Payment Breakdown --}}
                        <h6 class="fw-bold mb-3 text-primary">Rincian Pembayaran</h6>
                        
                        <div class="mb-3 p-3 bg-light rounded-2 border">
                            <small class="text-muted fw-bold d-block mb-2">TOTAL HARGA PESANAN</small>
                            <div class="fs-6 fw-bold text-dark">
                                Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                            </div>
                        </div>

                        @if ($isFullPending)
                            {{-- FULL_PENDING: Show DP (verified) and Pelunasan (pending) --}}
                            <div class="mb-3 p-3 bg-light rounded-2 border border-success-subtle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-success fw-bold d-block mb-1">✓ PEMBAYARAN DP (50%)</small>
                                        <small class="text-muted">Status: Terverifikasi</small>
                                    </div>
                                    <span class="badge bg-success">OK</span>
                                </div>
                                <div class="fs-6 fw-bold text-success mt-2">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-2 border border-warning-subtle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-warning fw-bold d-block mb-1">⏳ PELUNASAN (50% SISA)</small>
                                        <small class="text-muted">Status: Pending Verifikasi</small>
                                    </div>
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                </div>
                                <div class="fs-6 fw-bold text-warning mt-2">
                                    Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                </div>
                            </div>
                        @elseif ($isDpPaid)
                            {{-- DP_PAID: Show only DP (verified) --}}
                            <div class="p-3 bg-light rounded-2 border border-success-subtle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-success fw-bold d-block mb-1">✓ PEMBAYARAN DP (50%)</small>
                                        <small class="text-muted">Status: Terverifikasi</small>
                                    </div>
                                    <span class="badge bg-success">OK</span>
                                </div>
                                <div class="fs-6 fw-bold text-success mt-2">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning rounded-2">
                                <small class="text-warning fw-bold d-block mb-1">Sisa Pelunasan</small>
                                <div class="fs-6 fw-bold text-warning">
                                    Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                </div>
                                <small class="text-muted d-block mt-1">Menunggu pelanggan untuk membayar</small>
                            </div>
                        @elseif ($isPending && $isDP)
                            {{-- PENDING (DP Mode): Show DP pending --}}
                            <div class="p-3 bg-light rounded-2 border border-info-subtle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-info fw-bold d-block mb-1">⏳ PEMBAYARAN DP (50%)</small>
                                        <small class="text-muted">Status: Pending Verifikasi</small>
                                    </div>
                                    <span class="badge bg-info">PENDING</span>
                                </div>
                                <div class="fs-6 fw-bold text-info mt-2">
                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-light rounded-2 border">
                                <small class="text-muted fw-bold d-block mb-1">Sisa Pelunasan</small>
                                <div class="fs-6 fw-bold text-dark">
                                    Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                </div>
                                <small class="text-muted d-block mt-1">Akan dibayar di tahap pelunasan</small>
                            </div>
                        @else
                            {{-- PENDING (Full Mode): Show full payment pending --}}
                            <div class="p-3 bg-light rounded-2 border border-info-subtle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-info fw-bold d-block mb-1">⏳ PEMBAYARAN PENUH (100%)</small>
                                        <small class="text-muted">Status: Pending Verifikasi</small>
                                    </div>
                                    <span class="badge bg-info">PENDING</span>
                                </div>
                                <div class="fs-6 fw-bold text-info mt-2">
                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if ($isFullPending && $payment->payment_proof_full)
                    {{-- FULL_PENDING actions --}}
                    <div class="card shadow-sm border-warning border-2 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-check2-square me-2 text-warning"></i>Verifikasi Pelunasan (50% Sisa)
                            </h6>
                            <p class="small text-muted mb-3">Periksa kelengkapan bukti dan pastikan jumlah transfer sesuai dengan rincian pembayaran.</p>
                            
                            <form action="{{ route('admin.payments.confirm-final', $payment) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 fw-bold" 
                                    onclick="return confirm('Konfirmasi pelunasan ini sebagai LUNAS? Pesanan akan dilanjutkan ke tahap produksi.')">
                                    <i class="bi bi-check-circle me-1"></i>Setujui Pelunasan → LUNAS
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Tolak Pelunasan
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-warning small rounded-3 border-0">
                        <i class="bi bi-list-check me-2 fw-bold"></i>
                        <strong>Checklist Verifikasi Pelunasan:</strong>
                        <ul class="mb-0 mt-2 ps-4">
                            <li>✓ DP sudah terverifikasi</li>
                            <li>Bukti pelunasan jelas dan terbaca</li>
                            <li>Nominal: Rp {{ number_format($remainingPayment, 0, ',', '.') }}</li>
                            <li>Nama pengirim & rekening tujuan terverifikasi</li>
                        </ul>
                    </div>
                @elseif ($isPending && $isDP && $payment->payment_proof)
                    {{-- PENDING (DP Mode) actions --}}
                    <div class="card shadow-sm border-info border-2 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-check2-square me-2 text-info"></i>Verifikasi DP (50% dari Total)
                            </h6>
                            <p class="small text-muted mb-3">Periksa kelengkapan bukti dan pastikan jumlah transfer sesuai dengan rincian pembayaran.</p>
                            
                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-info w-100 fw-bold text-white" 
                                    onclick="return confirm('Setujui DP ini? Status akan berubah menjadi DP Terverifikasi, menunggu pelunasan.')">
                                    <i class="bi bi-check-circle me-1"></i>Setujui DP (50%)
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Tolak DP
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info small rounded-3 border-0">
                        <i class="bi bi-list-check me-2 fw-bold"></i>
                        <strong>Checklist Verifikasi DP:</strong>
                        <ul class="mb-0 mt-2 ps-4">
                            <li>Bukti transfer jelas dan terbaca</li>
                            <li>Nominal: Rp {{ number_format($dpAmount, 0, ',', '.') }}</li>
                            <li>Nama pengirim & rekening tujuan terverifikasi</li>
                            <li>Tanggal transfer masuk akal</li>
                        </ul>
                    </div>
                @elseif ($isPending && !$isDP && $payment->payment_proof)
                    {{-- PENDING (Full Mode) actions --}}
                    <div class="card shadow-sm border-success border-2 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-check2-square me-2 text-success"></i>Verifikasi Pembayaran Penuh (100%)
                            </h6>
                            <p class="small text-muted mb-3">Periksa kelengkapan bukti dan pastikan jumlah transfer sesuai dengan rincian pembayaran.</p>
                            
                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 fw-bold" 
                                    onclick="return confirm('Setujui pembayaran penuh ini? Pesanan akan dilanjutkan ke tahap produksi.')">
                                    <i class="bi bi-check-circle me-1"></i>Setujui Pembayaran Penuh
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Tolak Pembayaran
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-success small rounded-3 border-0">
                        <i class="bi bi-list-check me-2 fw-bold"></i>
                        <strong>Checklist Verifikasi Pembayaran:</strong>
                        <ul class="mb-0 mt-2 ps-4">
                            <li>Bukti transfer jelas dan terbaca</li>
                            <li>Nominal: Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</li>
                            <li>Nama pengirim & rekening tujuan terverifikasi</li>
                            <li>Tanggal transfer masuk akal</li>
                        </ul>
                    </div>
                @endif

                {{-- Status: DP_PAID (No action needed) --}}
                @if ($isDpPaid)
                    <div class="alert alert-success small rounded-3 border-0">
                        <i class="bi bi-info-circle me-2 fw-bold"></i>
                        <strong>Status: DP Terverifikasi</strong>
                        <p class="mb-0 mt-2 small">
                            DP sudah dikonfirmasi. Menunggu pelanggan melakukan pembayaran pelunasan untuk sisa cicilan sebesar Rp {{ number_format($remainingPayment, 0, ',', '.') }}.
                        </p>
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
                            <small class="text-muted">Contoh: Bukti tidak jelas, nominal tidak sesuai, nama rekening berbeda, bukti ganda</small>
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