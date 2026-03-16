@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15); }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="position-relative overflow-hidden mb-4 rounded-3 shadow-sm"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); padding: 2rem;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 2;">
                <i class="bi bi-person-badge" style="font-size: 8rem; color: white;"></i>
            </div>
            <div class="position-relative d-flex justify-content-between align-items-center" style="z-index: 3;">
                <div class="d-flex align-items-center text-white">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold text-shadow">{{ $user->name }}</h2>
                        <p class="mb-0 opacity-75">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning text-white shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light shadow-sm text-primary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Profile Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Informasi Akun</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Role</label>
                            <div>
                                <span class="badge bg-{{ $user->role_badge_class }} fs-6 px-3 rounded-pill">
                                    {{ ucfirst($user->role?->name ?? 'User') }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Status</label>
                            <div>
                                @if ($user->is_active)
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                @else
                                    <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Bergabung Sejak</label>
                            <div class="fw-semibold text-dark">{{ $user->created_at->format('d F Y') }}</div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Kontak</label>
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-telephone me-2 text-secondary"></i> {{ $user->phone ?? '-' }}
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope me-2 text-secondary"></i> {{ $user->email }}
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted fw-bold text-uppercase">Alamat</label>
                            <p class="mb-0 text-dark bg-light p-3 rounded border">
                                {{ $user->address ?? 'Alamat belum diisi.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity / Stats -->
            <div class="col-lg-8">
                @if ($user->isCustomer())
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-primary fw-bold text-uppercase small">Total Order</h6>
                                    <h3 class="fw-bold mb-0 text-primary">{{ $stats['total_orders'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-warning bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-warning fw-bold text-uppercase small">Order Pending</h6>
                                    <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending_orders'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-success bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-success fw-bold text-uppercase small">Total Belanja</h6>
                                    <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($stats['total_spent'] ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-cart-check me-2"></i>Riwayat Order Terakhir</h6>
                            <a href="{{ route('admin.orders.index', ['search' => $user->name]) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No. Order</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($user->orders as $order)
                                            <tr>
                                                <td class="ps-4 fw-bold text-primary">#{{ $order->order_number }}</td>
                                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} border border-{{ $order->status_color }} rounded-pill px-2">
                                                        {{ $order->status_label }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light text-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat order.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Tampilan untuk Staff/Admin -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body text-center py-5">
                            <div class="mb-3 text-muted opacity-25">
                                <i class="bi bi-shield-lock" style="font-size: 4rem;"></i>
                            </div>
                            <h5>Akun Administratif</h5>
                            <p class="text-muted">User ini adalah staff/admin dan tidak memiliki riwayat belanja.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection