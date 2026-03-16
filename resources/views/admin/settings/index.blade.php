@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-gear-fill fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Pengaturan Sistem</h2>
                                <p class="mb-0 opacity-90 small">Konfigurasi umum, tampilan, dan notifikasi aplikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Left Column: General & System -->
                <div class="col-lg-8">
                    <!-- General Settings -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-shop me-2"></i>Informasi Umum</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Aplikasi <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="site_name"
                                        class="form-control @error('site_name') is-invalid @enderror"
                                        value="{{ old('site_name', $settings['site_name']) }}" required>
                                    @error('site_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Admin <span class="text-danger">*</span></label>
                                    <input type="email" name="site_email"
                                        class="form-control @error('site_email') is-invalid @enderror"
                                        value="{{ old('site_email', $settings['site_email']) }}" required>
                                    @error('site_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. Telepon</label>
                                    <input type="text" name="site_phone"
                                        class="form-control @error('site_phone') is-invalid @enderror"
                                        value="{{ old('site_phone', $settings['site_phone']) }}">
                                    @error('site_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mata Uang</label>
                                    <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                        <option value="IDR"
                                            {{ old('currency', $settings['currency']) == 'IDR' ? 'selected' : '' }}>IDR
                                            (Rupiah)</option>
                                        <option value="USD"
                                            {{ old('currency', $settings['currency']) == 'USD' ? 'selected' : '' }}>USD
                                            (Dollar)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Alamat Perusahaan</label>
                                    <textarea name="site_address" rows="3" class="form-control @error('site_address') is-invalid @enderror">{{ old('site_address', $settings['site_address']) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Config -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-info"><i class="bi bi-sliders me-2"></i>Konfigurasi Sistem</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Zona Waktu</label>
                                    <select name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                        <option value="Asia/Jakarta"
                                            {{ old('timezone', $settings['timezone']) == 'Asia/Jakarta' ? 'selected' : '' }}>
                                            Asia/Jakarta (WIB)</option>
                                        <option value="Asia/Makassar"
                                            {{ old('timezone', $settings['timezone']) == 'Asia/Makassar' ? 'selected' : '' }}>
                                            Asia/Makassar (WITA)</option>
                                        <option value="Asia/Jayapura"
                                            {{ old('timezone', $settings['timezone']) == 'Asia/Jayapura' ? 'selected' : '' }}>
                                            Asia/Jayapura (WIT)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Data Per Halaman (Pagination)</label>
                                    <input type="number" name="items_per_page"
                                        class="form-control @error('items_per_page') is-invalid @enderror"
                                        value="{{ old('items_per_page', $settings['items_per_page']) }}" min="5"
                                        max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Toggles -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-bell me-2"></i>Preferensi</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Notifikasi</label>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="enable_notifications"
                                        name="enable_notifications" value="1"
                                        {{ old('enable_notifications', $settings['enable_notifications']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_notifications">Notifikasi Sistem</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_email_notifications"
                                        name="enable_email_notifications" value="1"
                                        {{ old('enable_email_notifications', $settings['enable_email_notifications']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_email_notifications">Notifikasi
                                        Email</label>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label fw-bold mb-2 text-danger">Zona Bahaya</label>
                                <div
                                    class="form-check form-switch p-3 bg-danger bg-opacity-10 rounded border border-danger">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                        name="maintenance_mode" value="1"
                                        {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="maintenance_mode">Mode
                                        Maintenance</label>
                                    <small class="d-block text-muted mt-1">
                                        Jika aktif, hanya admin yang dapat mengakses sistem.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        /* Danger switch override */
        #maintenance_mode:checked {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }
    </style>
@endpush
