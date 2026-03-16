@extends('layouts.production')

@section('title', 'Detail Tugas Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Tugas Produksi</h4>
            <p class="text-muted mb-0 small">Informasi lengkap tentang tugas ini</p>
        </div>
        <a href="{{ route('staff.production.todos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

            <div class="row g-4">
                {{-- Kolom Kiri: Detail Tugas --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Detail Tugas
                            </h5>
                            <span class="{{ $todo->status_badge_class }}">{{ $todo->status_label }}</span>
                        </div>
                        <div class="card-body p-4">
                            @if ($todo->description)
                                <div class="mb-4">
                                    <strong>Deskripsi:</strong>
                                    <p class="text-muted mt-2" style="white-space: pre-line;">{{ $todo->description }}</p>
                                </div>
                            @else
                                <p class="text-muted fst-italic">Tidak ada deskripsi.</p>
                            @endif

                            @if ($todo->deadline)
                                <div class="mb-3">
                                    <strong>Deadline:</strong>
                                    <p class="text-{{ $todo->isOverdue() ? 'danger' : 'dark' }} fw-bold">
                                        {{ $todo->deadline->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Metadata & Actions --}}
                <div class="col-lg-4">
                    {{-- Status Card --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
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
                        <div class="card-header bg-danger text-white border-0 py-3">
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
