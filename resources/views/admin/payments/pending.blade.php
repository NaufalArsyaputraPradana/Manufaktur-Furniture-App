@extends('layouts.admin')

@section('title', 'Pembayaran Menunggu Verifikasi')

@section('content')
    <div class="container-fluid">
        {{-- Header Hero Section --}}
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <div class="card-body text-white py-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="mb-1 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i>Manajemen Pembayaran</h2>
                        <p class="mb-0 opacity-75 small">Verifikasi pembayaran pelanggan dan lihat riwayat pembayaran yang sudah diproses</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm fw-bold" id="btnPending" onclick="showTab('pending')">
                            <i class="bi bi-hourglass-split me-2"></i>Menunggu Verifikasi
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm fw-bold" id="btnHistory" onclick="showTab('history')">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran
                        </button>
                    </div>
                </div>
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

        {{-- TAB 1: PENDING PAYMENTS --}}
        <div id="tabPending">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pembayaran Menunggu Verifikasi</h5>
                    <span class="badge bg-warning rounded-pill text-dark">{{ $pendingPayments->total() ?? 0 }} item</span>
                </div>
                <div class="card-body p-0">
                    @if (isset($pendingPayments) && $pendingPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tipe Pembayaran</th>
                                        <th>Metode</th>
                                        <th>Nominal</th>
                                        <th>Tanggal Upload</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingPayments as $payment)
                                        @php
                                            $isDP = in_array($payment->payment_channel, [\App\Models\Payment::CHANNEL_MANUAL_DP, \App\Models\Payment::CHANNEL_MIDTRANS]);
                                            $totalOrder = $payment->order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                                            $dpAmount = round($totalOrder * 50 / 100, 2);
                                            $displayAmount = $isDP ? $dpAmount : $totalOrder - $dpAmount;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-bold text-primary text-decoration-none">
                                                    #{{ $payment->order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->order->user->name ?? '-' }}</td>
                                            <td>
                                                @if ($isDP)
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="bi bi-hourglass-split me-1"></i>DP (50%)
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning rounded-pill text-dark">
                                                        <i class="bi bi-check-circle me-1"></i>Pelunasan (50%)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_method_name ?? ucfirst($payment->payment_method ?? '-') }}</td>
                                            <td class="fw-bold text-success">Rp {{ number_format($displayAmount, 0, ',', '.') }}</td>
                                            <td>
                                                <small class="text-muted">{{ $payment->created_at?->format('d M Y H:i') ?? '-' }}</small>
                                            </td>
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
                        <div class="p-3 border-top">{{ $pendingPayments->links('pagination::bootstrap-5') }}</div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Tidak ada pembayaran menunggu verifikasi</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- TAB 2: PAYMENT HISTORY --}}
        <div id="tabHistory" style="display: none;">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Pembayaran</h5>
                    <span class="badge bg-info rounded-pill">{{ $completedPayments->total() ?? 0 }} item</span>
                </div>
                <div class="card-body p-0">
                    @if (isset($completedPayments) && $completedPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tipe Pembayaran</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Tanggal Verifikasi</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($completedPayments as $payment)
                                        @php
                                            $isDP = in_array($payment->payment_channel, [\App\Models\Payment::CHANNEL_MANUAL_DP, \App\Models\Payment::CHANNEL_MIDTRANS]);
                                            $totalOrder = $payment->order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                                            $dpAmount = round($totalOrder * 50 / 100, 2);
                                            $displayAmount = $isDP ? $dpAmount : $totalOrder - $dpAmount;
                                            $isApproved = in_array($payment->payment_status, [\App\Models\Payment::STATUS_DP_PAID, \App\Models\Payment::STATUS_PAID]);
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-bold text-primary text-decoration-none">
                                                    #{{ $payment->order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->order->user->name ?? '-' }}</td>
                                            <td>
                                                @if ($isDP)
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="bi bi-hourglass-split me-1"></i>DP (50%)
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning rounded-pill text-dark">
                                                        <i class="bi bi-check-circle me-1"></i>Pelunasan (50%)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-success">Rp {{ number_format($displayAmount, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($isApproved)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Disetujui
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $payment->updated_at?->format('d M Y H:i') ?? '-' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">{{ $completedPayments->links('pagination::bootstrap-5') }}</div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Belum ada riwayat pembayaran</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function showTab(tab) {
        // Hide all tabs
        document.getElementById('tabPending').style.display = 'none';
        document.getElementById('tabHistory').style.display = 'none';
        
        // Remove active class from buttons
        document.getElementById('btnPending').classList.remove('btn-light');
        document.getElementById('btnPending').classList.add('btn-outline-light');
        document.getElementById('btnHistory').classList.remove('btn-light');
        document.getElementById('btnHistory').classList.add('btn-outline-light');
        
        // Show selected tab
        if (tab === 'pending') {
            document.getElementById('tabPending').style.display = 'block';
            document.getElementById('btnPending').classList.remove('btn-outline-light');
            document.getElementById('btnPending').classList.add('btn-light');
        } else if (tab === 'history') {
            document.getElementById('tabHistory').style.display = 'block';
            document.getElementById('btnHistory').classList.remove('btn-outline-light');
            document.getElementById('btnHistory').classList.add('btn-light');
        }
    }

    // Initialize: show pending tab by default
    document.addEventListener('DOMContentLoaded', function() {
        showTab('pending');
    });
</script>
@endsection