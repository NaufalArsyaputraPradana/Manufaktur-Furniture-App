@extends('layouts.production')

@section('title', 'Tambah Proses Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tambah Proses Produksi</h4>
            <p class="text-muted mb-0 small">Tambahkan tahapan baru ke order ini</p>
        </div>
        <a href="{{ isset($order) ? route('production.tracking.show', $order) : route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

            @if (isset($order) && $order)
                <div
                    class="alert alert-info bg-opacity-10 border-info d-flex align-items-center gap-3 mb-4 rounded-4 shadow-sm">
                    <div class="bg-primary rounded-circle p-3 text-white">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </div>
                    <div>
                        <strong class="d-block">Order #{{ $order->order_number }}</strong>
                        @if ($order->user)
                            <span><i class="bi bi-person-circle me-1"></i>{{ $order->user->name }}</span>
                        @endif
                        <span class="mx-2">-</span>
                        <span><i class="bi bi-box me-1"></i>{{ $order->orderDetails->count() }} item produk</span>
                    </div>
                </div>
            @else
                <div class="alert alert-warning rounded-4 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Order belum dipilih. Akses halaman ini melalui daftar order produksi.
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-gear-fill text-primary me-2"></i>Detail Proses</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('production.processes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($order) && $order)
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        @endif

                        <div class="row g-4">
                            <div class="col-md-6">
                                <x-form-input 
                                    name="stage" 
                                    label="Tahap Produksi"
                                    type="select"
                                    :options="collect(['', '-- Pilih Tahap --'])->union(collect(\App\Models\ProductionProcess::STAGE_LABELS)->mapWithKeys(function($label, $val) { return [$val => $label]; }))"
                                    :value="old('stage')"
                                    :errors="$errors"
                                    required />
                            </div>

                            <div class="col-md-6">
                                <x-form-input 
                                    name="status" 
                                    label="Status Awal"
                                    type="select"
                                    :options="['pending' => 'Menunggu (Pending)', 'in_progress' => 'Sedang Dikerjakan', 'completed' => 'Selesai']"
                                    :value="old('status', 'pending')"
                                    :errors="$errors"
                                    required />
                            </div>

                            <div class="col-12">
                                <x-form-input 
                                    name="notes" 
                                    label="Catatan Lapangan"
                                    type="textarea"
                                    :value="old('notes')"
                                    placeholder="Instruksi atau catatan khusus (opsional)"
                                    rows="3"
                                    :errors="$errors" />
                            </div>

                            <div class="col-12">
                                <label for="documentation" class="form-label fw-bold">Dokumentasi (opsional)</label>
                                <input type="file" class="form-control @error('documentation') is-invalid @enderror"
                                    id="documentation" name="documentation" accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Format: JPG, PNG, PDF. Maks 5 MB.</small>
                                @error('documentation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                    <div class="mt-3" id="documentationPreview"></div>
                                </div>

                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ url()->previous() }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Proses</button>
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('documentation')?.addEventListener('change', function(e) {
    const preview = document.getElementById('documentationPreview');
    preview.innerHTML = '';
    const file = e.target.files[0];
    if (file && file.type.match('image.*')) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML = `<img src="${ev.target.result}" alt="Preview" class="img-fluid rounded mb-2" style="max-width:300px;max-height:200px;cursor:pointer;" id="imgPreview">`;
            // Modal HTML
            if (!document.getElementById('imgModal')) {
                const modalHtml = `
                <div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="imgModalLabel">Preview Gambar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center">
                        <img src="" id="imgModalContent" class="img-fluid rounded" style="max-width:100%;max-height:80vh;">
                      </div>
                    </div>
                  </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            }
            setTimeout(() => {
                document.getElementById('imgPreview')?.addEventListener('click', function() {
                    document.getElementById('imgModalContent').src = ev.target.result;
                    const modal = new bootstrap.Modal(document.getElementById('imgModal'));
                    modal.show();
                });
            }, 100);
        };
        reader.readAsDataURL(file);
    } else if (file && file.type === 'application/pdf') {
        preview.innerHTML = '<span class="badge bg-secondary">File PDF terupload</span>';
    }
});
</script>
@endpush
