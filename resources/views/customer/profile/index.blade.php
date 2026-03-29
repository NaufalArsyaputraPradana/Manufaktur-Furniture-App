@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section position-relative text-white" aria-label="Profil hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0 p-0 bg-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"
                            class="text-white text-decoration-none opacity-75 hover-opacity-100 transition-all">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Profil Saya</li>
                </ol>
            </nav>

            <div class="text-center fade-in">
                <h1 class="display-4 fw-bold mb-3 text-white">
                    <i class="bi bi-person-circle me-2" aria-hidden="true"></i>Profil Saya
                </h1>
                <p class="lead mb-0 opacity-90">Kelola informasi akun dan pengaturan keamanan Anda</p>
            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" focusable="false">
                <path
                    d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                    class="shape-fill" fill="#f8f9fa"></path>
            </svg>
        </div>
    </section>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="profile-section py-5 bg-light" aria-label="Profil pengguna">
        <div class="container">

            {{-- Session Alerts --}}
            @foreach (['success' => ['check-circle-fill', 'alert-success'], 'error' => ['exclamation-triangle-fill', 'alert-danger']] as $type => [$icon, $class])
                @if (session($type))
                    <div class="alert {{ $class }} alert-dismissible fade show mb-4 rounded-4 border-0 shadow-sm"
                        role="alert">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-{{ $icon }} me-2 mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <div class="flex-grow-1 fw-medium">{{ session($type) }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="row g-4">
                {{-- ===== MAIN COLUMN ===== --}}
                <div class="col-lg-8">

                    {{-- Profile Information Form --}}
                    <div class="card shadow-sm border-0 mb-4 rounded-4 bg-white animate-on-scroll">
                        <div class="card-header bg-gradient-primary text-white border-0 p-4 rounded-top-4">
                            <h5 class="mb-0 fw-bold text-white d-flex align-items-center">
                                <i class="bi bi-person-badge-fill me-2" aria-hidden="true"></i>Informasi Profil
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('customer.profile.update') }}" method="POST" id="profileForm">
                                @csrf
                                @method('PATCH')

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-form-input 
                                            name="name" 
                                            label="Nama Lengkap"
                                            :value="old('name', $user->name)"
                                            :errors="$errors"
                                            required />
                                    </div>

                                    <div class="col-md-6">
                                        <x-form-input 
                                            name="email" 
                                            label="Alamat Email"
                                            type="email"
                                            :value="old('email', $user->email)"
                                            :errors="$errors"
                                            required />
                                    </div>

                                    <div class="col-md-12">
                                        <x-form-input 
                                            name="phone" 
                                            label="No. Telepon / WhatsApp"
                                            type="tel"
                                            :value="old('phone', $user->phone)"
                                            placeholder="Contoh: 08123456789"
                                            :errors="$errors" />
                                    </div>

                                    <div class="col-12">
                                        <x-form-input 
                                            name="address" 
                                            label="Alamat Pengiriman Default"
                                            type="textarea"
                                            :value="old('address', $user->address)"
                                            placeholder="Alamat lengkap untuk pengiriman (Otomatis terisi saat checkout)"
                                            rows="3"
                                            :errors="$errors" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Hak Akses
                                            (Role)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-shield-check text-success" aria-hidden="true"></i></span>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $user->role ? ucfirst(str_replace('_', ' ', $user->role->name)) : 'Customer' }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Bergabung
                                            Sejak</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-calendar-check text-info" aria-hidden="true"></i></span>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $user->created_at->format('d M Y') }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top border-light">
                                    <button type="submit"
                                        class="btn btn-primary btn-lg px-5 hover-lift fw-bold shadow-sm"
                                        id="btnUpdateProfile">
                                        <i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Profil
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Change Password Form --}}
                    <div class="card shadow-sm border-0 mb-4 rounded-4 bg-white animate-on-scroll">
                        <div
                            class="card-header bg-warning bg-opacity-10 py-3 border-bottom border-warning border-opacity-25 rounded-top-4">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center p-2">
                                <i class="bi bi-shield-lock-fill me-2 text-warning" aria-hidden="true"></i>Keamanan Akun
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('customer.profile.password.update') }}" method="POST"
                                id="passwordForm">
                                @csrf
                                @method('PATCH')

                                @foreach ([['id' => 'current_password', 'name' => 'current_password', 'label' => 'Password Saat Ini', 'icon' => 'bi-lock', 'required' => true], ['id' => 'password', 'name' => 'password', 'label' => 'Password Baru', 'icon' => 'bi-key', 'required' => true], ['id' => 'password_confirmation', 'name' => 'password_confirmation', 'label' => 'Konfirmasi Password Baru', 'icon' => 'bi-check2-circle', 'required' => true]] as $field)
                                    <div class="mb-4">
                                        <label for="{{ $field['id'] }}"
                                            class="form-label fw-bold small text-muted text-uppercase">
                                            {{ $field['label'] }} <span class="text-danger" aria-hidden="true">*</span>
                                        </label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="bi {{ $field['icon'] }} text-warning"
                                                    aria-hidden="true"></i></span>
                                            <input type="password"
                                                class="form-control border-start-0 border-end-0 ps-0 @error($field['name']) is-invalid @enderror"
                                                id="{{ $field['id'] }}" name="{{ $field['name'] }}"
                                                {{ $field['required'] ? 'required' : '' }}>
                                            <button class="btn btn-outline-secondary border-start-0 password-toggle"
                                                type="button" data-target="{{ $field['id'] }}"
                                                aria-label="Toggle password visibility">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                            @error($field['name'])
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if ($field['id'] === 'password')
                                            <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle me-1"
                                                    aria-hidden="true"></i>Minimal 8 karakter (Disarankan kombinasi huruf &
                                                angka).</small>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                                    <button type="submit"
                                        class="btn btn-warning text-dark btn-lg px-5 hover-lift fw-bold shadow-sm"
                                        id="btnUpdatePassword">
                                        <i class="bi bi-key-fill me-2" aria-hidden="true"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="card shadow-sm border-danger border-opacity-25 mb-4 rounded-4 bg-white animate-on-scroll">
                        <div class="card-header bg-danger text-white border-0 p-4 rounded-top-4">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>Zona Berbahaya
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                                <div>
                                    <h5 class="text-danger fw-bold mb-2">Hapus Akun Permanen</h5>
                                    <p class="text-muted mb-0">
                                        Setelah dihapus, semua data profil dan riwayat Anda akan hilang selamanya.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-lg fw-bold hover-lift"
                                    data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    <i class="bi bi-trash-fill me-2" aria-hidden="true"></i>Hapus Akun
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="col-lg-4">

                    {{-- Account Stats --}}
                    <div class="card shadow-sm border-0 mb-4 rounded-4 sticky-sidebar animate-on-scroll bg-white">
                        <div class="card-header bg-light border-0 p-4 rounded-top-4">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-graph-up-arrow me-2 text-primary" aria-hidden="true"></i>Statistik Belanja
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $orders = $user->orders ?? collect();
                                $totalOrders = $orders->count();
                                $activeOrders = $orders
                                    ->whereIn('status', ['pending', 'confirmed', 'in_production'])
                                    ->count();
                                $doneOrders = $orders->where('status', 'completed')->count();
                            @endphp

                            @foreach ([['color' => 'primary', 'icon' => 'bi-box-seam', 'label' => 'Total Pesanan Dibuat', 'value' => $totalOrders], ['color' => 'warning', 'icon' => 'bi-clock-history', 'label' => 'Pesanan Berjalan', 'value' => $activeOrders], ['color' => 'success', 'icon' => 'bi-check-circle', 'label' => 'Pesanan Selesai', 'value' => $doneOrders]] as $stat)
                                <div
                                    class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ $loop->last ? '' : 'border-bottom border-light' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon bg-{{ $stat['color'] }} bg-opacity-10 rounded-circle p-2 me-3 shadow-sm"
                                            style="width: 45px; height: 45px; display: flex; justify-content: center; align-items: center;">
                                            <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }} fs-5"
                                                aria-hidden="true"></i>
                                        </div>
                                        <span class="fw-semibold text-muted">{{ $stat['label'] }}</span>
                                    </div>
                                    <strong class="fs-4 text-{{ $stat['color'] }}">{{ $stat['value'] }}</strong>
                                </div>
                            @endforeach

                            <div class="d-grid gap-3 mt-4 pt-3 border-top border-light">
                                <a href="{{ route('customer.orders.index') }}"
                                    class="btn btn-light border text-primary fw-bold hover-lift py-2">
                                    <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Lihat Riwayat Penuh
                                </a>
                                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold hover-lift py-2">
                                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ===== DELETE ACCOUNT MODAL ===== --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-danger text-white border-0 p-4">
                    <h5 class="modal-title fw-bold" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-octagon-fill me-2" aria-hidden="true"></i>Konfirmasi Hapus Akun
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.profile.destroy') }}" method="POST" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4 bg-white">
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger mb-4 rounded-3 p-3"
                            role="alert">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill fs-3 flex-shrink-0" aria-hidden="true"></i>
                                <div><strong>Peringatan Kritikal!</strong> Tindakan ini akan menghapus akun Anda beserta
                                    seluruh riwayatnya.</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="delete_password" class="form-label fw-bold text-dark">Konfirmasi Password
                                Anda</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"
                                        aria-hidden="true"></i></span>
                                <input type="password"
                                    class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                    id="delete_password" name="password" required
                                    placeholder="Masukkan password untuk memverifikasi...">
                            </div>
                            @error('password')
                                <div class="text-danger small mt-2 fw-medium"><i
                                        class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-light border fw-medium px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm">
                            <i class="bi bi-trash-fill me-2" aria-hidden="true"></i>Ya, Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* HERO */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            padding-top: 9rem;
            padding-bottom: 5rem;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: rgba(255, 255, 255, 0.6);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        .wave-bottom {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 2;
        }

        .wave-bottom svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .wave-bottom .shape-fill {
            fill: #f8f9fa;
        }

        /* GRADIENTS & UTILITIES */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .rounded-modern {
            border-radius: 1rem !important;
        }

        .profile-section {
            min-height: 60vh;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        /* FORMS */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            background-color: #fff !important;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #667eea;
        }

        /* BUTTONS & ANIMATIONS */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
        }

        .hover-lift:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63408a 100%);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .sticky-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 40px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            document.addEventListener('DOMContentLoaded', function() {
                initScrollAnimations();
                initPasswordToggles();
                initFormLoading();
                initSweetAlertTriggers();
            });

            // SCROLL ANIMATIONS
            function initScrollAnimations() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
            }

            // PASSWORD VISIBILITY TOGGLE
            function initPasswordToggles() {
                document.querySelectorAll('.password-toggle').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const input = document.getElementById(this.dataset.target);
                        if (!input) return;

                        input.type = input.type === 'password' ? 'text' : 'password';
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.toggle('bi-eye');
                            icon.classList.toggle('bi-eye-slash');
                        }
                    });
                });
            }

            // FORM LOADING STATES
            function initFormLoading() {
                const setupLoading = (formId, btnId, loadingText) => {
                    const form = document.getElementById(formId);
                    if (!form) return;

                    form.addEventListener('submit', function() {
                        const btn = document.getElementById(btnId);
                        if (btn && !btn.disabled) {
                            btn.disabled = true;
                            btn.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}...`;
                            // Automatically release lock after 8 seconds if server fails to respond
                            setTimeout(() => {
                                location.reload();
                            }, 8000);
                        }
                    });
                };

                setupLoading('profileForm', 'btnUpdateProfile', 'Menyimpan');
                setupLoading('passwordForm', 'btnUpdatePassword', 'Memproses');
            }

            // SWEETALERT ACTIONS (LOGOUT & DELETE)
            function initSweetAlertTriggers() {
                // DELETE ACCOUNT
                const deleteForm = document.getElementById('deleteAccountForm');
                if (deleteForm) {
                    deleteForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const pwd = document.getElementById('delete_password').value;

                        if (!pwd) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Password Kosong',
                                text: 'Masukkan password Anda untuk memverifikasi penghapusan akun.',
                                confirmButtonColor: '#667eea',
                                customClass: {
                                    popup: 'rounded-4'
                                }
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Apakah Anda sangat yakin?',
                            text: 'Seluruh riwayat pesanan dan akun Anda akan hilang selamanya!',
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus Permanen',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'shadow-sm',
                                cancelButton: 'fw-medium bg-light text-dark border'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById(
                                    'deleteAccountModal'));
                                if (modal) modal.hide();

                                Swal.fire({
                                    title: 'Menghapus Akun...',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4'
                                    },
                                    didOpen: () => Swal.showLoading()
                                });
                                deleteForm.submit();
                            }
                        });
                    });
                }

                // LOGOUT
                const logoutForm = document.getElementById('logoutForm');
                if (logoutForm) {
                    logoutForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Logout',
                            text: 'Apakah Anda yakin ingin keluar dari sesi ini?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#667eea',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Logout',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'shadow-sm',
                                cancelButton: 'fw-medium bg-light text-dark border'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Logging out...',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4'
                                    },
                                    didOpen: () => Swal.showLoading()
                                });
                                logoutForm.submit();
                            }
                        });
                    });
                }
            }

            // MODAL CLEANUP (Mencegah backdrop tersangkut)
            document.getElementById('deleteAccountModal')?.addEventListener('hidden.bs.modal', function() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
                document.getElementById('delete_password').value = ''; // Reset password input on close
            });

        })();
    </script>
@endpush
