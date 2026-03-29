@extends('layouts.production')

@section('title', 'Pengiriman · #' . $order->order_number)

@push('styles')
<style>
    .ship-timeline { position: relative; padding-left: 0; list-style: none; }
    .ship-timeline::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--prod-primary, #0d6efd) 0%, rgba(13,110,253,.15) 100%);
        border-radius: 4px;
    }
    .ship-timeline-item { position: relative; padding-left: 52px; padding-bottom: 1.5rem; }
    .ship-timeline-item:last-child { padding-bottom: 0; }
    .ship-timeline-icon {
        position: absolute;
        left: 0;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 2px solid rgba(13,110,253,.35);
        color: var(--prod-primary, #0d6efd);
        box-shadow: 0 2px 8px rgba(13,110,253,.12);
        z-index: 1;
    }
    .ship-doc-thumb { max-height: 160px; object-fit: contain; border-radius: .5rem; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('production.shipping.index') }}" class="text-decoration-none">Monitoring Pengiriman</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">#{{ $order->order_number }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Alur pengiriman</h4>
            <p class="text-muted mb-0 small">
                Catat muat barang, serah kurir, dan lampirkan bukti foto untuk jejak yang jelas.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('production.shipping.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Daftar
            </a>
            <a href="{{ route('production.monitoring.order.show', $order) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i>Detail order
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Ringkasan</h6>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Order</dt>
                        <dd class="col-7 fw-semibold">#{{ $order->order_number }}</dd>
                        <dt class="col-5 text-muted">Pelanggan</dt>
                        <dd class="col-7">{{ $order->user?->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Status pesanan</dt>
                        <dd class="col-7"><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></dd>
                        <dt class="col-5 text-muted">Pengiriman</dt>
                        <dd class="col-7">{{ $order->shipping_status_label }}</dd>
                    </dl>
                    @if ($order->shipping_address)
                        <hr>
                        <div class="small">
                            <span class="text-muted d-block mb-1">Alamat kirim</span>
                            <span class="text-dark">{{ $order->shipping_address }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2 text-primary"></i>Kurir &amp; resi</h6>
                    <form action="{{ route('production.shipping.courier.update', $order) }}" method="POST" class="small">
                        @csrf
                        @method('PATCH')
                        <div class="mb-2">
                            <label class="form-label text-muted mb-0">Ekspedisi / kurir</label>
                            <input type="text" name="courier" class="form-control form-control-sm"
                                value="{{ old('courier', $order->courier) }}" placeholder="Contoh: JNE, SiCepat">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted mb-0">No. resi</label>
                            <input type="text" name="tracking_number" class="form-control form-control-sm"
                                value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Nomor pelacakan">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Simpan kurir &amp; resi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle text-success me-2"></i>Tambah catatan pengiriman</h5>
                    <p class="text-muted small mb-0 mt-1">Pilih tahapan, tulis keterangan, unggah foto muat atau bukti serah.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('production.shipping.logs.store', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tahapan <span class="text-danger">*</span></label>
                                <select name="stage" class="form-select @error('stage') is-invalid @enderror" required>
                                    <option value="">— Pilih —</option>
                                    @foreach ($stageLabels as $key => $label)
                                        <option value="{{ $key }}" @selected(old('stage') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('stage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Foto / dokumen</label>
                                <input type="file" name="documentation" accept="image/*,.pdf"
                                    class="form-control @error('documentation') is-invalid @enderror">
                                <div class="form-text">JPG, PNG, WebP, atau PDF (maks. 5 MB)</div>
                                @error('documentation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Contoh: Muat 3 paket ke armada, dicek kelengkapan…">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama kurir (opsional, sinkron ke order)</label>
                                <input type="text" name="courier_note" class="form-control form-control-sm"
                                    value="{{ old('courier_note') }}" placeholder="Isi jika ingin memperbarui sekalian">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. resi (opsional)</label>
                                <input type="text" name="tracking_note" class="form-control form-control-sm"
                                    value="{{ old('tracking_note') }}" placeholder="Isi jika ada resi baru">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-check2-circle me-1"></i>Simpan catatan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat</h5>
                    <span class="badge bg-secondary rounded-pill">{{ $logs->count() }} entri</span>
                </div>
                <div class="card-body">
                    @if ($logs->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-image fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada catatan. Mulai dengan tahapan <strong>Persiapan &amp; muat barang</strong> dan lampirkan foto.
                        </div>
                    @else
                        <ul class="ship-timeline mb-0">
                            @foreach ($logs as $log)
                                @php
                                    $icon = $stageIcons[$log->stage] ?? 'bi-circle';
                                @endphp
                                <li class="ship-timeline-item">
                                    <div class="ship-timeline-icon">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                    <div class="card border-0 bg-light bg-opacity-50 rounded-4">
                                        <div class="card-body p-3">
                                            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start mb-2">
                                                <div>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $log->stage_label }}</span>
                                                    <span class="text-muted small ms-2">{{ $log->created_at->format('d M Y · H:i') }}</span>
                                                </div>
                                                @if ($log->recordedBy)
                                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $log->recordedBy->name }}</span>
                                                @endif
                                            </div>
                                            @if ($log->notes)
                                                <p class="mb-2 small text-dark">{{ $log->notes }}</p>
                                            @endif
                                            @if ($log->courier_note || $log->tracking_note)
                                                <div class="small text-muted mb-2">
                                                    @if ($log->courier_note)
                                                        <span class="me-3"><i class="bi bi-truck me-1"></i>{{ $log->courier_note }}</span>
                                                    @endif
                                                    @if ($log->tracking_note)
                                                        <span><i class="bi bi-upc-scan me-1"></i>{{ $log->tracking_note }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if ($log->documentation)
                                                @php $url = asset('storage/' . $log->documentation); $isPdf = str_ends_with(strtolower($log->documentation), '.pdf'); @endphp
                                                @if ($isPdf)
                                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i>Buka PDF
                                                    </a>
                                                @else
                                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="d-inline-block">
                                                        <img src="{{ $url }}" alt="Bukti" class="ship-doc-thumb border bg-white">
                                                    </a>
                                                @endif
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
@endsection
