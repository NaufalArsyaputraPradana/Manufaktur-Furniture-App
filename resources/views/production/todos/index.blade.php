@extends('layouts.production')

@section('title', 'To Do List Produksi')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">To Do List Produksi</h4>
            <p class="text-muted mb-0 small">Kelola tugas harian sebagai staf produksi</p>
        </div>
        <a href="{{ route('staff.production.todos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Buat Tugas Baru
        </a>
    </div>

            {{-- Filter Form --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <x-form-input
                                name="search"
                                label="Cari Judul"
                                type="text"
                                :value="$search"
                                placeholder="Cari tugas..."
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form-input
                                name="status"
                                label="Filter Status"
                                type="select"
                                :options="collect(['' => 'Semua Status'])->union(collect($statusOptions)->flip()->flip()->mapWithKeys(fn($status) => [$status => ucfirst(str_replace('_', ' ', $status))]))"
                                :value="$currentStatus"
                            />
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                            <a href="{{ route('staff.production.todos.index') }}" class="btn btn-outline-secondary flex-grow-1">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            @if ($todos->count())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                        <h5 class="fw-bold mb-0"><i class="bi bi-check2-square text-primary me-2"></i>Daftar Tugas</h5>
                        <span class="badge bg-primary">Total {{ $todos->total() }}</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($todos as $todo)
                            <div class="list-group-item p-3 p-md-4 d-flex flex-column flex-md-row align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="fw-bold mb-0">
                                            <a href="{{ route('staff.production.todos.show', $todo) }}"
                                                class="text-decoration-none text-dark">{{ $todo->title }}</a>
                                        </h6>
                                        <span class="{{ $todo->status_badge_class }}">{{ $todo->status_label }}</span>
                                        @if ($todo->isOverdue())
                                            <span class="badge bg-danger bg-opacity-10 text-danger"><i
                                                    class="bi bi-exclamation-triangle me-1"></i>Terlambat</span>
                                        @endif
                                    </div>
                                    @if ($todo->deadline)
                                        <small class="text-muted d-block mb-1"><i class="bi bi-clock me-1"></i>Deadline:
                                            {{ $todo->deadline->translatedFormat('d M Y H:i') }}</small>
                                    @endif
                                    @if ($todo->description)
                                        <p class="mb-1 text-muted small">
                                            {{ \Illuminate\Support\Str::limit($todo->description, 140) }}</p>
                                    @endif
                                </div>
                                <div class="mt-3 mt-md-0 ms-md-3 text-end">
                                    <div class="mb-2">
                                        <select class="form-select form-select-sm todo-status-select"
                                            data-url="{{ route('staff.production.todos.update-status', $todo) }}">
                                            @foreach ($statusOptions as $status)
                                                <option value="{{ $status }}"
                                                    {{ $todo->status == $status ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('staff.production.todos.edit', $todo) }}"
                                            class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('staff.production.todos.destroy', $todo) }}" method="POST"
                                            class="d-inline-block" onsubmit="return confirm('Hapus tugas ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($todos->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $todos->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-clipboard-x display-1 text-muted opacity-50"></i>
                        <h5 class="mt-3">Belum ada tugas</h5>
                        <p class="text-muted">Buat to do list untuk mengatur prioritas pekerjaan produksi Anda.</p>
                        <a href="{{ route('staff.production.todos.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i>Buat Tugas Pertama
                        </a>
                    </div>
                </div>
            @endif
</div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrf) return;

            document.querySelectorAll('.todo-status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const url = this.dataset.url;
                    const status = this.value;
                    const row = this.closest('.list-group-item');

                    fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data?.message && window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                            }
                            if (row) {
                                row.classList.add('bg-light');
                                setTimeout(() => row.classList.remove('bg-light'), 600);
                            }
                        })
                        .catch(() => {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal mengubah status.'
                                });
                            } else {
                                alert('Gagal mengubah status.');
                            }
                        });
                });
            });
        })();
    </script>
@endpush
