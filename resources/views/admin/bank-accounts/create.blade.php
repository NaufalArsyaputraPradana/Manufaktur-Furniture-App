@extends('layouts.admin')

@section('title', 'Tambah Rekening Bank')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #2e3338;
            margin-bottom: 0.5rem;
        }

        .input-group .form-control {
            border-radius: 0.5rem 0 0 0.5rem;
        }

        .input-group-text {
            border: 2px solid #e3e6f0;
            border-left: none;
            border-radius: 0 0.5rem 0.5rem 0;
            background: #f8f9fc;
            font-weight: 500;
        }

        .form-text {
            font-size: 0.85rem;
            color: #858796;
        }

        .required-mark {
            color: #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Header Hero Section --}}
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-plus-circle fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Tambah Rekening Bank</h2>
                                <p class="mb-0 opacity-90 small">Isi form di bawah untuk menambahkan rekening bank baru</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.bank-accounts.store') }}" method="POST">
                            @csrf

                            <x-form-input 
                                name="bank_name" 
                                label="Nama Bank"
                                :value="old('bank_name')"
                                placeholder="Contoh: BCA, Mandiri, BNI"
                                :errors="$errors"
                                required />

                            <x-form-input 
                                name="account_holder" 
                                label="Nama Pemilik Rekening"
                                :value="old('account_holder')"
                                placeholder="Contoh: PT. Manufaktur Furniture"
                                :errors="$errors"
                                required />

                            <x-form-input 
                                name="account_number" 
                                label="Nomor Rekening"
                                :value="old('account_number')"
                                placeholder="Contoh: 1234567890"
                                :errors="$errors"
                                pattern="[0-9]+"
                                title="Nomor rekening hanya boleh berisi angka"
                                help="Masukkan nomor rekening dalam format angka saja"
                                required />

                            {{-- Is Active --}}
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" @checked(old('is_active', true)) style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                    <label class="form-check-label" for="is_active" style="cursor: pointer;">
                                        <strong>Aktifkan Rekening</strong>
                                        <small class="d-block text-muted">Rekening aktif akan ditampilkan di halaman pembayaran pelanggan</small>
                                    </label>
                                </div>
                            </div>

                            <x-form-input 
                                name="notes" 
                                label="Catatan (Opsional)"
                                type="textarea"
                                :value="old('notes')"
                                placeholder="Contoh: Gunakan untuk pembayaran dP, atau catatan penting lainnya"
                                rows="3"
                                :errors="$errors"
                                maxlength="500" />

                            {{-- Form Actions --}}
                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg rounded-2 flex-grow-1">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Rekening
                                </button>
                                <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary btn-lg rounded-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Character counter untuk notes
        document.getElementById('notes').addEventListener('input', function() {
            document.getElementById('notes-count').textContent = this.value.length;
        });

        // Initialize character count on page load
        document.getElementById('notes-count').textContent = 
            document.getElementById('notes').value.length;
    </script>
@endpush
