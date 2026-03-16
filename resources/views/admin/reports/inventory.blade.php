@extends('layouts.admin')

@section('title', 'Laporan Inventori')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
        }

        .modern-table tbody tr {
            transition: background-color 0.2s;
        }

        .modern-table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-box-seam fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Laporan & Analitik</h2>
                                <p class="mb-0 opacity-90 small">Ringkasan performa produk dan kuantitas terjual per
                                    periode.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.index') }}">
                            <i class="bi bi-wallet2 me-2"></i>Laporan Keuangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.sales') }}">
                            <i class="bi bi-graph-up-arrow me-2"></i>Laporan Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.production') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.production') }}">
                            <i class="bi bi-gear-wide-connected me-2"></i>Laporan Produksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.inventory') }}">
                            <i class="bi bi-box-seam me-2"></i>Inventori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.profitability') ? 'active bg-primary' : 'text-secondary' }}"
                            href="{{ route('admin.reports.profitability') }}">
                            <i class="bi bi-pie-chart me-2"></i>Profitabilitas
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filter -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-funnel me-1"></i>Filter Periode Inventori</h6>
                <form method="GET" action="{{ route('admin.reports.inventory') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control border-primary-subtle"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control border-primary-subtle"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-search me-1"></i> Tampilkan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Ringkasan Produk Terjual -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-box-seam me-2"></i>Produk Terjual
                    <span class="text-muted fw-normal ms-2">
                        ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
                    </span>
                </h6>
                <button class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Ekspor CSV</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 modern-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold" width="10%">#</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">Nama Produk</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-end pe-4">Total Unit
                                    Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productSummary as $idx => $item)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ $item->product_name ?: 'Produk Tidak Terdefinisi' }}
                                        @if (!$item->product_id)
                                            <span class="badge bg-warning text-dark ms-2 small">Custom Non-Katalog</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success fs-6 px-3 border border-success border-opacity-25">
                                            {{ number_format($item->total_sold, 0, ',', '.') }} unit
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        Tidak ada data penjualan produk untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
