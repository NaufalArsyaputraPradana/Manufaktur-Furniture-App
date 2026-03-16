@extends('layouts.admin')

@section('title', 'Detail Kategori')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="position-relative overflow-hidden mb-4 rounded-3 shadow-sm"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); padding: 2rem;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 2;">
                <i class="bi bi-folder-check" style="font-size: 8rem; color: white;"></i>
            </div>
            <div class="position-relative" style="z-index: 3;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-3 p-3 shadow-sm me-3">
                            <i class="bi bi-folder text-primary fs-3"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 text-white fw-bold text-shadow">{{ $category->name }}</h2>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                @if ($category->is_active)
                                    <span class="badge bg-success border border-white"><i
                                            class="bi bi-check-circle me-1"></i>Aktif</span>
                                @else
                                    <span class="badge bg-secondary border border-white"><i
                                            class="bi bi-x-circle me-1"></i>Non-Aktif</span>
                                @endif
                                <span class="text-white opacity-75 small">| Slug: {{ $category->slug }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light shadow-sm me-2">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <a href="{{ route('admin.categories.edit', $category) }}"
                            class="btn btn-warning text-white shadow-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text me-2"></i>Detail Informasi</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <label class="form-label text-muted small text-uppercase fw-bold mb-2">Gambar
                                    Kategori</label>
                                <div class="rounded border p-1 bg-light d-inline-block">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                            class="rounded"
                                            style="max-width: 100%; height: 150px; object-fit: cover; cursor:pointer;"
                                            onclick="showImageModal('{{ asset('storage/' . $category->image) }}', '{{ $category->name }}')">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-white rounded text-muted"
                                            style="width: 150px; height: 150px;">
                                            <div class="text-center">
                                                <i class="bi bi-image fs-1 opacity-50"></i>
                                                <span class="d-block small mt-2">Tidak ada gambar</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="text-muted small text-uppercase fw-bold">Induk Kategori</label>
                                        <div class="fs-5">
                                            @if ($category->parent)
                                                <a href="{{ route('admin.categories.show', $category->parent) }}"
                                                    class="text-decoration-none fw-semibold">
                                                    {{ $category->parent->name }} <i
                                                        class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic">- Kategori Utama -</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-muted small text-uppercase fw-bold">Dibuat Pada</label>
                                        <div class="fs-5">{{ $category->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small text-uppercase fw-bold">Deskripsi</label>
                                        <p class="mb-0 bg-light p-3 rounded border text-secondary">
                                            {{ $category->description ?? 'Tidak ada deskripsi.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($category->children->count() > 0)
                            <hr>
                            <h6 class="fw-bold mb-3">Sub-Kategori ({{ $category->children->count() }})</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($category->children as $child)
                                    <a href="{{ route('admin.categories.show', $child->id) }}"
                                        class="badge bg-light text-dark border p-2 text-decoration-none">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Products -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Produk Terkait
                            ({{ $products->total() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        @if ($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Produk</th>
                                            <th>Harga</th>
                                            <th>Estimasi (hari)</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded p-1 me-2 flex-shrink-0"
                                                            style="width: 40px; height: 40px;">
                                                            @if ($product->thumbnail)
                                                                <img src="{{ $product->thumbnail }}"
                                                                    alt="{{ $product->name }}"
                                                                    class="w-100 h-100 rounded object-fit-cover"
                                                                    style="cursor:pointer;"
                                                                    onclick="showImageModal('{{ $product->thumbnail }}', '{{ $product->name }}')">
                                                            @else
                                                                <i
                                                                    class="bi bi-box text-secondary fs-4 d-flex align-items-center justify-content-center w-100 h-100"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $product->name }}</div>
                                                            <div class="small text-muted">{{ $product->sku ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Rp {{ number_format($product->base_price ?? 0, 0, ',', '.') }}</td>
                                                <td>{{ $product->estimated_production_days ?? '-' }}</td>
                                                <td>
                                                    @if ($product->is_active)
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary">Non-Aktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="btn btn-sm btn-light text-primary" title="Detail"><i
                                                            class="bi bi-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                {{ $products->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted fs-1 opacity-50"></i>
                                <p class="text-muted mt-2">Belum ada produk di kategori ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Stats -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-bar-chart-line me-2"></i>Statistik Cepat
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Produk</span>
                            <span class="fw-bold fs-5">{{ $category->products_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Sub-Kategori</span>
                            <span class="fw-bold fs-5">{{ $category->children->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status</span>
                            @if ($category->is_active)
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Publik</span>
                            @else
                                <span class="text-danger fw-bold"><i class="bi bi-eye-slash me-1"></i>Tersembunyi</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-3 small text-uppercase">Bantuan</h6>
                        <p class="small text-muted mb-0">
                            Anda dapat mengubah detail kategori, mengganti induk kategori, atau mengupload gambar baru
                            melalui menu <strong>Edit</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
