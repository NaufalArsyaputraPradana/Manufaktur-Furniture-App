@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Tambah Pengguna Baru</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Alert Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4">
                        <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan</h6>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                    @csrf
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-person-plus-fill me-2"></i>Form Data Pengguna
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Nama & Email -->
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="name" 
                                        label="Nama Lengkap"
                                        placeholder="Contoh: John Doe"
                                        :value="old('name')"
                                        :errors="$errors"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="email" 
                                        label="Alamat Email"
                                        type="email"
                                        placeholder="email@example.com"
                                        :value="old('email')"
                                        :errors="$errors"
                                        required />
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="password" 
                                        label="Password"
                                        type="password"
                                        placeholder="Minimal 8 karakter"
                                        :errors="$errors"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="password_confirmation" 
                                        label="Konfirmasi Password"
                                        type="password"
                                        placeholder="Ulangi password"
                                        :errors="$errors"
                                        required />
                                </div>

                                <!-- Kontak & Role -->
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="phone" 
                                        label="No. Telepon"
                                        type="tel"
                                        placeholder="0812..."
                                        :value="old('phone')"
                                        :errors="$errors" />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="role_id" 
                                        label="Role"
                                        type="select"
                                        :options="$roles->pluck('name', 'id')"
                                        :value="old('role_id')"
                                        :errors="$errors"
                                        required />
                                </div>

                                <!-- Alamat -->
                                <div class="col-12">
                                    <x-form-input 
                                        name="address" 
                                        label="Alamat Lengkap"
                                        type="textarea"
                                        placeholder="Alamat tempat tinggal..."
                                        :value="old('address')"
                                        :errors="$errors"
                                        rows="3" />
                                </div>

                                <!-- Status Switch -->
                                <div class="col-12">
                                    <div class="form-check form-switch p-3 bg-light rounded border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active"
                                            id="is_active" value="1"
                                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_active">
                                            Aktifkan Akun
                                        </label>
                                        <small class="d-block text-muted ms-5">Pengguna dapat login jika akun aktif.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm" id="submitBtn">
                                    <i class="bi bi-save me-1"></i>Simpan Pengguna
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
        document.getElementById('createUserForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            // Mencegah multiple clicks
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
