@extends('layouts.production')

@section('title', 'Buat Jadwal Produksi')

@push('styles')
<style>
    :root { --prod-primary: #10b981; --prod-secondary: #059669; }
    .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
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
                    <h2 class="fw-bold mb-2 text-shadow">Buat Jadwal Produksi</h2>
                    <p class="text-white text-opacity-90 mb-0"><i class="bi bi-calendar-plus me-1"></i>Tambahkan jadwal baru</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-4">
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
