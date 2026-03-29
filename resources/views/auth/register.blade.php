@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-7">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 65px; height: 65px;">
                                <i class="bi bi-person-plus-fill fs-1"></i>
                            </div>
                            <h3 class="fw-bold">Buat Akun Baru</h3>
                            <p class="text-muted">Bergabunglah dengan UD Bisa Furniture untuk akses penuh layanan kami</p>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4"
                                role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('register.submit') }}" method="POST" id="registerForm">
                            @csrf

                            <div class="row">
                                <!-- Nama Lengkap -->
                                <div class="col-12 mb-3">
                                    <x-form-input 
                                        name="name" 
                                        label="Nama Lengkap"
                                        type="text"
                                        placeholder="Nama Lengkap Anda"
                                        :value="old('name')"
                                        :errors="$errors"
                                        required
                                        autofocus />
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <x-form-input 
                                        name="email" 
                                        label="Alamat Email"
                                        type="email"
                                        placeholder="email@contoh.com"
                                        :value="old('email')"
                                        :errors="$errors"
                                        required />
                                </div>

                                <!-- No Telepon -->
                                <div class="col-md-6 mb-3">
                                    <x-form-input 
                                        name="phone" 
                                        label="No. Telepon / WhatsApp"
                                        type="tel"
                                        placeholder="0812xxxx"
                                        :value="old('phone')"
                                        :errors="$errors"
                                        required />
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <x-form-input 
                                    name="address" 
                                    label="Alamat Lengkap (Opsional)"
                                    type="textarea"
                                    rows="2"
                                    placeholder="Jl. Raya, Kota, Provinsi, Kode Pos"
                                    :value="old('address')"
                                    :errors="$errors" />
                            </div>
                                @enderror
                            </div>

                            <!-- Password Section -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password"
                                        class="form-label fw-bold small text-muted text-uppercase">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <input type="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Min. 8 Karakter" required>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            id="toggleRegPassword" aria-label="Tampilkan Password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!-- Password Strength Meter -->
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar transition-all" role="progressbar" style="width: 0%"
                                            id="passwordStrength"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted" id="passwordFeedback">Gunakan kombinasi huruf &
                                            angka.</small>
                                        <small class="fw-bold" id="strengthLabel"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation"
                                        class="form-label fw-bold small text-muted text-uppercase">Konfirmasi Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control form-control-lg" id="password_confirmation"
                                        name="password_confirmation" placeholder="Ulangi password" required>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    Saya telah membaca dan menyetujui <a href="#"
                                        class="text-primary text-decoration-none">Syarat & Ketentuan</a> yang berlaku.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3"
                                id="registerBtn">
                                Buat Akun Sekarang
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="mb-0 text-muted small">
                                Sudah memiliki akun?
                                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                                    Masuk di Sini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle
            const toggleBtn = document.querySelector('#toggleRegPassword');
            const passwordInput = document.querySelector('#password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }

            // Enhanced Password Strength Analysis
            if (passwordInput) {
                const strengthBar = document.getElementById('passwordStrength');
                const strengthLabel = document.getElementById('strengthLabel');

                passwordInput.addEventListener('input', function() {
                    const val = this.value;
                    let strength = 0;

                    if (val.length > 0) {
                        if (val.length >= 8) strength += 25;
                        if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 25;
                        if (val.match(/[0-9]/)) strength += 25;
                        if (val.match(/[^a-zA-Z0-9]/)) strength += 25;
                    }

                    strengthBar.style.width = strength + '%';

                    if (strength === 0) {
                        strengthBar.className = 'progress-bar';
                        strengthLabel.innerText = '';
                    } else if (strength <= 25) {
                        strengthBar.className = 'progress-bar bg-danger';
                        strengthLabel.innerText = 'Lemah';
                        strengthLabel.className = 'fw-bold text-danger small';
                    } else if (strength <= 50) {
                        strengthBar.className = 'progress-bar bg-warning';
                        strengthLabel.innerText = 'Cukup';
                        strengthLabel.className = 'fw-bold text-warning small';
                    } else if (strength <= 75) {
                        strengthBar.className = 'progress-bar bg-info';
                        strengthLabel.innerText = 'Baik';
                        strengthLabel.className = 'fw-bold text-info small';
                    } else {
                        strengthBar.className = 'progress-bar bg-success';
                        strengthLabel.innerText = 'Sangat Kuat';
                        strengthLabel.className = 'fw-bold text-success small';
                    }
                });
            }

            // Loading State on Submit
            const registerForm = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');

            if (registerForm) {
                registerForm.addEventListener('submit', function() {
                    registerBtn.disabled = true;
                    registerBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mendaftarkan Akun...
                    `;
                });
            }
        });
    </script>
@endpush
