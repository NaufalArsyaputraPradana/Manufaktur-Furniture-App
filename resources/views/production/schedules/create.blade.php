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
                        <x-form-input
                            name="title"
                            label="Judul Jadwal"
                            type="text"
                            required
                        />

                        <x-form-input
                            name="description"
                            label="Deskripsi"
                            type="textarea"
                            rows="4"
                        />

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-form-input
                                    name="start_datetime"
                                    label="Waktu Mulai"
                                    type="datetime-local"
                                    required
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form-input
                                    name="end_datetime"
                                    label="Waktu Selesai"
                                    type="datetime-local"
                                    required
                                />
                            </div>
                        </div>

                        <x-form-input
                            name="location"
                            label="Lokasi"
                            type="text"
                            placeholder="Contoh: Workshop 1"
                        />

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
