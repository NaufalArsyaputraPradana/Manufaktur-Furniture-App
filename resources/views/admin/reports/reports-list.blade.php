@extends('layouts.admin')

@section('title', 'Daftar Laporan')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-3">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Daftar Laporan</h1>
            <p class="text-muted small mt-1">Kelola dan lihat semua laporan yang telah dibuat</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm align-self-start">
            <i class="bi bi-plus-circle"></i>
            <span class="d-none d-sm-inline">Buat Laporan Baru</span><span class="d-sm-none">Buat</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.reports.index') }}" method="GET">
                <div class="row g-3">
                    <!-- Type Filter - Full width on mobile -->
                    <div class="col-12 col-md-4">
                        <label for="type" class="form-label small mb-1">Tipe Laporan</label>
                        <select id="type" name="type" class="form-select form-select-sm">
                            <option value="">-- Semua Tipe --</option>
                            <option value="sales" @selected(request('type') === 'sales')>Laporan Penjualan</option>
                            <option value="production" @selected(request('type') === 'production')>Laporan Produksi</option>
                            <option value="inventory" @selected(request('type') === 'inventory')>Laporan Inventori</option>
                            <option value="financial" @selected(request('type') === 'financial')>Laporan Keuangan</option>
                            <option value="custom" @selected(request('type') === 'custom')>Laporan Kustom</option>
                        </select>
                    </div>

                    <!-- Status Filter - Full width on mobile -->
                    <div class="col-12 col-md-4">
                        <label for="status" class="form-label small mb-1">Status</label>
                        <select id="status" name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="processing" @selected(request('status') === 'processing')>Sedang Diproses</option>
                            <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                            <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                        </select>
                    </div>

                    <!-- Buttons - Stack on mobile -->
                    <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 flex-md-grow-0">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1 flex-md-grow-0">
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
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 small">Judul Laporan</th>
                            <th class="px-3 py-2 small">Tipe</th>
                            <th class="px-3 py-2 small">Periode</th>
                            <th class="px-3 py-2 small">Status</th>
                            <th class="px-3 py-2 small d-none d-lg-table-cell">Dibuat Oleh</th>
                            <th class="px-3 py-2 small">Dibuat Pada</th>
                            <th class="px-3 py-2 text-end small">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($reports as $report)
                            <tr class="align-middle">
                                <td class="px-3 py-3 small fw-bold">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="text-primary text-decoration-none">
                                        {{ $report->title }}
                                    </a>
                                </td>
                                <td class="px-3 py-3 small">
                                    <span class="badge bg-info">{{ ucfirst($report->report_type) }}</span>
                                </td>
                                <td class="px-3 py-3 small text-muted">
                                    {{ \Carbon\Carbon::parse($report->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($report->end_date)->format('d M Y') }}
                                </td>
                                <td class="px-3 py-3 small">
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
                                <td class="px-3 py-3 small text-muted d-none d-lg-table-cell">
                                    {{ $report->generatedBy?->name ?? 'Admin' }}
                                </td>
                                <td class="px-3 py-3 small text-muted">
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
                                        <span class="badge bg-info">{{ ucfirst($report->report_type) }}</span>
                                        @if ($report->status === 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif ($report->status === 'processing')
                                            <span class="badge bg-warning text-dark">Sedang Diproses</span>
                                        @elseif ($report->status === 'failed')
                                            <span class="badge bg-danger">Gagal</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($report->status) }}</span>
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
            <div class="card-footer bg-light border-top p-3 p-md-4">
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="mt-4 mb-2 text-muted">Belum Ada Laporan</h5>
                <p class="text-muted small mb-4">Mulai dengan membuat laporan pertama Anda</p>
                <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Buat Laporan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Stats - Mobile responsive -->
    <div class="row g-2 g-md-4 mt-3 mt-md-4">
        <div class="col-6 col-sm-3">
            <div class="card border-0 shadow-sm text-center p-3 p-md-4">
                <p class="text-muted small mb-2">Total Laporan</p>
                <p class="fw-bold text-dark mb-0 fs-5 fs-md-3">{{ $reports->total() }}</p>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card border-0 shadow-sm text-center p-3 p-md-4">
                <p class="text-muted small mb-2">Selesai</p>
                <p class="fw-bold text-success mb-0 fs-5 fs-md-3">{{ $reports->where('status', 'completed')->count() }}</p>
            </div>
        </div>
        <div class="col-6 col-sm-3 mt-2 mt-sm-0">
            <div class="card border-0 shadow-sm text-center p-3 p-md-4">
                <p class="text-muted small mb-2">Diproses</p>
                <p class="fw-bold text-warning mb-0 fs-5 fs-md-3">{{ $reports->where('status', 'processing')->count() }}</p>
            </div>
        </div>
        <div class="col-6 col-sm-3 mt-2 mt-sm-0">
            <div class="card border-0 shadow-sm text-center p-3 p-md-4">
                <p class="text-muted small mb-2">Gagal</p>
                <p class="fw-bold text-danger mb-0 fs-5 fs-md-3">{{ $reports->where('status', 'failed')->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
