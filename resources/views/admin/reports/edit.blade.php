@extends('layouts.app')

@section('title', 'Edit Laporan - ' . $report->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Laporan</h1>
        <p class="text-gray-600 mt-2">Perbarui informasi laporan: {{ $report->title }}</p>
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
        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Report Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Laporan <span class="text-red-500">*</span>
                    </label>
                    <select id="report_type" name="report_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('report_type') border-red-500 @enderror">
                        <option value="sales" @selected($report->report_type === 'sales')>Laporan Penjualan</option>
                        <option value="production" @selected($report->report_type === 'production')>Laporan Produksi</option>
                        <option value="inventory" @selected($report->report_type === 'inventory')>Laporan Inventori</option>
                        <option value="financial" @selected($report->report_type === 'financial')>Laporan Keuangan</option>
                        <option value="custom" @selected($report->report_type === 'custom')>Laporan Kustom</option>
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
                    <input type="text" id="title" name="title" value="{{ old('title', $report->title) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
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
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $report->start_date) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $report->end_date) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Report Info -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="font-semibold text-gray-900">
                            @if ($report->status === 'completed')
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Selesai</span>
                            @elseif ($report->status === 'processing')
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Sedang Diproses</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ ucfirst($report->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Dibuat Oleh</p>
                        <p class="font-semibold text-gray-900">{{ $report->generatedBy?->name ?? 'Admin' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Dibuat Pada</p>
                        <p class="font-semibold text-gray-900">{{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>
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
                                <option value="pending" @selected(($report->filters['order_status'] ?? null) === 'pending')>Pending</option>
                                <option value="processing" @selected(($report->filters['order_status'] ?? null) === 'processing')>Sedang Diproses</option>
                                <option value="production" @selected(($report->filters['order_status'] ?? null) === 'production')>Dalam Produksi</option>
                                <option value="ready" @selected(($report->filters['order_status'] ?? null) === 'ready')>Siap Dikirim</option>
                                <option value="shipped" @selected(($report->filters['order_status'] ?? null) === 'shipped')>Dikirim</option>
                                <option value="completed" @selected(($report->filters['order_status'] ?? null) === 'completed')>Selesai</option>
                                <option value="cancelled" @selected(($report->filters['order_status'] ?? null) === 'cancelled')>Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Payment Status Filter -->
                        <div>
                            <label for="filter_payment_status" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                            <select id="filter_payment_status" name="filters[payment_status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Status --</option>
                                <option value="pending" @selected(($report->filters['payment_status'] ?? null) === 'pending')>Menunggu Pembayaran</option>
                                <option value="paid" @selected(($report->filters['payment_status'] ?? null) === 'paid')>Sudah Dibayar</option>
                                <option value="failed" @selected(($report->filters['payment_status'] ?? null) === 'failed')>Gagal</option>
                                <option value="refunded" @selected(($report->filters['payment_status'] ?? null) === 'refunded')>Dikembalikan</option>
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
                                    <option value="{{ $category->id }}" @selected(($report->filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Min Amount Filter -->
                        <div>
                            <label for="filter_min_amount" class="block text-sm font-medium text-gray-700 mb-2">Nilai Minimum (Rp)</label>
                            <input type="number" id="filter_min_amount" name="filters[min_amount]" step="1000" value="{{ $report->filters['min_amount'] ?? '' }}" placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 flex gap-3 justify-end">
                <a href="{{ route('admin.reports.show', $report) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
