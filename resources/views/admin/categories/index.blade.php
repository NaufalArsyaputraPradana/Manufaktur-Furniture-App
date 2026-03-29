@extends('layouts.admin')

@section('title', 'Kelola Kategori')

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

        .opacity-95 {
            opacity: 0.95;
        }

        .hover-lift {
            transition: transform 0.2s;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
        }

        .modern-table tbody tr {
            transition: background-color 0.2s;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 0.25rem;
            margin: 0 2px;
            border: none;
            background: #fff;
            color: #5a5c69;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .page-item.active .page-link {
            background: var(--admin-primary);
            color: white;
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
                    <i class="bi bi-folder"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3"
                                style="width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="bi bi-folder fs-3"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold text-shadow">Manajemen Kategori</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                                class="text-white text-decoration-none opacity-90">Dashboard</a></li>
                                        <li class="breadcrumb-item text-white fw-semibold opacity-95" aria-current="page">
                                            Kategori</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="d-grid">
                            <a href="{{ route('admin.categories.create') }}"
                                class="btn btn-light btn-lg shadow-sm d-flex align-items-center justify-content-center hover-lift">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Pencarian</label>
                        <x-search-input 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cari nama atau deskripsi..."
                        />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-form-input
                            name="is_active"
                            label="Status"
                            type="select"
                            :options="['1' => 'Aktif', '0' => 'Tidak Aktif']"
                            :value="request('is_active')"
                        />
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary shadow-sm">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card shadow-sm border-0 rounded-3 hover-lift">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-ul me-2"></i>Daftar Kategori</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                    Total: {{ $categories->total() }}
                </span>
            </div>
            <div class="card-body p-0">
                @if ($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table modern-table align-middle mb-0 table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-bottom-0 text-secondary text-uppercase small fw-bold"
                                        width="5%">ID</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold"
                                        width="10%">Gambar</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold"
                                        width="25%">Nama Kategori</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold"
                                        width="15%">Induk</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold text-center"
                                        width="10%">Produk</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold"
                                        width="10%">Status</th>
                                    <th class="py-3 border-bottom-0 text-secondary text-uppercase small fw-bold text-end pe-4"
                                        width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="px-4"><span class="text-muted small">#{{ $category->id }}</span></td>
                                        <td>
                                            @if ($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                    alt="{{ $category->name }}" class="rounded shadow-sm object-fit-cover"
                                                    width="45" height="45" style="cursor:pointer;"
                                                    onclick="showImageModal('{{ asset('storage/' . $category->image) }}', '{{ $category->name }}')">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-secondary"
                                                    style="width: 45px; height: 45px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $category->name }}</div>
                                            <div class="small text-muted">{{ Str::limit($category->description, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($category->parent)
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info-subtle">
                                                    {{ $category->parent->name }}
                                                </span>
                                            @else
                                                <span class="text-muted small fst-italic">- Utama -</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border shadow-sm px-3 py-2 rounded-pill">
                                                {{ $category->products_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($category->is_active)
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                                                    <i class="bi bi-x-circle me-1"></i>Non-Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.categories.show', $category) }}"
                                                    class="btn btn-sm btn-info text-white shadow-sm"
                                                    data-bs-toggle="tooltip" title="Lihat"><i class="bi bi-eye"></i></a>
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                    class="btn btn-sm btn-warning text-white shadow-sm"
                                                    data-bs-toggle="tooltip" title="Edit"><i
                                                        class="bi bi-pencil"></i></a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger shadow-sm delete-category"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    data-products="{{ $category->products_count }}"
                                                    data-children="{{ $category->children_count }}"
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
                    <!-- Pagination -->
                    <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                        <small class="text-muted">Menampilkan {{ $categories->firstItem() }} -
                            {{ $categories->lastItem() }} dari {{ $categories->total() }} data</small>
                        <div class="small">
                            {{ $categories->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3 text-muted opacity-25">
                            <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted fw-bold">Belum ada kategori</h5>
                        <p class="text-muted mb-4">Silakan tambahkan kategori produk baru Anda.</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm px-4">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
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

            document.querySelectorAll('.delete-category').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const productsCount = parseInt(this.dataset.products) || 0;
                    const childrenCount = parseInt(this.dataset.children) || 0;

                    if (productsCount > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: `Kategori "${name}" masih memiliki ${productsCount} produk. Hapus atau pindahkan produk terlebih dahulu.`,
                            confirmButtonColor: '#4e73df',
                        });
                        return;
                    }

                    if (childrenCount > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: `Kategori "${name}" masih memiliki ${childrenCount} sub-kategori. Hapus sub-kategori terlebih dahulu.`,
                            confirmButtonColor: '#4e73df',
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        html: `Akan menghapus kategori <strong>${name}</strong> secara permanen.`,
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
                            form.action = "{{ route('admin.categories.index') }}/" + id;
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
