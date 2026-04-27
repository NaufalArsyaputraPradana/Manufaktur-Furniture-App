@extends('layouts.admin')

@section('title', 'Buat Laporan Baru')

@section('content')
    <div class="container-fluid py-3">
        <div class="mb-4">
            <h1 class="h4 fw-bold text-dark mb-1">Buat Laporan Baru</h1>
            <p class="text-muted small mb-0">Buat laporan kustom berdasarkan periode dan tipe yang dipilih</p>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 p-md-5">
                <form action="{{ route('admin.reports.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="report_type" class="form-label">
                                Tipe Laporan <span class="text-danger">*</span>
                            </label>
                            <select id="report_type" name="report_type" required
                                class="form-select form-select-sm @error('report_type') is-invalid @enderror">
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

                        <div class="col-12 col-md-6">
                            <label for="title" class="form-label">
                                Judul Laporan <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                placeholder="Contoh: Laporan Penjualan Januari 2026"
                                class="form-control form-control-sm @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="start_date" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date"
                                value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required
                                class="form-control form-control-sm @error('start_date') is-invalid @enderror">
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="end_date" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date"
                                value="{{ old('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required
                                class="form-control form-control-sm @error('end_date') is-invalid @enderror">
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="border-top pt-4 mb-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-funnel"></i> Filter Tambahan (Opsional)
                        </h5>

                        <div id="filters-container">
                            <div class="row g-3 mb-3 mb-md-4">
                                <div class="col-12 col-md-6">
                                    <label for="filter_status" class="form-label">Status Pesanan</label>
                                    <select id="filter_status" name="filters[order_status]" class="form-select form-select-sm">
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

                                <div class="col-12 col-md-6">
                                    <label for="filter_payment_status" class="form-label">Status Pembayaran</label>
                                    <select id="filter_payment_status" name="filters[payment_status]" class="form-select form-select-sm">
                                        <option value="">-- Semua Status --</option>
                                        <option value="pending">Menunggu Pembayaran</option>
                                        <option value="paid">Sudah Dibayar</option>
                                        <option value="failed">Gagal</option>
                                        <option value="refunded">Dikembalikan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="filter_category" class="form-label">Kategori Produk</label>
                                    <select id="filter_category" name="filters[category]" class="form-select form-select-sm">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach ($categories ?? [] as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="filter_min_amount" class="form-label">Nilai Minimum (Rp)</label>
                                    <input type="number" id="filter_min_amount" name="filters[min_amount]" step="1000"
                                        placeholder="0" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-4 d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                        <a href="{{ route('admin.reports.index') }}"
                            class="btn btn-outline-secondary btn-sm order-2 order-md-1">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm order-1 order-md-2">
                            <i class="bi bi-save"></i>
                            <span class="d-none d-sm-inline">Buat Laporan</span><span class="d-sm-none">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bi bi-lightbulb"></i> Petunjuk:
            </h6>
            <ul class="mb-0 ps-4 small">
                <li>Pilih tipe laporan yang sesuai dengan kebutuhan analisis Anda</li>
                <li>Atur periode tanggal untuk mengambil data dalam rentang waktu tertentu</li>
                <li>Gunakan filter tambahan untuk menyaring data sesuai kriteria spesifik</li>
                <li>Laporan dapat diunduh dalam format CSV, PDF, atau Excel setelah dibuat</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endsection
