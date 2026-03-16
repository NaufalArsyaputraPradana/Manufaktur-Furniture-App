@extends('layouts.admin')

@section('title', 'Detail Produk')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="position-relative overflow-hidden mb-4 rounded-3 shadow-sm"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); padding: 2rem;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="position-relative z-2 d-flex justify-content-between align-items-center text-white">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}"
                                    class="text-white opacity-75 text-decoration-none">Produk</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-shadow mb-0">{{ $product->name }}</h2>
                    <div class="d-flex align-items-center gap-3 mt-2 opacity-90">
                        <span class="badge bg-white text-primary"><i
                                class="bi bi-tag-fill me-1"></i>{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        <span><i class="bi bi-qr-code me-1"></i>SKU: {{ $product->sku }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning text-white shadow-sm"><i
                            class="bi bi-pencil me-2"></i>Edit</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light shadow-sm text-primary"><i
                            class="bi bi-arrow-left me-2"></i>Kembali</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Gallery -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-2">
                        <div class="bg-light rounded-3 overflow-hidden d-flex align-items-center justify-content-center mb-2"
                            style="height: 350px;">
                            <img id="mainImage" src="{{ $product->thumbnail }}" class="img-fluid"
                                style="max-height: 100%; object-fit: contain; cursor:pointer;"
                                onclick="showImageModal(this.src, '{{ $product->name }}')">
                        </div>
                        @if (!empty($product->images) && count($product->images) > 1)
                            <div class="d-flex gap-2 overflow-auto py-2">
                                @foreach ($product->gallery as $img)
                                    <img src="{{ $img }}"
                                        class="rounded border cursor-pointer thumb-img {{ $loop->first ? 'border-primary' : '' }}"
                                        style="width: 70px; height: 70px; object-fit: cover;"
                                        onclick="showImageModal('{{ $img }}', '{{ $product->name }}')">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-3 text-uppercase small">Status & Opsi</h6>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <span>Status Publikasi</span>
                            @if ($product->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-slash-circle me-1"></i>Non-Aktif</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Kustomisasi</span>
                            @if ($product->is_customizable)
                                <span class="badge bg-primary"><i class="bi bi-check-lg me-1"></i>Ya</span>
                            @else
                                <span class="badge bg-light text-dark border">Tidak</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        @if ($product->base_price !== null)
                            <h4 class="fw-bold text-success mb-3">Rp {{ number_format($product->base_price, 0, ',', '.') }}</h4>
                        @else
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 px-3 py-2">
                                    <i class="bi bi-chat-dots me-1"></i>Harga Belum Ditentukan
                                </span>
                                <a href="https://wa.me/6285290505442?text=Halo%20Admin%2C%20saya%20ingin%20mengetahui%20harga%20produk%20{{ urlencode($product->name) }}"
                                   target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                    <i class="bi bi-whatsapp me-1"></i>Tanya Harga
                                </a>
                            </div>
                        @endif
                        <p class="text-muted" style="white-space: pre-line;">
                            {{ $product->description ?? 'Tidak ada deskripsi.' }}</p>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3"><i class="bi bi-rulers me-2 text-primary"></i>Spesifikasi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Dimensi</small>
                                    <span class="fw-semibold">{{ $product->dimensions ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Estimasi Produksi</small>
                                    <span class="fw-semibold">{{ $product->estimated_production_days }} Hari Kerja</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Material Kayu</small>
                                    <span class="fw-semibold">{{ $product->wood_type ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Finishing</small>
                                    <span class="fw-semibold">{{ $product->finishing_type ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Dibuat pada:
                            {{ $product->created_at->format('d F Y, H:i') }}</small>
                        <small class="text-muted d-block">Terakhir diupdate:
                            {{ $product->updated_at->format('d F Y, H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumb-img').forEach(el => el.classList.remove('border-primary'));
            element.classList.add('border-primary');
        }
    </script>
@endpush
