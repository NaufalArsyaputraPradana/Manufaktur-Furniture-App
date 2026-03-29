@extends('layouts.app')

@section('title', 'Masuk ke Akun')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 65px; height: 65px;">
                                <i class="bi bi-person-lock fs-1"></i>
                            </div>
                            <h3 class="fw-bold">Selamat Datang</h3>
                            <p class="text-muted">Silakan masuk untuk mengelola pesanan Anda</p>
                        </div>

                        <!-- Alerts -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Alamat
                                    Email</label>
                                <input type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required
                                    autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password"
                                    class="form-label fw-bold small text-muted text-uppercase">Password</label>
                                <div class="input-group has-validation">
                                    <input type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Masukkan password" required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button"
                                        id="togglePassword" aria-label="Tampilkan Password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label text-muted small" for="remember">
                                        Ingat saya di perangkat ini
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm py-3"
                                id="loginBtn">
                                Masuk ke Akun
                            </button>
                        </form>

                        @if (config('services.google.client_id'))
                            <div class="d-flex align-items-center gap-3 my-4" role="presentation">
                                <hr class="flex-grow-1 m-0 opacity-25">
                                <span class="text-muted small text-nowrap fw-medium">atau masuk dengan</span>
                                <hr class="flex-grow-1 m-0 opacity-25">
                            </div>

                            <a href="{{ route('google.redirect') }}"
                                class="btn btn-light btn-lg w-100 fw-semibold py-3 rounded-3 shadow-sm border google-signin-btn d-inline-flex align-items-center justify-content-center gap-2"
                                style="border-color: #dadce0 !important; color: #3c4043;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"
                                    aria-hidden="true">
                                    <path fill="#FFC107"
                                        d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.283 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                                    <path fill="#FF3D00"
                                        d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" />
                                    <path fill="#4CAF50"
                                        d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.265 0-9.63-3.331-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                                    <path fill="#1976D2"
                                        d="M43.611 20.083 44 20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.081 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
                                </svg>
                                <span>Masuk dengan Google</span>
                            </a>
                            <p class="text-center text-muted mt-2 mb-0" style="font-size: 0.75rem;">Akun baru akan
                                otomatis terdaftar sebagai pelanggan.</p>
                        @endif

                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="mb-0 text-muted small">
                                Belum memiliki akun?
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                                    Daftar Sekarang
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
            // Toggle Password Visibility
            const toggleBtn = document.querySelector('#togglePassword');
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

            // Loading State Handling
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function() {
                    loginBtn.disabled = true;
                    loginBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memproses...
                    `;
                });
            }
        });
    </script>
@endpush
