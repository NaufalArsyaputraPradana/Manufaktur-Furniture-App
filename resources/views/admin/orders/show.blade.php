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
                                $hasProof = !empty($order->payment->payment_proof);
                                $needsVerify = ($payStatus === 'unpaid' && $hasProof);
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Status</span>
                                @if ($payStatus === 'paid')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif ($payStatus === 'failed')
                                    <span class="badge bg-danger">Gagal/Ditolak</span>
                                @elseif ($needsVerify)
                                    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                @else
                                    <span class="badge bg-secondary">Belum Dibayar</span>
                                @endif
                            </div>
                            @if ($order->payment->payment_method)
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted small">Metode</span>
                                    <strong class="small">
                                        @if ($order->payment->payment_method === 'transfer') Transfer Bank
                                        @elseif ($order->payment->payment_method === 'credit_card') Kartu Kredit
                                        @elseif ($order->payment->payment_method === 'cash') Tunai
                                        @else {{ ucfirst($order->payment->payment_method) }}
                                        @endif
                                    </strong>
                                </div>
                            @endif
                            @if ($order->payment->amount)
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted small">Nominal</span>
                                    <strong class="text-success">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                            @if ($order->payment->payment_date)
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted small">Tgl. Bayar</span>
                                    <strong class="small">{{ $order->payment->payment_date->format('d M Y, H:i') }}</strong>
                                </div>
                            @endif
                            @if ($hasProof)
                                <div class="mt-3">
                                    <label class="small text-muted fw-bold d-block mb-2">Bukti Transfer</label>
                                    <a href="javascript:void(0)" onclick="showImageModal('{{ asset('storage/' . $order->payment->payment_proof) }}', 'Bukti Pembayaran')" class="d-block mb-2">
                                        <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti" class="img-fluid rounded border" style="max-height:120px; object-fit:contain; cursor:pointer;">
                                    </a>
                                    @if ($needsVerify)
                                        <div class="d-flex gap-2 mt-2">
                                            <a href="{{ route('admin.payments.show', $order->payment) }}" class="btn btn-sm btn-primary flex-grow-1">
                                                <i class="bi bi-eye me-1"></i>Lihat & Verifikasi
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <p class="text-muted small mb-0">Belum ada data pembayaran untuk pesanan ini.</p>
                        @endif
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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