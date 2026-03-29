@extends('layouts.production')

@section('title', 'Edit Proses Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Proses Produksi</h4>
            <p class="text-muted mb-0 small">Perbarui data proses produksi</p>
        </div>
        <a href="{{ route('production.tracking.show', $process) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

            {{-- Order Info Banner --}}
            <div
                class="alert alert-info bg-opacity-10 border-info d-flex align-items-center gap-3 mb-4 rounded-4 shadow-sm">
                <div class="bg-primary rounded-circle p-3 text-white">
                    <i class="bi bi-box-seam-fill fs-4"></i>
                </div>
                <div>
                    <strong class="d-block">Order #{{ $process->order?->order_number ?? 'N/A' }}</strong>
                    @if ($process->order?->user)
                        <span><i class="bi bi-person-circle me-1"></i>{{ $process->order->user->name }}</span>
                    @endif
                    <span class="mx-2">-</span>
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $process->created_at?->format('d M Y') ?? 'â€“' }}</span>
                </div>
                <span class="badge bg-{{ $process->status_color }} ms-auto">{{ $process->status_label }}</span>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-gear-fill text-primary me-2"></i>Detail Proses</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('production.processes.update', $process) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <x-form-input 
                                    name="stage" 
                                    label="Tahap Produksi"
                                    type="select"
                                    :options="collect(['', '-- Pilih Tahap --'])->union(collect(\App\Models\ProductionProcess::STAGE_LABELS)->mapWithKeys(function($label, $val) { return [$val => $label]; }))"
                                    :value="old('stage', $process->stage)"
                                    :errors="$errors"
                                    required />
                            </div>

                            <div class="col-md-6">
                                <x-form-input 
                                    name="status" 
                                    label="Status"
                                    type="select"
                                    :options="['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Selesai', 'paused' => 'Dijeda', 'issue' => 'Ada Masalah']"
                                    :value="old('status', $process->status)"
                                    :errors="$errors"
                                    required />
                            </div>

                            <div class="col-12">
                                <x-form-input 
                                    name="notes" 
                                    label="Catatan Lapangan"
                                    type="textarea"
                                    :value="old('notes', $process->notes)"
                                    placeholder="Instruksi atau catatan khusus (opsional)"
                                    rows="3"
                                    :errors="$errors" />
                            </div>

                            <div class="col-12">
                                <label for="documentation" class="form-label fw-bold">Dokumentasi (opsional)</label>
                                <input type="file" class="form-control @error('documentation') is-invalid @enderror"
                                    id="documentation" name="documentation" accept=".jpg,.jpeg,.png,.pdf" onchange="previewDocumentation(event)">
                                <small class="text-muted">Format: JPG, PNG, PDF. Maks 5 MB.</small>
                                @error('documentation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    @if ($process->documentation && (preg_match('/\.(jpg|jpeg|png)$/i', $process->documentation)))
                                        <div class="d-inline-block position-relative" style="max-width: 120px;">
                                            <img src="{{ asset('storage/' . $process->documentation) }}" alt="Dokumentasi" class="img-thumbnail shadow-sm documentation-preview-img" style="cursor:pointer; max-width:120px; max-height:120px;" data-src="{{ asset('storage/' . $process->documentation) }}">
                                            <div class="text-center small mt-1 text-success">File tersimpan</div>
                                            <div class="text-center small text-muted">Klik gambar untuk preview</div>
                                        </div>
                                    @elseif ($process->documentation)
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> File tersimpan:</span>
                                        <a href="{{ asset('storage/' . $process->documentation) }}" target="_blank" class="text-primary">Lihat file</a>
                                        <small class="text-muted">(unggah file baru untuk mengganti)</small>
                                    @endif
                                    <div id="documentationPreviewContainer" class="mt-2"></div>
                                </div>
                                <!-- Modal for documentation preview -->
                                <div class="modal fade" id="documentationModal" tabindex="-1" aria-labelledby="documentationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="documentationModalLabel">Preview Dokumentasi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img id="documentationModalImg" src="" alt="Dokumentasi" class="img-fluid rounded shadow">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function previewDocumentation(event) {
                                        const file = event.target.files[0];
                                        const container = document.getElementById('documentationPreviewContainer');
                                        container.innerHTML = '';
                                        if (file && file.type.match('image.*')) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                container.innerHTML = `<div class='d-inline-block position-relative' style='max-width:120px;'><img src='${e.target.result}' alt='Preview' class='img-thumbnail shadow-sm documentation-preview-img' style='cursor:pointer; max-width:120px; max-height:120px;' data-src='${e.target.result}'><div class='text-center small text-primary'>Preview baru</div><div class='text-center small text-muted'>Klik gambar untuk preview</div></div>`;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    }
                                    // Event delegation for modal preview
                                    document.addEventListener('click', function(e) {
                                        if (e.target.classList.contains('documentation-preview-img')) {
                                            var src = e.target.getAttribute('data-src');
                                            document.getElementById('documentationModalImg').src = src;
                                            var modal = new bootstrap.Modal(document.getElementById('documentationModal'));
                                            modal.show();
                                        }
                                    });
                                </script>
                                </script>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('production.processes.show', $process) }}"
                                class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection
