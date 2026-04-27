@extends('layouts.production')

@section('title', 'Detail Jadwal Produksi')

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
                    <h2 class="fw-bold mb-2 text-shadow">Detail Jadwal Produksi</h2>
                    <p class="text-white text-opacity-90 mb-0"><i class="bi bi-calendar-event me-1"></i>Informasi lengkap jadwal ini</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Detail Utama --}}
        <div class="col-12 col-xxl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Informasi
                    Jadwal</h5>
                </div>
                <div class="card-body p-4">
                    @if ($schedule->location)
                        <div class="mb-5">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                            <strong>Lokasi:</strong> {{ $schedule->location }}
                        </div>
                    @endif

                    @if ($schedule->description)
                        <div class="mb-5">
                            <strong>Deskripsi:</strong>
                            <p class="text-muted mt-2" style="white-space: pre-line;">{{ $schedule->description }}
                            </p>
                        </div>
                    @else
                        <p class="text-muted fst-italic">Tidak ada deskripsi.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-xxl-4">
            {{-- Status Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Status</h5>
                </div>
                <div class="card-body">
                    @if ($schedule->isPast())
                        <span class="badge bg-secondary fs-6">Selesai</span>
                    @else
                        <span class="badge bg-success fs-6">Aktif</span>
                    @endif
                </div>
            </div>

            {{-- Informasi Waktu --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-week text-primary me-2"></i>Waktu</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Mulai</small>
                        <strong>{{ $schedule->start_datetime->translatedFormat('d M Y H:i') }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Selesai</small>
                        <strong>{{ $schedule->end_datetime->translatedFormat('d M Y H:i') }}</strong>
                    </div>
                    @if ($schedule->durationInMinutes())
                        <div>
                            <small class="text-muted d-block">Durasi</small>
                            <strong>{{ $schedule->durationInMinutes() }} menit</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Metadata --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Metadata</h5>
                </div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dibuat</span>
                        <span>{{ $schedule->created_at?->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Diperbarui</span>
                        <span>{{ $schedule->updated_at?->translatedFormat('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Export Card --}}
            <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-calendar-plus fs-3 text-info"></i>
                        <div>
                            <h6 class="fw-bold">Export ke Kalender</h6>
                            <p class="small mb-2">Import file .ics ke Google Calendar atau aplikasi kalender
                            lainnya.</p>
                            <a href="{{ route('staff.production.schedules.export-ics', $schedule) }}"
                            class="btn btn-sm btn-outline-info">
                            <i class="bi bi-download me-1"></i>Export .ics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
