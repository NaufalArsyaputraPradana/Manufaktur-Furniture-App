@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daftar Laporan</h1>
            <p class="text-gray-600 mt-2">Kelola dan lihat semua laporan yang telah dibuat</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Buat Laporan Baru
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-green-800">Sukses</h3>
                <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
            </div>
            <button class="text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-red-800">Gagal</h3>
                <p class="text-red-700 text-sm mt-1">{{ session('error') }}</p>
            </div>
            <button class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Laporan</label>
                    <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Tipe --</option>
                        <option value="sales" @selected(request('type') === 'sales')>Laporan Penjualan</option>
                        <option value="production" @selected(request('type') === 'production')>Laporan Produksi</option>
                        <option value="inventory" @selected(request('type') === 'inventory')>Laporan Inventori</option>
                        <option value="financial" @selected(request('type') === 'financial')>Laporan Keuangan</option>
                        <option value="custom" @selected(request('type') === 'custom')>Laporan Kustom</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="processing" @selected(request('status') === 'processing')>Sedang Diproses</option>
                        <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                        <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if ($reports->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Judul Laporan</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tipe</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Periode</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Dibuat Pada</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($reports as $report)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-600 hover:underline">
                                        {{ $report->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ ucfirst($report->report_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($report->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($report->end_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($report->status === 'completed')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                                    @elseif ($report->status === 'processing')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Sedang Diproses</span>
                                    @elseif ($report->status === 'failed')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Gagal</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">{{ ucfirst($report->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $report->generatedBy?->name ?? 'Admin' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $report->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-600 hover:text-blue-800 transition" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.edit', $report) }}" class="text-yellow-600 hover:text-yellow-800 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Laporan</h3>
                <p class="text-gray-600 mb-6">Mulai dengan membuat laporan pertama Anda</p>
                <a href="{{ route('admin.reports.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Buat Laporan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-gray-600 text-sm">Total Laporan</p>
            <p class="text-3xl font-bold text-gray-900">{{ $reports->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-gray-600 text-sm">Selesai</p>
            <p class="text-3xl font-bold text-green-600">{{ $reports->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-gray-600 text-sm">Sedang Diproses</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $reports->where('status', 'processing')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-gray-600 text-sm">Gagal</p>
            <p class="text-3xl font-bold text-red-600">{{ $reports->where('status', 'failed')->count() }}</p>
        </div>
    </div>
</div>
@endsection
