@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Buat Laporan Baru</h1>
        <p class="text-gray-600 mt-2">Buat laporan kustom berdasarkan periode dan tipe yang dipilih</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h3 class="font-semibold text-red-800 mb-2">Terjadi kesalahan:</h3>
            <ul class="list-disc list-inside space-y-1 text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.reports.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Report Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Laporan <span class="text-red-500">*</span>
                    </label>
                    <select id="report_type" name="report_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('report_type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe Laporan --</option>
                        <option value="sales" @selected(old('report_type') === 'sales')>Laporan Penjualan</option>
                        <option value="production" @selected(old('report_type') === 'production')>Laporan Produksi</option>
                        <option value="inventory" @selected(old('report_type') === 'inventory')>Laporan Inventori</option>
                        <option value="financial" @selected(old('report_type') === 'financial')>Laporan Keuangan</option>
                        <option value="custom" @selected(old('report_type') === 'custom')>Laporan Kustom</option>
                    </select>
                    @error('report_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Laporan Penjualan Januari 2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Filters Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Tambahan (Opsional)</h3>
                
                <div id="filters-container" class="space-y-4">
                    <!-- Status Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                            <select id="filter_status" name="filters[order_status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
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
                        <div>
                            <label for="filter_payment_status" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                            <select id="filter_payment_status" name="filters[payment_status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Status --</option>
                                <option value="pending">Menunggu Pembayaran</option>
                                <option value="paid">Sudah Dibayar</option>
                                <option value="failed">Gagal</option>
                                <option value="refunded">Dikembalikan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="filter_category" class="block text-sm font-medium text-gray-700 mb-2">Kategori Produk</label>
                            <select id="filter_category" name="filters[category]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Min Amount Filter -->
                        <div>
                            <label for="filter_min_amount" class="block text-sm font-medium text-gray-700 mb-2">Nilai Minimum (Rp)</label>
                            <input type="number" id="filter_min_amount" name="filters[min_amount]" step="1000" placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 flex gap-3 justify-end">
                <a href="{{ route('admin.reports.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Buat Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="font-semibold text-blue-900 mb-2">💡 Petunjuk:</h4>
        <ul class="list-disc list-inside space-y-1 text-blue-800 text-sm">
            <li>Pilih tipe laporan yang sesuai dengan kebutuhan analisis Anda</li>
            <li>Atur periode tanggal untuk mengambil data dalam rentang waktu tertentu</li>
            <li>Gunakan filter tambahan untuk menyaring data sesuai kriteria spesifik</li>
            <li>Laporan dapat diunduh dalam format CSV, PDF, atau Excel setelah dibuat</li>
        </ul>
    </div>
</div>
@endsection
