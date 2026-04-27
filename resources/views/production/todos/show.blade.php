@extends('layouts.production')

@section('title', 'Detail Tugas Produksi')

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
                    <h2 class="fw-bold mb-2 text-shadow">Detail Tugas Produksi</h2>
                    <p class="text-white text-opacity-90 mb-0"><i class="bi bi-clipboard-check me-1"></i>Informasi lengkap tentang tugas ini</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <a href="{{ route('staff.production.todos.index') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Detail Tugas --}}
        <div class="col-12 col-xxl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Detail Tugas
                    </h5>
                    <span class="{{ $todo->status_badge_class }}">{{ $todo->status_label }}</span>
                </div>
                <div class="card-body p-4">
                    @if ($todo->description)
                        <div class="mb-5">
                            <strong>Deskripsi:</strong>
                            <p class="text-muted mt-2" style="white-space: pre-line;">{{ $todo->description }}</p>
                        </div>
                    @else
                        <p class="text-muted fst-italic">Tidak ada deskripsi.</p>
                    @endif

                    @if ($todo->deadline)
                        <div class="mb-5">
                            <strong>Deadline:</strong>
                            <p class="text-{{ $todo->isOverdue() ? 'danger' : 'dark' }} fw-bold">
                            {{ $todo->deadline->translatedFormat('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Metadata & Actions --}}
        <div class="col-12 col-xxl-4">
            {{-- Status Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dibuat</span>
                        <strong>{{ $todo->created_at?->translatedFormat('d M Y H:i') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Diperbarui</span>
                        <strong>{{ $todo->updated_at?->translatedFormat('d M Y H:i') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <span class="{{ $todo->status_badge_class }}">{{ $todo->status_label }}</span>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card border-0 shadow-sm rounded-4 border-danger">
                <div class="card-header bg-danger text-white border-0 py-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-danger small">Menghapus tugas ini bersifat permanen.</p>
                    <form action="{{ route('staff.production.todos.destroy', $todo) }}" method="POST"
                    onsubmit="return confirm('Hapus tugas ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-trash me-2"></i>Hapus
                    Tugas</button>
                </form>
            </div>
        </div>

        @if ($todo->isOverdue())
            <div class="alert alert-danger rounded-4 shadow-sm mt-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Tugas terlambat!</strong> Segera selesaikan.
            </div>
        @endif
    </div>
</div>
</div>
@endsection
