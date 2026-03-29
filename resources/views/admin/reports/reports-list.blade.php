@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
<div class="container-fluid px-2 md:px-4 py-3 md:py-6">
    <!-- Header - Mobile optimized -->
    <div class="mb-3 md:mb-6 d-flex flex-column md:flex-row justify-content-md-between align-items-md-start gap-3">
        <div>
            <h1 class="h4 md:h3 fw-bold text-gray-900">Daftar Laporan</h1>
            <p class="text-muted small mt-1">Kelola dan lihat semua laporan yang telah dibuat</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm align-self-start md:btn">
            <i class="bi bi-plus-circle"></i>
            <span class="d-none sm:inline">Buat Laporan Baru</span><span class="d-sm-none">Buat</span>
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 md:mb-6" role="alert">
            <strong><i class="bi bi-check-circle"></i> Sukses</strong>
            <p class="mb-0 text-sm mt-1">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 md:mb-6" role="alert">
            <strong><i class="bi bi-exclamation-circle"></i> Gagal</strong>
            <p class="mb-0 text-sm mt-1">{{ session('error') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters - Mobile responsive -->
    <div class="card border-0 shadow-sm mb-3 md:mb-6">
        <div class="card-body p-3 md:p-4">
            <form action="{{ route('admin.reports.index') }}" method="GET">
                <div class="row g-2 md:g-3">
                    <!-- Type Filter - Full width on mobile -->
                    <div class="col-12 md:col-4">
                        <label for="type" class="form-label small mb-1">Tipe Laporan</label>
                        <select id="type" name="type" class="form-select form-select-sm md:form-select">
                            <option value="">-- Semua Tipe --</option>
                            <option value="sales" @selected(request('type') === 'sales')>Laporan Penjualan</option>
                            <option value="production" @selected(request('type') === 'production')>Laporan Produksi</option>
                            <option value="inventory" @selected(request('type') === 'inventory')>Laporan Inventori</option>
                            <option value="financial" @selected(request('type') === 'financial')>Laporan Keuangan</option>
                            <option value="custom" @selected(request('type') === 'custom')>Laporan Kustom</option>
                        </select>
                    </div>

                    <!-- Status Filter - Full width on mobile -->
                    <div class="col-12 md:col-4">
                        <label for="status" class="form-label small mb-1">Status</label>
                        <select id="status" name="status" class="form-select form-select-sm md:form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="processing" @selected(request('status') === 'processing')>Sedang Diproses</option>
                            <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                            <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                        </select>
                    </div>

                    <!-- Buttons - Stack on mobile -->
                    <div class="col-12 md:col-4 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 md:flex-grow-0 md:btn">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1 md:flex-grow-0 md:btn">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table - Responsive -->
    <div class="card border-0 shadow-sm">
        @if ($reports->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none md:block">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 text-sm">Judul Laporan</th>
                            <th class="px-3 py-2 text-sm">Tipe</th>
                            <th class="px-3 py-2 text-sm">Periode</th>
                            <th class="px-3 py-2 text-sm">Status</th>
                            <th class="px-3 py-2 text-sm d-none lg:table-cell">Dibuat Oleh</th>
                            <th class="px-3 py-2 text-sm">Dibuat Pada</th>
                            <th class="px-3 py-2 text-end text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($reports as $report)
                            <tr class="align-middle">
                                <td class="px-3 py-3 text-sm font-weight-bold">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="text-primary text-decoration-none">
                                        {{ $report->title }}
                                    </a>
                                </td>
                                <td class="px-3 py-3 text-sm">
                                    <span class="badge bg-info">{{ ucfirst($report->report_type) }}</span>
                                </td>
                                <td class="px-3 py-3 text-sm text-muted">
                                    {{ \Carbon\Carbon::parse($report->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($report->end_date)->format('d M Y') }}
                                </td>
                                <td class="px-3 py-3 text-sm">
                                    @if ($report->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($report->status === 'processing')
                                        <span class="badge bg-warning text-dark">Sedang Diproses</span>
                                    @elseif ($report->status === 'failed')
                                        <span class="badge bg-danger">Gagal</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($report->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-sm text-muted d-none lg:table-cell">
                                    {{ $report->generatedBy?->name ?? 'Admin' }}
                                </td>
                                <td class="px-3 py-3 text-sm text-muted">
                                    {{ $report->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-3 py-3 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.edit', $report) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                <div class="list-group list-group-flush">
                    @foreach ($reports as $report)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="text-primary text-decoration-none">
                                            {{ $report->title }}
                                        </a>
                                    </h6>
                                    <div class="d-flex gap-2 flex-wrap mb-2">
                                        <span class="badge bg-info text-sm">{{ ucfirst($report->report_type) }}</span>
                                        @if ($report->status === 'completed')
                                            <span class="badge bg-success text-sm">Selesai</span>
                                        @elseif ($report->status === 'processing')
                                            <span class="badge bg-warning text-dark text-sm">Sedang Diproses</span>
                                        @elseif ($report->status === 'failed')
                                            <span class="badge bg-danger text-sm">Gagal</span>
                                        @else
                                            <span class="badge bg-secondary text-sm">{{ ucfirst($report->status) }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">
                                        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($report->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($report->end_date)->format('d M Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <strong>Dibuat:</strong> {{ $report->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <a href="{{ route('admin.reports.edit', $report) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Yakin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination - Mobile optimized -->
            <div class="card-footer bg-light border-top p-3 md:p-4">
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card-body text-center py-8 md:py-12">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="mt-4 mb-2 text-muted">Belum Ada Laporan</h5>
                <p class="text-muted text-sm mb-4">Mulai dengan membuat laporan pertama Anda</p>
                <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Buat Laporan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Stats - Mobile responsive -->
    <div class="row g-2 md:g-4 mt-3 md:mt-6">
        <div class="col-6 sm:col-3">
            <div class="card border-0 shadow-sm text-center p-3 md:p-4">
                <p class="text-muted small mb-2">Total Laporan</p>
                <p class="h5 md:h3 fw-bold text-dark mb-0">{{ $reports->total() }}</p>
            </div>
        </div>
        <div class="col-6 sm:col-3">
            <div class="card border-0 shadow-sm text-center p-3 md:p-4">
                <p class="text-muted small mb-2">Selesai</p>
                <p class="h5 md:h3 fw-bold text-success mb-0">{{ $reports->where('status', 'completed')->count() }}</p>
            </div>
        </div>
        <div class="col-6 sm:col-3 mt-2 sm:mt-0">
            <div class="card border-0 shadow-sm text-center p-3 md:p-4">
                <p class="text-muted small mb-2">Diproses</p>
                <p class="h5 md:h3 fw-bold text-warning mb-0">{{ $reports->where('status', 'processing')->count() }}</p>
            </div>
        </div>
        <div class="col-6 sm:col-3 mt-2 sm:mt-0">
            <div class="card border-0 shadow-sm text-center p-3 md:p-4">
                <p class="text-muted small mb-2">Gagal</p>
                <p class="h5 md:h3 fw-bold text-danger mb-0">{{ $reports->where('status', 'failed')->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
