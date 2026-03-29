@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="container-fluid px-2 md:px-4 py-3 md:py-6">
    <!-- Header - Mobile optimized -->
    <div class="mb-3 md:mb-6">
        <h1 class="h4 md:h3 fw-bold text-gray-900">Buat Laporan Baru</h1>
        <p class="text-muted small mt-2">Buat laporan kustom berdasarkan periode dan tipe yang dipilih</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 md:mb-6" role="alert">
            <h5 class="alert-heading mb-3">
                <i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan:
            </h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm mb-3 md:mb-6">
        <div class="card-body p-3 md:p-5">
            <form action="{{ route('admin.reports.store') }}" method="POST">
                @csrf

                <!-- Report Type & Title - Stack on mobile -->
                <div class="row g-2 md:g-3 mb-4">
                    <div class="col-12 md:col-6">
                        <label for="report_type" class="form-label">
                            Tipe Laporan <span class="text-danger">*</span>
                        </label>
                        <select id="report_type" name="report_type" required class="form-select form-select-sm md:form-select @error('report_type') is-invalid @enderror">
                            <option value="">-- Pilih Tipe Laporan --</option>
                            <option value="sales" @selected(old('report_type') === 'sales')>Laporan Penjualan</option>
                            <option value="production" @selected(old('report_type') === 'production')>Laporan Produksi</option>
                            <option value="inventory" @selected(old('report_type') === 'inventory')>Laporan Inventori</option>
                            <option value="financial" @selected(old('report_type') === 'financial')>Laporan Keuangan</option>
                            <option value="custom" @selected(old('report_type') === 'custom')>Laporan Kustom</option>
                        </select>
                        @error('report_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="col-12 md:col-6">
                        <label for="title" class="form-label">
                            Judul Laporan <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Laporan Penjualan Januari 2026" class="form-control form-control-sm md:form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Date Range - Stack on mobile -->
                <div class="row g-2 md:g-3 mb-4">
                    <div class="col-12 sm:col-6">
                        <label for="start_date" class="form-label">
                            Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required class="form-control form-control-sm md:form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 sm:col-6">
                        <label for="end_date" class="form-label">
                            Tanggal Selesai <span class="text-danger">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required class="form-control form-control-sm md:form-control @error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="border-top pt-4 mb-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-funnel"></i> Filter Tambahan (Opsional)
                    </h5>
                    
                    <div id="filters-container">
                        <!-- Status & Payment Status - Stack on mobile -->
                        <div class="row g-2 md:g-3 mb-3 md:mb-4">
                            <div class="col-12 md:col-6">
                                <label for="filter_status" class="form-label">Status Pesanan</label>
                                <select id="filter_status" name="filters[order_status]" class="form-select form-select-sm md:form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Sedang Diproses</option>
                                    <option value="production">Dalam Produksi</option>
                                    <option value="ready">Siap Dikirim</option>
                                    <option value="shipped">Dikirim</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="col-12 md:col-6">
                                <label for="filter_payment_status" class="form-label">Status Pembayaran</label>
                                <select id="filter_payment_status" name="filters[payment_status]" class="form-select form-select-sm md:form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="pending">Menunggu Pembayaran</option>
                                    <option value="paid">Sudah Dibayar</option>
                                    <option value="failed">Gagal</option>
                                    <option value="refunded">Dikembalikan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Category & Min Amount - Stack on mobile -->
                        <div class="row g-2 md:g-3">
                            <div class="col-12 md:col-6">
                                <label for="filter_category" class="form-label">Kategori Produk</label>
                                <select id="filter_category" name="filters[category]" class="form-select form-select-sm md:form-select">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Min Amount Filter -->
                            <div class="col-12 md:col-6">
                                <label for="filter_min_amount" class="form-label">Nilai Minimum (Rp)</label>
                                <input type="number" id="filter_min_amount" name="filters[min_amount]" step="1000" placeholder="0" class="form-control form-control-sm md:form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions - Full width buttons on mobile -->
                <div class="border-top pt-4 d-flex gap-2 flex-column md:flex-row justify-content-md-end">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm md:btn order-2 md:order-1">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm md:btn order-1 md:order-2">
                        <i class="bi bi-save"></i>
                        <span class="d-none sm:inline">Buat Laporan</span><span class="d-sm-none">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Box - Mobile optimized -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="alert-heading mb-2">
            <i class="bi bi-lightbulb"></i> Petunjuk:
        </h6>
        <ul class="mb-0 ps-4 text-sm">
            <li>Pilih tipe laporan yang sesuai dengan kebutuhan analisis Anda</li>
            <li>Atur periode tanggal untuk mengambil data dalam rentang waktu tertentu</li>
            <li>Gunakan filter tambahan untuk menyaring data sesuai kriteria spesifik</li>
            <li>Laporan dapat diunduh dalam format CSV, PDF, atau Excel setelah dibuat</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endsection
