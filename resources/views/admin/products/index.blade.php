@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .opacity-90 {
            opacity: 0.9;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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
                <div class="position-absolute top-0 end-0 opacity-10"
                    style="font-size: 8rem; margin-top: -1rem; margin-right: -1rem;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3"
                                style="width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="bi bi-box-seam fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Manajemen Produk</h2>
                                <p class="mb-0 opacity-90 small">Kelola katalog furniture dan harga.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-lg-end">
                        <a href="{{ route('admin.products.create') }}"
                            class="btn btn-light btn-lg shadow-sm hover-lift text-primary fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Pencarian</label>
                        <x-search-input 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cari nama atau SKU..."
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-form-input
                            name="category_id"
                            label="Kategori"
                            type="select"
                            :options="collect(['' => 'Semua Kategori'])->union($categories->pluck('name', 'id'))"
                            :value="request('category_id')"
                        />
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <x-form-input
                            name="is_active"
                            label="Status"
                            type="select"
                            :options="['1' => 'Aktif', '0' => 'Non-Aktif']"
                            :value="request('is_active')"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary shadow-sm"
                                title="Reset">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-primary"></i>Daftar Produk</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                    Total: {{ $products->total() }}
                </span>
            </div>
            <div class="card-body p-0">
                @if ($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-bottom-0 fw-bold text-secondary text-uppercase small"
                                        width="5%">No</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small"
                                        width="30%">Produk</th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">Kategori
                                    </th>
                                    <th class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small">Harga</th>
                                    <th
                                        class="py-3 border-bottom-0 fw-bold text-secondary text-uppercase small text-center">
                                        Status</th>
                                    <th
                                        class="pe-4 py-3 border-bottom-0 fw-bold text-secondary text-uppercase small text-end">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $products->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                                                        class="rounded shadow-sm border"
                                                        style="width: 50px; height: 50px; object-fit: cover; cursor:pointer;"
                                                        onclick="showImageModal('{{ $product->thumbnail }}', '{{ $product->name }}')">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                                        {{ $product->name }}</div>
                                                    <small class="text-muted d-block">SKU: <span
                                                            class="font-monospace">{{ $product->sku }}</span></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">
                                                {{ $product->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($product->base_price !== null)
                                                <div class="fw-bold text-success">Rp
                                                    {{ number_format($product->base_price, 0, ',', '.') }}</div>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                    <i class="bi bi-chat-dots me-1"></i>Tanya Harga
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($product->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i
                                                        class="bi bi-check-circle me-1"></i>Aktif</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill"><i
                                                        class="bi bi-slash-circle me-1"></i>Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                    class="btn btn-sm btn-info text-white shadow-sm"
                                                    data-bs-toggle="tooltip" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="btn btn-sm btn-warning text-white shadow-sm"
                                                    data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger shadow-sm delete-confirm"
                                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                        <small class="text-muted">Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }}
                            dari {{ $products->total() }} data</small>
                        <div class="small">
                            {{ $products->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3 text-muted opacity-25">
                            <i class="bi bi-box-seam" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted fw-bold">Belum ada produk</h5>
                        <p class="text-muted mb-4">Mulai tambahkan produk ke katalog Anda.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
            document.querySelectorAll('.delete-confirm').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    Swal.fire({
                        title: 'Hapus Produk?',
                        html: `Anda yakin ingin menghapus produk <strong>${name}</strong>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b',
                        cancelButtonColor: '#858796',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = "{{ route('admin.products.index') }}/" + id;
                            form.innerHTML = `@csrf @method('DELETE')`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
