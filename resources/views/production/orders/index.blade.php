@extends('layouts.production')

@section('title', 'Daftar Order Produksi')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Order Produksi</h4>
            <p class="text-muted mb-0 small">Order yang siap masuk atau sedang dalam proses produksi</p>
        </div>
        <a href="{{ route('production.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-table text-primary me-2"></i>Daftar Order</h5>
                    <span class="badge bg-primary">{{ $orders->count() }} order</span>
                </div>

                @if ($orders->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">#</th>
                                    <th>No. Order</th>
                                    <th>Customer</th>
                                    <th>Produk</th>
                                    <th>Status Order</th>
                                    <th style="min-width:180px;">Progress Produksi</th>
                                    <th>Tanggal</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $i => $order)
                                    @php
                                        $processes = $order->productionProcesses;
                                        $totalProc = $processes?->count() ?? 0;
                                        $doneProc = $processes?->where('status', 'completed')->count() ?? 0;
                                        $avgPct = $totalProc > 0 ? round($processes->avg('progress_percentage')) : 0;
                                    @endphp
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $order->user?->name ?? 'Guest' }}</span>
                                            @if ($order->user?->email)
                                                <br><small class="text-muted">{{ $order->user->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @forelse($order->orderDetails?->take(2) ?? [] as $detail)
                                                <div class="text-truncate" style="max-width:180px;"
                                                    title="{{ $detail->product_name ?? ($detail->product?->name ?? 'Custom Item') }}">
                                                    {{ $detail->product_name ?? ($detail->product?->name ?? 'Custom Item') }}
                                                </div>
                                            @empty
                                                <span class="text-muted fst-italic">â€“</span>
                                            @endforelse
                                            @if (($order->orderDetails?->count() ?? 0) > 2)
                                                <small class="text-muted">+{{ $order->orderDetails->count() - 2 }}
                                                    lainnya</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ $order->status_label ?? ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        </td>
                                        <td>
                                            @if ($totalProc > 0)
                                                <div class="progress mb-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $avgPct === 100 ? 'success' : ($avgPct >= 50 ? 'primary' : 'info') }}"
                                                        style="width: {{ $avgPct }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $doneProc }}/{{ $totalProc }} tahap Â·
                                                    {{ $avgPct }}%</small>
                                            @else
                                                <span class="text-muted fst-italic small">Belum ada proses</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('production.monitoring.order.show', $order) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill d-inline-flex align-items-center shadow-sm"
                                                title="Lihat Detail Order">
                                                <i class="bi bi-eye me-1"></i>
                                                <span>Lihat</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                        <p class="text-muted mt-2">Belum ada order yang siap diproduksi.</p>
                        <a href="{{ route('production.dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
                    </div>
                @endif
            </div>
</div>
@endsection
