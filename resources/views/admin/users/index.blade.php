@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .rounded-modern { border-radius: 0.75rem; }
        .table-hover tbody tr:hover { background-color: rgba(78, 115, 223, 0.05); }
        .avatar-initial {
            width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: bold; color: white; font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="position-absolute top-0 end-0 opacity-10"
                    style="font-size: 8rem; margin-top: -1rem; margin-right: -1rem;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3 shadow-sm">
                                <i class="bi bi-people-fill fs-3 text-white"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Kelola Pengguna</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                                class="text-white text-decoration-none opacity-75">Dashboard</a></li>
                                        <li class="breadcrumb-item active text-white" aria-current="page">Pengguna</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-lg-end">
                        <a href="{{ route('admin.users.create') }}"
                            class="btn btn-light btn-lg shadow-sm hover-lift text-primary fw-bold">
                            <i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm border-0 mb-4 rounded-modern">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Nama, Email, atau No. HP..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Role</label>
                        <select name="role_id" class="form-select">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-muted text-uppercase">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary shadow-sm" title="Reset Filter">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow-sm border-0 rounded-modern">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-table me-2"></i>Daftar Pengguna</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Total: {{ $users->total() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3 fw-bold">User</th>
                                <th class="py-3 fw-bold">Kontak</th>
                                <th class="py-3 fw-bold">Role</th>
                                <th class="py-3 fw-bold text-center">Status</th>
                                <th class="py-3 fw-bold">Terdaftar</th>
                                <th class="pe-4 py-3 fw-bold text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial me-3 bg-gradient bg-{{ $user->role_badge_class }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                <div class="small text-muted">ID: #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark small"><i class="bi bi-envelope me-1 text-muted"></i>{{ $user->email }}</span>
                                            <span class="text-muted small"><i class="bi bi-telephone me-1 text-muted"></i>{{ $user->phone ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role_badge_class }} bg-opacity-10 text-{{ $user->role_badge_class }} border border-{{ $user->role_badge_class }} rounded-pill px-3">
                                            {{ ucfirst($user->role?->name ?? 'User') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($user->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small" title="{{ $user->created_at }}">
                                            {{ $user->created_at->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="btn btn-sm btn-info text-white shadow-sm" data-bs-toggle="tooltip" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="btn btn-sm btn-warning text-white shadow-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <button type="button"
                                                    class="btn btn-sm btn-danger shadow-sm delete-confirm"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        Tidak ada data pengguna ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-light">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
            document.querySelectorAll('.delete-confirm').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    Swal.fire({
                        title: 'Hapus Pengguna?',
                        html: `Anda yakin ingin menghapus pengguna <strong>${name}</strong>?<br><small class="text-muted">Aksi ini tidak dapat dibatalkan!</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush