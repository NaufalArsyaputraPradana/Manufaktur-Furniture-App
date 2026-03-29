@extends('layouts.admin')

@section('title', 'Pembayaran Menunggu Verifikasi')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
        }

        .stats-card {
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Header Hero Section --}}
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-credit-card-2-front fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Manajemen Pembayaran</h2>
                                <p class="mb-0 opacity-90 small">Verifikasi pembayaran pelanggan dan lihat riwayat semua transaksi pembayaran</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link {{ request('tab', 'pending') === 'pending' ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.payments.pending', ['tab' => 'pending']) }}">
                            <i class="bi bi-hourglass-split me-2"></i>Menunggu Verifikasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('tab') === 'history' ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.payments.pending', ['tab' => 'history']) }}">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body py-3">
                @php
                    // Build month options (1 => January, etc.)
                    $monthOptions = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $monthOptions[$m] = DateTime::createFromFormat('!m', $m)->format('F');
                    }
                    $currentMonth = request('month', now()->month);

                    // Build year options (current year down to 2020)
                    $yearOptions = [];
                    for ($y = date('Y'); $y >= 2020; $y--) {
                        $yearOptions[$y] = (string) $y;
                    }
                    $currentYear = request('year', now()->year);
                @endphp

                <form method="GET" action="{{ route('admin.payments.pending') }}" class="d-flex align-items-end gap-3">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif

                    <div class="flex-grow-1">
                        <x-form-input
                            name="month"
                            type="select"
                            label="Periode Bulan"
                            :options="$monthOptions"
                            :value="request('month', now()->month)"
                            class="border-primary-subtle"
                        />
                    </div>

                    <div class="flex-grow-1">
                        <x-form-input
                            name="year"
                            type="select"
                            label="Tahun"
                            :options="$yearOptions"
                            :value="request('year', now()->year)"
                            class="border-primary-subtle"
                        />
                    </div>

                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-filter me-1"></i>Terapkan Filter
                    </button>
                </form>
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

        {{-- TAB CONTENT --}}
        @php
            $currentTab = request('tab', 'pending');
        @endphp

        @if($currentTab === 'pending')
            {{-- TAB 1: PENDING PAYMENTS --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pembayaran Menunggu Verifikasi</h5>
                    <span class="badge bg-warning rounded-pill text-dark">{{ $payments->total() ?? 0 }} item</span>
                </div>
                <div class="card-body p-0">
                    @if (isset($payments) && $payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tipe Pembayaran</th>
                                        <th>Metode</th>
                                        <th>Nominal</th>
                                        <th>Status Pembayaran</th>
                                        <th>Tanggal Upload</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        @php
                                            $isDP = in_array($payment->payment_channel, [\App\Models\Payment::CHANNEL_MANUAL_DP, \App\Models\Payment::CHANNEL_MIDTRANS]);
                                            $totalOrder = $payment->order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                                            $dpAmount = round($totalOrder * 50 / 100, 2);
                                            $displayAmount = $isDP ? $dpAmount : $totalOrder - $dpAmount;
                                            $isFull = $payment->payment_status === \App\Models\Payment::STATUS_FULL_PENDING;
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
                                                @if ($isFull)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-hourglass-split me-1"></i>Pelunasan Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-hourglass-split me-1"></i>DP Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $payment->created_at?->format('d M Y H:i') ?? '-' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye me-1"></i>Lihat
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
        @else
            {{-- TAB 2: PAYMENT HISTORY --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Pembayaran</h5>
                    <span class="badge bg-info rounded-pill">{{ $payments->total() ?? 0 }} item</span>
                </div>
                <div class="card-body p-0">
                    @if (isset($payments) && $payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tipe Pembayaran</th>
                                        <th>Nominal</th>
                                        <th>Status Pembayaran</th>
                                        <th>Tanggal Verifikasi</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
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
                                                    <i class="bi bi-eye me-1"></i>Lihat
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
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Belum ada riwayat pembayaran</h5>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection