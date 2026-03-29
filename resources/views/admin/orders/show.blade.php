@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->order_number)

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15); }
        .cursor-pointer { cursor: pointer; }
        .thumb-img { transition: all 0.2s ease; }
        .thumb-img:hover { opacity: 0.8; transform: scale(1.05); }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold mb-0">Order #{{ $order->order_number }}</h3>
                    <span class="badge bg-{{ $order->status_color }} fs-6 rounded-pill px-3 shadow-sm">
                        {{ $order->status_label }}
                    </span>
                </div>
                <nav aria-label="breadcrumb" class="mt-1">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Pesanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @if ($order->status == 'pending')
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i class="bi bi-check-circle me-1"></i>Konfirmasi
                        </button>
                    </form>
                @endif

                @if (!in_array($order->status, ['cancelled', 'completed']))
                    <button type="button" class="btn btn-danger shadow-sm" onclick="cancelOrder()">
                        <i class="bi bi-x-circle me-1"></i>Batalkan
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                        <i class="bi bi-pencil-square me-1"></i>Update Status
                    </button>
                @endif

                @if ($order->orderDetails->contains('is_custom', true))
                    <a href="{{ route('admin.custom-orders.index', ['order_id' => $order->id]) }}" class="btn btn-info shadow-sm">
                        <i class="bi bi-calculator me-1"></i>Hitung Harga Custom
                    </a>
                @endif

                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- Items Table -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-basket me-2"></i>Item Pesanan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Produk</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                                @if ($item->is_custom && !empty($item->custom_specifications['description']))
                                                    <div class="text-muted small fst-italic">
                                                        <i class="bi bi-info-circle me-1"></i>Spek: {{ Str::limit($item->custom_specifications['description'], 60) }}
                                                    </div>
                                                @endif
                                                @if ($item->is_custom && !empty($item->custom_specifications['design_image']))
                                                    <a href="javascript:void(0)" onclick="showImageModal('{{ asset('storage/' . $item->custom_specifications['design_image']) }}', 'Desain Custom')"
                                                        class="badge bg-secondary text-decoration-none mt-1 shadow-sm">
                                                        <i class="bi bi-image me-1"></i>Lihat Desain
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->is_custom)
                                                    <span class="badge bg-warning text-dark border border-warning-subtle">Custom</span>
                                                @else
                                                    <span class="badge bg-info text-white border border-info-subtle">Katalog</span>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end pe-4 fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold fs-5 text-primary py-3">Total Pembayaran</td>
                                        <td class="text-end pe-4 fw-bold fs-5 text-primary py-3">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase small">Catatan Pelanggan</h6>
                                <div class="text-muted fst-italic p-3 bg-light rounded border" style="min-height: 100px;">
                                    {{ $order->customer_notes ?? 'Tidak ada catatan dari pelanggan.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase small">Catatan Admin (Riwayat)</h6>
                                <div class="text-muted p-3 bg-light rounded border" style="white-space: pre-line; min-height: 100px; font-size: 0.85rem;">
                                    {{ $order->admin_notes ?? 'Belum ada catatan internal.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info Area -->
            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-circle me-2 text-primary"></i>Informasi Pelanggan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold shadow-sm"
                                style="width: 50px; height: 50px; font-size: 1.2rem;">
                                {{ strtoupper(substr($order->user?->name ?? 'G', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $order->user?->name ?? 'Guest' }}</h6>
                                <small class="text-muted">{{ $order->user?->email ?? '-' }}</small>
                            </div>
                        </div>
                        <hr class="opacity-10">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">Alamat Pengiriman</label>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;">{{ $order->shipping_address ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">No. Telepon / WhatsApp</label>
                            <p class="mb-0 text-dark fw-semibold">{{ $order->user?->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Pembayaran</h6>
                    </div>
                    <div class="card-body p-4">
                        @if ($order->payment)
                            @php
                                $payStatus = $order->payment->payment_status;
                                $dpProof = $order->payment->payment_proof_dp;
                                $fullProof = $order->payment->payment_proof_full;
                                $legacyProof = $order->payment->payment_proof;
                                
                                // Tentukan proof mana yang digunakan (legacy atau yang baru)
                                $dpProofToShow = $dpProof ?: ($payStatus === \App\Models\Payment::STATUS_DP_PAID ? $legacyProof : null);
                                $fullProofToShow = $fullProof ?: ($payStatus === \App\Models\Payment::STATUS_PAID && !$dpProof ? $legacyProof : null);
                                
                                // Hitung nominal DP dan sisa pembayaran
                                $totalOrder = $order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                                $dpPercent = 50;
                                $dpAmount = round($totalOrder * $dpPercent / 100, 2);
                                $remainingAmount = $totalOrder - $dpAmount;
                                
                                $needsVerify = in_array($payStatus, [\App\Models\Payment::STATUS_PENDING], true) && ($dpProofToShow || $fullProofToShow);
                            @endphp
                            
                            <!-- Status Section -->
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <span class="text-muted small fw-bold">STATUS PEMBAYARAN</span>
                                @if ($payStatus === \App\Models\Payment::STATUS_PAID)
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>LUNAS
                                    </span>
                                @elseif ($payStatus === \App\Models\Payment::STATUS_DP_PAID)
                                    <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                        <i class="bi bi-hourglass-split me-1"></i>DP VERIFIED
                                    </span>
                                @elseif ($payStatus === \App\Models\Payment::STATUS_FAILED)
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i>GAGAL
                                    </span>
                                @elseif ($needsVerify)
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                        <i class="bi bi-clock me-1"></i>VERIFIKASI
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6 px-3 py-2">
                                        <i class="bi bi-question-circle me-1"></i>MENUNGGU
                                    </span>
                                @endif
                            </div>

                            <!-- Order Summary -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between py-2 border-bottom small">
                                    <span class="text-muted">Total Pesanan</span>
                                    <strong class="text-dark">Rp {{ number_format($totalOrder, 0, ',', '.') }}</strong>
                                </div>
                                @if ($order->payment->payment_method)
                                    <div class="d-flex justify-content-between py-2 border-bottom small">
                                        <span class="text-muted">Metode Pembayaran</span>
                                        <strong>
                                            @if ($order->payment->payment_method === 'transfer') 
                                                <i class="bi bi-bank me-1"></i>Transfer Bank
                                            @elseif ($order->payment->payment_method === 'credit_card') 
                                                <i class="bi bi-credit-card me-1"></i>Kartu Kredit
                                            @elseif ($order->payment->payment_method === 'cash') 
                                                <i class="bi bi-coin me-1"></i>Tunai
                                            @else 
                                                {{ ucfirst($order->payment->payment_method) }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                                @if ($order->payment->payment_date)
                                    <div class="d-flex justify-content-between py-2 border-bottom small">
                                        <span class="text-muted">Tgl. Pembayaran</span>
                                        <strong>{{ $order->payment->payment_date->format('d M Y, H:i') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <!-- Payment Breakdown -->
                            @if ($payStatus === \App\Models\Payment::STATUS_DP_PAID || $payStatus === \App\Models\Payment::STATUS_PAID)
                                <div class="alert alert-light border rounded-3 p-3 mb-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">DP (50%)</small>
                                                <strong class="text-success fs-5">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong>
                                                @if ($dpProofToShow || ($payStatus === \App\Models\Payment::STATUS_PAID && !$dpProofToShow && $legacyProof))
                                                    <span class="badge bg-success-light text-success small ms-2">
                                                        <i class="bi bi-check me-1"></i>Terverifikasi
                                                    </span>
                                                @elseif ($needsVerify && $dpProofToShow)
                                                    <span class="badge bg-warning-light text-warning small ms-2">
                                                        <i class="bi bi-clock me-1"></i>Verifikasi
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Sisa (50%)</small>
                                                @if ($payStatus === \App\Models\Payment::STATUS_PAID)
                                                    <strong class="text-success fs-5">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong>
                                                    <span class="badge bg-success-light text-success small ms-2">
                                                        <i class="bi bi-check me-1"></i>Terverifikasi
                                                    </span>
                                                @else
                                                    <strong class="text-warning fs-5">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong>
                                                    <span class="badge bg-secondary-light text-secondary small ms-2">
                                                        <i class="bi bi-clock me-1"></i>Belum
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if ($payStatus === \App\Models\Payment::STATUS_PAID)
                                        <div class="border-top pt-3 mt-3">
                                            <small class="text-muted d-block mb-1">Total Dibayar</small>
                                            <strong class="text-success fs-6">Rp {{ number_format($totalOrder, 0, ',', '.') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Bukti Pembayaran Section -->
                            <div class="mt-4 pt-3">
                                <h6 class="text-muted fw-bold mb-3">
                                    <i class="bi bi-file-earmark-image me-2"></i>BUKTI PEMBAYARAN
                                </h6>

                                <!-- DP Proof -->
                                @if ($dpProofToShow)
                                    @php
                                        $dpProofPath = null;
                                        $proof = $dpProofToShow;
                                        if (str_starts_with($proof, 'storage/')) {
                                            $dpProofPath = asset($proof);
                                        } elseif (str_starts_with($proof, '/')) {
                                            $dpProofPath = asset('storage' . $proof);
                                        } else {
                                            $dpProofPath = asset('storage/' . $proof);
                                        }
                                    @endphp
                                    <div class="mb-4 p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="small fw-bold text-dark me-2">
                                                <i class="bi bi-file-earmark-check text-success me-1"></i>Bukti Transfer DP (50%)
                                            </span>
                                            <span class="badge bg-success small">TERVERIFIKASI</span>
                                        </div>
                                        @if ($dpProofPath)
                                            <div class="text-center">
                                                <img src="{{ $dpProofPath }}" alt="Bukti Transfer DP" 
                                                    class="img-fluid rounded border" 
                                                    style="max-height:280px; width:auto; object-fit:contain; cursor:pointer; transition: all 0.3s ease;" 
                                                    onclick="showImageModal('{{ $dpProofPath }}', 'Bukti Transfer DP')"
                                                    onmouseover="this.style.opacity='0.8'; this.style.transform='scale(1.02)'" 
                                                    onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Full Payment Proof -->
                                @if ($fullProofToShow)
                                    @php
                                        $fullProofPath = null;
                                        $proof = $fullProofToShow;
                                        if (str_starts_with($proof, 'storage/')) {
                                            $fullProofPath = asset($proof);
                                        } elseif (str_starts_with($proof, '/')) {
                                            $fullProofPath = asset('storage' . $proof);
                                        } else {
                                            $fullProofPath = asset('storage/' . $proof);
                                        }
                                    @endphp
                                    <div class="mb-4 p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="small fw-bold text-dark me-2">
                                                <i class="bi bi-file-earmark-check text-success me-1"></i>Bukti Transfer Pelunasan (50% Sisa)
                                            </span>
                                            <span class="badge bg-success small">TERVERIFIKASI</span>
                                        </div>
                                        @if ($fullProofPath)
                                            <div class="text-center">
                                                <img src="{{ $fullProofPath }}" alt="Bukti Transfer Pelunasan" 
                                                    class="img-fluid rounded border" 
                                                    style="max-height:280px; width:auto; object-fit:contain; cursor:pointer; transition: all 0.3s ease;" 
                                                    onclick="showImageModal('{{ $fullProofPath }}', 'Bukti Transfer Pelunasan')"
                                                    onmouseover="this.style.opacity='0.8'; this.style.transform='scale(1.02)'" 
                                                    onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Verification Action -->
                                @if ($needsVerify)
                                    <div class="mt-3">
                                        <a href="{{ route('admin.payments.show', $order->payment) }}" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-eye me-1"></i>Lihat & Verifikasi Pembayaran
                                        </a>
                                    </div>
                                @elseif ($payStatus === \App\Models\Payment::STATUS_DP_PAID)
                                    <div class="alert alert-info alert-sm py-2 px-3 small mb-0 mt-2">
                                        <i class="bi bi-info-circle me-1"></i><strong>Info:</strong> DP telah terverifikasi. Menunggu pembayaran sisa pesanan (50%).
                                    </div>
                                @elseif ($payStatus === \App\Models\Payment::STATUS_PAID)
                                    <div class="alert alert-success alert-sm py-2 px-3 small mb-0 mt-2">
                                        <i class="bi bi-check-circle me-1"></i><strong>Selesai:</strong> Pesanan telah lunas. Semua pembayaran telah diverifikasi.
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning alert-sm py-2 px-3 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>Belum ada data pembayaran untuk pesanan ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-truck me-2 text-primary"></i>Pengiriman</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.orders.shipping', $order) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PATCH')
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status kirim</label>
                                <select name="shipping_status" class="form-select">
                                    <option value="">— Belum diatur —</option>
                                    <option value="processing" {{ old('shipping_status', $order->shipping_status) === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ old('shipping_status', $order->shipping_status) === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ old('shipping_status', $order->shipping_status) === 'delivered' ? 'selected' : '' }}>Sampai</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Ekspedisi</label>
                                <input type="text" name="courier" class="form-control" value="{{ old('courier', $order->courier) }}" placeholder="JNE, SiCepat, dll.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">No. resi</label>
                                <input type="text" name="tracking_number" class="form-control" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Nomor resi">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-save me-1"></i>Simpan pengiriman
                                </button>
                                @php
                                    $trackUrl = app(\App\Support\CourierTrackingService::class)->publicTrackingUrl($order->courier, $order->tracking_number);
                                @endphp
                                @if ($trackUrl)
                                    <a href="{{ $trackUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Lacak pengiriman
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Timeline / Logistik Info -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Timeline Pesanan</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small">Tanggal Order</span>
                                <span class="fw-bold text-dark small">{{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small">Estimasi Selesai</span>
                                <span class="fw-bold text-primary small">{{ $order->expected_completion_date ? $order->expected_completion_date->format('d M Y') : '-' }}</span>
                            </li>
                            @if ($order->actual_completion_date)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-success bg-opacity-10">
                                    <span class="text-success fw-bold small">Selesai Realisasi</span>
                                    <span class="text-success fw-bold small">{{ $order->actual_completion_date->format('d M Y') }}</span>
                                </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted small">Waktu Pembuatan Data</span>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $order->created_at->diffForHumans() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="updateStatusModalLabel">Update Status Pesanan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label fw-bold">Status Baru</label>
                            <select name="status" id="statusSelect" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed (Dikonfirmasi)</option>
                                <option value="in_production" {{ $order->status == 'in_production' ? 'selected' : '' }}>In Production (Produksi)</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                                <option value="on_hold" {{ $order->status == 'on_hold' ? 'selected' : '' }}>On Hold (Ditahan)</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="notesTextarea" class="form-label fw-bold">Catatan Perubahan <small class="text-muted fw-normal">(Opsional)</small></label>
                            <textarea name="notes" id="notesTextarea" class="form-control" rows="3" placeholder="Masukkan alasan atau detail perubahan status..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Cancel Hidden -->
    <form id="cancelOrderForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-none">
        @csrf
        @method('PATCH')
        <input type="hidden" name="reason" id="cancelReason">
    </form>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fw-bold" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <img id="imagePreviewContent" src="" alt="Bukti Pembayaran" class="img-fluid" style="max-height: 90vh; object-fit: contain;">
                </div>
                <div class="modal-footer bg-light py-2">
                    <a id="imageDownloadLink" href="" download class="btn btn-sm btn-primary" title="Download gambar">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi untuk menampilkan gambar dalam modal
        function showImageModal(imageSrc, title = 'Bukti Pembayaran') {
            const modal = document.getElementById('imagePreviewModal');
            const imageElement = document.getElementById('imagePreviewContent');
            const titleElement = document.getElementById('imagePreviewModalLabel');
            const downloadLink = document.getElementById('imageDownloadLink');
            
            // Set gambar dan title
            imageElement.src = imageSrc;
            titleElement.textContent = title;
            downloadLink.href = imageSrc;
            
            // Buka modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        function cancelOrder() {
            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Pesanan ini akan dibatalkan secara permanen!",
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Masukkan alasan pembatalan...',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value === "") {
                        Swal.fire('Error', 'Alasan pembatalan wajib diisi!', 'error');
                        return;
                    }
                    document.getElementById('cancelReason').value = result.value;
                    document.getElementById('cancelOrderForm').submit();
                }
            });
        }
    </script>
@endpush