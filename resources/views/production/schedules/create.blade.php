@extends('layouts.production')

@section('title', 'Buat Jadwal Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Buat Jadwal Produksi</h4>
            <p class="text-muted mb-0 small">Tambahkan jadwal baru</p>
        </div>
        <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Jadwal</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.production.schedules.store') }}" method="POST" id="scheduleCreateForm">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Judul Jadwal <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_datetime" class="form-label fw-bold">Waktu Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('start_datetime') is-invalid @enderror" id="start_datetime"
                                    name="start_datetime" value="{{ old('start_datetime') }}" required>
                                @error('start_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_datetime" class="form-label fw-bold">Waktu Selesai <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('end_datetime') is-invalid @enderror" id="end_datetime"
                                    name="end_datetime" value="{{ old('end_datetime') }}" required>
                                @error('end_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="location" class="form-label fw-bold">Lokasi</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                id="location" name="location" value="{{ old('location') }}"
                                placeholder="Contoh: Workshop 1">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('staff.production.schedules.index') }}"
                                class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4" id="scheduleSubmitBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection

@push('scripts')
    <script>
        document.getElementById('scheduleCreateForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('scheduleSubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }
        });
    </script>
@endpush
