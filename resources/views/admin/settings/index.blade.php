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
                                    <x-form-input 
                                        name="site_name" 
                                        label="Nama Aplikasi"
                                        type="text"
                                        :value="old('site_name', $settings['site_name'])"
                                        :errors="$errors"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="site_email" 
                                        label="Email Admin"
                                        type="email"
                                        :value="old('site_email', $settings['site_email'])"
                                        :errors="$errors"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="site_phone" 
                                        label="No. Telepon"
                                        type="tel"
                                        :value="old('site_phone', $settings['site_phone'])"
                                        :errors="$errors" />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="currency" 
                                        label="Mata Uang"
                                        type="select"
                                        :options="['IDR' => 'IDR (Rupiah)', 'USD' => 'USD (Dollar)']"
                                        :value="old('currency', $settings['currency'])"
                                        :errors="$errors" />
                                </div>
                                <div class="col-12">
                                    <x-form-input 
                                        name="site_address" 
                                        label="Alamat Perusahaan"
                                        type="textarea"
                                        rows="3"
                                        :value="old('site_address', $settings['site_address'])"
                                        :errors="$errors" />
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
                                    <x-form-input 
                                        name="timezone" 
                                        label="Zona Waktu"
                                        type="select"
                                        :options="['Asia/Jakarta' => 'Asia/Jakarta (WIB)', 'Asia/Makassar' => 'Asia/Makassar (WITA)', 'Asia/Jayapura' => 'Asia/Jayapura (WIT)']"
                                        :value="old('timezone', $settings['timezone'])"
                                        :errors="$errors" />
                                </div>
                                <div class="col-md-6">
                                    <x-form-input 
                                        name="items_per_page" 
                                        label="Data Per Halaman (Pagination)"
                                        type="number"
                                        :value="old('items_per_page', $settings['items_per_page'])"
                                        :errors="$errors" />
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
