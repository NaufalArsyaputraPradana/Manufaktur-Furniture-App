@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Edit Pengguna</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active">{{ $user->name }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4">
                        <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Memperbarui</h6>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-pencil-square me-2"></i>Edit Data Pengguna
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Alamat Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-md-12">
                                    <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-0">
                                        <small><i class="bi bi-info-circle-fill me-1"></i>Kosongkan kolom password jika
                                            tidak ingin mengubahnya.</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold">Password Baru</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Biarkan kosong jika tetap">
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-bold">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="Ulangi password baru">
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">No. Telepon</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label fw-bold">Role <span
                                            class="text-danger">*</span></label>
                                    <select name="role_id" id="role_id" class="form-select" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-bold">Alamat Lengkap</label>
                                    <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch p-3 bg-light rounded border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active"
                                            id="is_active" value="1"
                                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_active">
                                            Status Akun Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-warning text-white fw-bold shadow-sm" id="submitBtn">
                                    <i class="bi bi-save me-1"></i>Perbarui Data
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('editUserForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
