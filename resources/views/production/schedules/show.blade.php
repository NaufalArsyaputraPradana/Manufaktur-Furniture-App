@extends('layouts.production')

@section('title', 'Detail Jadwal Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Jadwal Produksi</h4>
            <p class="text-muted mb-0 small">Informasi lengkap jadwal ini</p>
        </div>
        <a href="{{ route('staff.production.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

            <div class="row g-4">
                {{-- Detail Utama --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Informasi
                                Jadwal</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($schedule->location)
                                <div class="mb-3">
                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                    <strong>Lokasi:</strong> {{ $schedule->location }}
                                </div>
                            @endif

                            @if ($schedule->description)
                                <div class="mb-3">
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
                <div class="col-lg-4">
                    {{-- Status Card --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
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
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
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
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
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
