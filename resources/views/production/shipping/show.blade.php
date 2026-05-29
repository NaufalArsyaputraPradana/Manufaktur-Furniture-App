@extends('layouts.production')

@section('title', 'Pengiriman · #' . $order->order_number)

@push('styles')
<style>
    :root { --prod-primary: #10b981; --prod-secondary: #059669; --prod-success: #1cc88a; }
    .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }

    /* Timeline Styling */
    .ship-timeline { position: relative; padding-left: 0; list-style: none; }
    .ship-timeline::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--prod-primary) 0%, rgba(16, 185, 129, 0.15) 100%);
        border-radius: 4px;
    }
    .ship-timeline-item { position: relative; padding-left: 60px; padding-bottom: 2rem; }
    .ship-timeline-item:last-child { padding-bottom: 0; }
    .ship-timeline-icon {
        position: absolute;
        left: 0;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 3px solid var(--prod-primary);
        color: var(--prod-primary);
        box-shadow: 0 2px 12px rgba(78, 115, 223, 0.2);
        z-index: 2;
        font-weight: bold;
    }
    .ship-timeline-item.completed .ship-timeline-icon {
        background: var(--prod-success);
        border-color: var(--prod-success);
        color: white;
    }

    /* Timeline Card */
    .ship-timeline-card {
        border-left: 4px solid var(--prod-primary);
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .ship-timeline-item.completed .ship-timeline-card {
        border-left-color: var(--prod-success);
        background: rgba(28, 200, 138, 0.05);
    }
    .ship-timeline-card:hover { box-shadow: 0 4px 16px rgba(16, 185, 129, 0.12); }

    /* Documentation */
    .ship-doc-thumb { 
        max-height: 160px; 
        object-fit: contain; 
        border-radius: 0.5rem; 
        cursor: pointer;
        transition: transform 0.2s ease;
        border: 2px solid #e9ecef;
    }
    .ship-doc-thumb:hover { transform: scale(1.05); }

    /* Summary Card */
    .summary-row { border-bottom: 1px solid #f0f0f0; padding: 0.75rem 0; }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { color: #6c757d; font-weight: 600; font-size: 0.85rem; }
    .summary-value { font-weight: 600; color: #2d3748; }

    /* Responsive */
    @media (max-width: 1399px) {
        .ship-timeline-item { padding-left: 52px; padding-bottom: 1.75rem; }
    }
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
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb breadcrumb-dark mb-0 small">
                            <li class="breadcrumb-item">
                                <a href="{{ route('production.shipping.index') }}" class="text-white text-opacity-90 text-decoration-none">Monitoring Pengiriman</a>
                            </li>
                            <li class="breadcrumb-item active text-white text-opacity-80" aria-current="page">
                                Order #{{ $order->order_number }}
                            </li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold mb-2 text-shadow">Alur Pengiriman</h2>
                    <p class="text-white text-opacity-90 mb-0">Catat muat barang, serah kurir, dan lampirkan bukti foto untuk jejak yang jelas</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <a href="{{ route('production.shipping.index') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                            <i class="bi bi-arrow-left me-1"></i>Daftar
                        </a>
                        <a href="{{ route('production.monitoring.order.show', $order) }}" class="btn btn-light shadow-sm text-primary fw-bold">
                            <i class="bi bi-eye me-1"></i>Detail Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sidebar: Ringkasan & Kurir --}}
        <div class="col-12 col-xxl-3">
            {{-- Summary Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 hover-lift">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-uppercase text-muted small mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Ringkasan Order
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="summary-row">
                        <div class="summary-label">Nomor Order</div>
                        <div class="summary-value text-primary fw-bold fs-5">#{{ $order->order_number }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Pelanggan</div>
                        <div class="summary-value">{{ $order->user?->name ?? '—' }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Status Order</div>
                        <div>
                            <span class="badge bg-{{ $order->status_color }} badge-lg">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Status Pengiriman</div>
                        <div>
                            <span class="badge bg-{{ match($order->shipping_status instanceof \App\Enums\ShippingStatus ? $order->shipping_status->value : $order->shipping_status) {
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'processing' => 'info',
                                default => 'secondary'
                            } }} badge-lg">
                                {{ $order->shipping_status_label }}
                            </span>
                        </div>
                    </div>
                    @if ($order->shipping_address)
                        <div class="summary-row mt-3 pt-3 border-top">
                            <div class="summary-label">Alamat Pengiriman</div>
                            <div class="summary-value small mt-2">{{ $order->shipping_address }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kurir & Resi Form --}}
            <div class="card border-0 shadow-sm rounded-3 hover-lift">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-uppercase text-muted small mb-0">
                        <i class="bi bi-truck text-primary me-2"></i>Kurir & Resi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('production.shipping.courier.update', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ekspedisi / Kurir</label>
                            <input type="text"
                                name="courier"
                                value="{{ old('courier', $order->courier) }}"
                                placeholder="Contoh: JNE, SiCepat, Grab"
                                class="form-control form-control-sm @error('courier') is-invalid @enderror">
                            @error('courier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nomor Resi</label>
                            <input type="text"
                                name="tracking_number"
                                value="{{ old('tracking_number', $order->tracking_number) }}"
                                placeholder="Nomor pelacakan"
                                class="form-control form-control-sm @error('tracking_number') is-invalid @enderror">
                            @error('tracking_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            <i class="bi bi-check2-circle me-1"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content: Form & Timeline --}}
        <div class="col-12 col-xxl-9">
            {{-- Add New Log Form --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 hover-lift">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-uppercase text-muted small mb-0">
                        <i class="bi bi-plus-circle text-success me-2"></i>Tambah Catatan Pengiriman
                    </h6>
                    <p class="text-muted small mb-0 mt-2">Pilih tahapan, tulis keterangan, dan unggah bukti foto</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('production.shipping.logs.store', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tahapan Pengiriman</label>
                                <select name="stage" class="form-select form-select-sm @error('stage') is-invalid @enderror" required>
                                    <option value="">— Pilih Tahapan —</option>
                                    @foreach($stageLabels as $key => $label)
                                        <option value="{{ $key }}" @selected(old('stage') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('stage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Foto / Dokumen</label>
                                <input type="file" name="documentation[]" accept="image/*" multiple
                                    class="form-control form-control-sm @error('documentation') is-invalid @enderror"
                                    id="docInput">
                                <div class="form-text small">JPG, PNG, WebP (maks. 5 MB per file)</div>
                                <div id="fileCount" class="small text-muted mt-1"></div>
                                @error('documentation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Catatan Pengiriman</label>
                            <textarea name="notes" rows="3" class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                placeholder="Contoh: Muat 3 paket ke armada, dicek kelengkapan…">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Kurir (Opsional)</label>
                                <input type="text" name="courier_note" class="form-control form-control-sm"
                                    placeholder="Nama kurir yang mengangkut"
                                    value="{{ old('courier_note') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No. Resi (Opsional)</label>
                                <input type="text" name="tracking_note" class="form-control form-control-sm"
                                    placeholder="Nomor resi baru"
                                    value="{{ old('tracking_note') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-check2-circle me-1"></i>Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Timeline History --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-uppercase text-muted small mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pengiriman
                    </h6>
                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $logs->count() }} entri</span>
                </div>
                <div class="card-body p-4">
                    @if ($logs->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            <p class="mb-0">Belum ada catatan pengiriman</p>
                            <small>Mulai dengan tahapan <strong>"Persiapan & Muat Barang"</strong> di atas</small>
                        </div>
                    @else
                        <ul class="ship-timeline mb-0">
                        @foreach ($logs as $log)
                            @php
                            $icon = $stageIcons[$log->stage] ?? 'bi-circle';
                            $isCompleted = $log->status === 'completed';
                            @endphp
                            <li class="ship-timeline-item {{ $isCompleted ? 'completed' : '' }}">
                                <div class="ship-timeline-icon">
                                    <i class="bi {{ $icon }}"></i>
                                </div>
                                <div class="card border-0 shadow-sm ship-timeline-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start mb-2 gap-2">
                                            <div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                                    {{ $log->stage_label }}
                                                </span>
                                                <span class="text-muted small ms-2">
                                                    <i class="bi bi-calendar-event me-1"></i>{{ $log->created_at->format('d M Y') }}
                                                </span>
                                                <span class="text-muted small ms-2">
                                                    <i class="bi bi-clock me-1"></i>{{ $log->created_at->format('H:i') }}
                                                </span>
                                            </div>
                                            @if ($log->recordedBy)
                                                <span class="small text-muted">
                                                    <i class="bi bi-person-circle me-1"></i>{{ $log->recordedBy->name }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($log->notes)
                                            <p class="mb-2 small text-dark">{{ $log->notes }}</p>
                                        @endif

                                        @if ($log->courier_note || $log->tracking_note)
                                            <div class="small mb-2 p-2 rounded bg-light">
                                                @if ($log->courier_note)
                                                    <div class="text-muted">
                                                        <i class="bi bi-truck me-1"></i>
                                                        <strong>Kurir:</strong> {{ $log->courier_note }}
                                                    </div>
                                                @endif
                                                @if ($log->tracking_note)
                                                    <div class="text-muted">
                                                        <i class="bi bi-upc-scan me-1"></i>
                                                        <strong>Resi:</strong> {{ $log->tracking_note }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($log->documentation)
                                            @php 
                                            // Handle both old single string and new JSON array format
                                            $docs = [];
                                            $isJsonArray = false;
                                            try {
                                                $decoded = json_decode($log->documentation, true);
                                                if (is_array($decoded) && count($decoded) > 0) {
                                                    $docs = $decoded;
                                                    $isJsonArray = true;
                                                }
                                            } catch (\Exception $e) {}
                                            if (!$isJsonArray) {
                                                $docs = [$log->documentation];
                                            }
                                            @endphp

                                            <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top">
                                                @foreach ($docs as $doc)
                                                    @php
                                                    $url = asset('storage/' . $doc);
                                                    $isPdf = str_ends_with(strtolower($doc), '.pdf');
                                                    @endphp
                                                    @if ($isPdf)
                                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                                        </a>
                                                    @else
                                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="d-inline-block">
                                                            <img src="{{ $url }}" alt="Bukti" class="ship-doc-thumb">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const docInput = document.getElementById('docInput');
    const fileCount = document.getElementById('fileCount');

    if (docInput) {
        docInput.addEventListener('change', function() {
            const count = this.files.length;
            if (count > 0) {
                fileCount.textContent = '✓ ' + count + ' file dipilih';
                fileCount.className = 'small text-success mt-1 fw-semibold';
            } else {
                fileCount.textContent = '';
            }
        });
    }
});
</script>
@endpush
@endsection
