@extends('layouts.admin')

@section('title', 'Manajemen Rekening Bank')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .account-card {
            transition: all 0.3s ease;
            border-left: 4px solid #4e73df;
        }

        .account-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
        }

        .badge-active {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: white;
        }

        .badge-inactive {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Header Hero Section --}}
        <div class="card border-0 shadow-lg mb-4 overflow-hidden"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 bg-white bg-opacity-25 rounded-3 p-3">
                                <i class="bi bi-bank2 fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Manajemen Rekening Bank</h2>
                                <p class="mb-0 opacity-90 small">Kelola nomor rekening bank untuk pembayaran pesanan pelanggan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-light btn-lg shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Rekening
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Section --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Rekening Aktif</p>
                                <h3 class="mb-0 fw-bold text-success">
                                    {{ $bankAccounts->where('is_active', true)->count() }}
                                </h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="bi bi-check-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Total Rekening</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ $bankAccounts->total() }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="bi bi-bank2 fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Accounts List --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-table me-2 text-primary"></i>Daftar Rekening Bank
                </h5>
                <span class="badge bg-primary rounded-pill">{{ $bankAccounts->total() }} item</span>
            </div>

            @if ($bankAccounts->count() > 0)
                <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach ($bankAccounts as $bank)
                            <div class="col-lg-6">
                                <div class="card account-card border shadow-sm rounded-3 h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $bank->bank_name }}</h6>
                                                <small class="text-muted">{{ $bank->account_holder }}</small>
                                            </div>
                                            @if ($bank->is_active)
                                                <span class="badge badge-active">Aktif</span>
                                            @else
                                                <span class="badge badge-inactive">Tidak Aktif</span>
                                            @endif
                                        </div>

                                        <div class="p-3 bg-light rounded-2 mb-3">
                                            <small class="text-muted d-block mb-1">Nomor Rekening</small>
                                            <code class="fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: 1px;">
                                                {{ $bank->account_number }}
                                            </code>
                                        </div>

                                        @if ($bank->notes)
                                            <div class="mb-3 p-2 bg-info bg-opacity-10 border-start border-info border-2 rounded">
                                                <small class="text-muted d-block"><strong>Catatan:</strong></small>
                                                <small class="text-dark">{{ $bank->notes }}</small>
                                            </div>
                                        @endif

                                        <div class="text-muted small mb-3">
                                            Dibuat: {{ $bank->created_at->format('d M Y H:i') }}
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.bank-accounts.edit', $bank) }}"
                                                class="btn btn-sm btn-primary rounded-2 flex-grow-1">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.bank-accounts.destroy', $bank) }}" method="POST"
                                                class="d-inline flex-grow-1" onsubmit="return confirm('Yakin ingin menghapus rekening ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-2 w-100">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 border-top bg-light">
                    {{ $bankAccounts->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Belum ada rekening bank</h5>
                        <p class="text-muted">Mulai tambahkan rekening bank untuk pembayaran pesanan</p>
                        <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Rekening Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
