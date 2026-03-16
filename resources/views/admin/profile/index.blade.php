@extends('layouts.admin')

@section('title', 'Profil Saya')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!-- Header Card -->
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>

            <!-- Decorative Icon -->
            <div class="position-absolute top-0 end-0 opacity-10"
                style="font-size: 8rem; margin-top: -1rem; margin-right: -1rem;">
                <i class="bi bi-person-circle"></i>
            </div>

            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <!-- Avatar Placeholder -->
                            <div class="me-3 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 65px; height: 65px; font-size: 2rem; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Profil Saya</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                                class="text-white text-decoration-none opacity-75">Dashboard</a></li>
                                        <li class="breadcrumb-item text-white fw-semibold active" aria-current="page">
                                            Pengaturan Profil</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <span class="badge bg-white text-primary px-3 py-2 fs-6 shadow-sm border-0">
                            <i class="bi bi-shield-lock-fill me-1"></i> {{ ucfirst($user->role?->name ?? 'Administrator') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Forms -->
            <div class="col-lg-8">

                <!-- 1. Edit Profile Form -->
                <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-person-badge me-2"></i>Informasi Akun
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Alamat Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">No. Telepon / WA</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                        placeholder="Contoh: 08123456789">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Alamat Tinggal</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                        placeholder="Alamat lengkap...">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnUpdateProfile">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 2. Change Password Form -->
                <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-warning bg-opacity-10 py-3 border-bottom border-warning border-opacity-25">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-key-fill me-2 text-warning"></i>Ganti Password Keamanan
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.profile.password.update') }}" method="POST" id="passwordForm">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Password Saat Ini <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('current_password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">Password Baru <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Minimal 8 karakter
                                        unik.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password
                                        Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password_confirmation')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-warning text-dark px-4 shadow-sm fw-bold"
                                    id="btnUpdatePassword">
                                    <i class="bi bi-shield-lock me-2"></i>Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 3. Danger Zone -->
                <div class="card shadow-sm border-danger border-opacity-25 rounded-3 mb-4">
                    <div class="card-header bg-danger text-white py-3 border-0">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Zona Berbahaya
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="text-danger fw-bold mb-1">Hapus Akun Permanen</h6>
                                <p class="text-muted mb-0 small">
                                    Setelah dihapus, semua akses dan data Anda akan hilang selamanya.
                                </p>
                            </div>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash me-2"></i>Hapus Akun Saya
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-4">
                <!-- Stats -->
                <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-bold text-secondary">
                            <i class="bi bi-graph-up me-2 text-primary"></i>Statistik Aktivitas
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Waktu Bergabung</small>
                                <span class="fw-bold text-dark">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-shield-check text-info fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Status Verifikasi</small>
                                @if ($user->email_verified_at)
                                    <span class="text-success fw-bold"><i
                                            class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                                @else
                                    <span class="text-warning fw-bold"><i class="bi bi-hourglass-split me-1"></i>Belum
                                        Verifikasi</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-activity text-success fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Pesanan Diproses</small>
                                <span class="fw-bold text-dark">{{ $stats['total_orders'] ?? 0 }} Transaksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-bold text-secondary">
                            <i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Navigasi
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.dashboard') }}"
                                class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center">
                                <i class="bi bi-speedometer2 me-3 text-primary fs-5"></i> Ke Panel Dashboard
                                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="d-block w-100">
                                @csrf
                                <button type="submit"
                                    class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center text-danger">
                                    <i class="bi bi-box-arrow-right me-3 fs-5"></i> Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
                        Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4">
                        <div
                            class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger mb-4">
                            <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Seluruh data transaksi dan
                            riwayat login Anda akan dihapus permanen dari sistem.
                        </div>
                        <div class="mb-0">
                            <label for="del_password" class="form-label fw-bold">Konfirmasi Password Anda</label>
                            <input type="password" name="password" id="del_password"
                                class="form-control form-control-lg" required placeholder="Masukkan password saat ini...">
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batalkan</button>
                        <button type="submit" class="btn btn-danger fw-bold px-4">Ya, Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /**
         * Fungsi Toggle Password Visibility
         */
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const btn = input.nextElementSibling;
            const icon = btn.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        /**
         * Loading States pada saat submit form
         */
        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('profileForm');
            const passwordForm = document.getElementById('passwordForm');

            profileForm?.addEventListener('submit', function() {
                const btn = document.getElementById('btnUpdateProfile');
                btn.disabled = true;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
            });

            passwordForm?.addEventListener('submit', function() {
                const btn = document.getElementById('btnUpdatePassword');
                btn.disabled = true;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            });
        });
    </script>
@endpush
