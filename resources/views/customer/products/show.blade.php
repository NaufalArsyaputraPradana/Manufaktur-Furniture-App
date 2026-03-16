@extends('layouts.app')

@section('title', $product->name . ' - UD Bisa Furniture')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-product-detail position-relative"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" aria-label="Product detail hero section">

        {{-- Background Pattern --}}
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index') }}" class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-grid-fill me-1" aria-hidden="true"></i>Katalog
                        </a>
                    </li>
                    @if ($product->category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}"
                                class="text-white text-decoration-none hover-opacity">
                                {{ $product->category->name }}
                            </a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">
                        {{ Str::limit($product->name, 30) }}
                    </li>
                </ol>
            </nav>

            <div class="text-center text-white fade-in">
                <span class="badge bg-white bg-opacity-25 text-white px-4 py-2 mb-3 d-inline-block rounded-pill shadow-sm">
                    <i class="bi bi-box-seam me-2" aria-hidden="true"></i> Detail Produk
                </span>
                <h1 class="display-5 fw-bold mb-2">{{ $product->name }}</h1>
                <p class="lead mb-0" style="opacity: 0.9;">
                    {{ $product->category->name ?? 'Kategori Umum' }}
                    <span class="mx-2 opacity-50">•</span>
                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }} border border-light">
                        {{ $product->is_active ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                <path fill="#ffffff"
                    d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
            </svg>
        </div>
    </section>

    {{-- ===== PRODUCT DETAIL SECTION ===== --}}
    <section class="product-detail-section bg-white" aria-label="Detail produk">
        <div class="container">
            <div class="row g-4 g-lg-5">

                {{-- ===== GALLERY ===== --}}
                <aside class="col-lg-5" role="complementary" aria-label="Galeri gambar produk">
                    <div class="card border-0 shadow-sm rounded-4 sticky-gallery bg-light">
                        <div class="card-body p-0">

                            @php
                                $displayImages = [];
                                $rawPaths = is_array($product->images ?? null)
                                    ? array_filter($product->images, 'is_string')
                                    : [];

                                foreach ($rawPaths as $path) {
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                                        $displayImages[] = asset('storage/' . $path);
                                    }
                                }
                                $hasImages = count($displayImages) > 0;
                            @endphp

                            {{-- Main Carousel --}}
                            <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner rounded-top-4 bg-white">
                                    @if ($hasImages)
                                        @foreach ($displayImages as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <div class="position-relative"
                                                    onclick="openImageModal('{{ $image }}', {{ $index }})"
                                                    style="cursor: zoom-in;">
                                                    <img src="{{ $image }}" class="d-block w-100 product-main-image"
                                                        alt="{{ $product->name }} - Gambar {{ $index + 1 }}"
                                                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                                        style="height: 450px; object-fit: contain; background-color: #f8f9fa;">
                                                    <div
                                                        class="image-zoom-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                        <div class="text-white text-center">
                                                            <i class="bi bi-zoom-in display-4 mb-2 drop-shadow"
                                                                aria-hidden="true"></i>
                                                            <p class="mb-0 fw-bold drop-shadow">Klik untuk memperbesar</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="carousel-item active">
                                            <div class="d-flex align-items-center justify-content-center text-white"
                                                style="height: 450px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1rem 1rem 0 0;">
                                                <div class="text-center">
                                                    <i class="bi bi-image display-1 mb-3 opacity-50" aria-hidden="true"></i>
                                                    <p class="h5 mb-0 opacity-75">Gambar Tidak Tersedia</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($hasImages && count($displayImages) > 1)
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#productImageCarousel" data-bs-slide="prev"
                                        aria-label="Gambar sebelumnya">
                                        <span
                                            class="carousel-control-prev-icon bg-dark bg-opacity-75 rounded-circle p-3 shadow"
                                            aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#productImageCarousel" data-bs-slide="next"
                                        aria-label="Gambar selanjutnya">
                                        <span
                                            class="carousel-control-next-icon bg-dark bg-opacity-75 rounded-circle p-3 shadow"
                                            aria-hidden="true"></span>
                                    </button>
                                @endif
                            </div>

                            {{-- Thumbnails --}}
                            @if ($hasImages && count($displayImages) > 1)
                                <div class="p-3 bg-white rounded-bottom-4 border-top">
                                    <div class="row g-2">
                                        @foreach ($displayImages as $index => $image)
                                            <div class="col-3">
                                                <img src="{{ $image }}"
                                                    class="img-thumbnail thumbnail-item bg-light border-0"
                                                    data-bs-target="#productImageCarousel"
                                                    data-bs-slide-to="{{ $index }}"
                                                    onclick="openImageModal('{{ $image }}', {{ $index }})"
                                                    alt="Thumbnail {{ $index + 1 }}" loading="lazy"
                                                    style="cursor: pointer; height: 80px; width: 100%; object-fit: cover; border-radius: 0.5rem;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </aside>

                {{-- ===== PRODUCT INFO ===== --}}
                <main class="col-lg-7" role="main">

                    {{-- Badges Mobile View (If needed) --}}
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-25">
                            <i class="bi bi-tag-fill me-1" aria-hidden="true"></i>
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        <span
                            class="badge px-3 py-2 rounded-pill border {{ $product->is_active ? 'bg-success bg-opacity-10 text-success border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25' }}">
                            <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"
                                aria-hidden="true"></i>
                            {{ $product->is_active ? 'Stok Tersedia' : 'Kosong / Pre-Order' }}
                        </span>
                    </div>

                    <h2 class="h2 fw-bold mb-4 text-dark">{{ $product->name }}</h2>

                    {{-- Price Card --}}
                    <div class="card border-0 bg-light rounded-4 mb-4 p-4 shadow-sm border border-light">
                        @if ($product->base_price !== null)
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1 fw-bold text-uppercase">
                                        <i class="bi bi-tag me-1" aria-hidden="true"></i> Harga Mulai Dari
                                    </small>
                                    <div class="d-flex align-items-baseline">
                                        <h3 class="h2 text-primary mb-0 fw-bold me-2" data-price="{{ $product->base_price }}"
                                            data-currency="IDR">
                                            Rp {{ number_format($product->base_price, 0, ',', '.') }}
                                        </h3>
                                        <span class="text-muted fw-medium">/ unit</span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <div
                                        class="d-flex align-items-center justify-content-md-end bg-white p-2 rounded-3 border">
                                        <i class="bi bi-info-circle-fill text-warning me-2 fs-5" aria-hidden="true"></i>
                                        <small class="text-muted text-start lh-sm" style="max-width: 200px;">
                                            Harga akhir dapat disesuaikan dengan dimensi & material custom.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <small class="text-muted d-block mb-1 fw-bold text-uppercase">
                                        <i class="bi bi-tag me-1" aria-hidden="true"></i> Harga Produk
                                    </small>
                                    <p class="mb-1 text-dark fw-semibold">Harga belum ditentukan</p>
                                    <small class="text-muted">Hubungi kami untuk informasi harga terbaik sesuai kebutuhan Anda.</small>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20menanyakan%20harga%20produk%20%22{{ urlencode($product->name) }}%22"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp fs-5" aria-hidden="true"></i>
                                        Tanya Harga via WhatsApp
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Info Spec Cards --}}
                    <div class="row g-3 mb-4">
                        @if ($product->dimensions)
                            <div class="col-6 col-md-4">
                                <div class="card border-0 bg-white shadow-sm text-center p-3 h-100 rounded-3">
                                    <i class="bi bi-arrows-fullscreen fs-3 text-primary mb-2" aria-hidden="true"></i>
                                    <p class="small mb-1 fw-bold text-muted text-uppercase">Dimensi Standar</p>
                                    <strong class="text-dark">{{ $product->dimensions }}</strong>
                                </div>
                            </div>
                        @endif

                        @if ($product->wood_type)
                            <div class="col-6 col-md-4">
                                <div class="card border-0 bg-white shadow-sm text-center p-3 h-100 rounded-3">
                                    <i class="bi bi-tree fs-3 text-success mb-2" aria-hidden="true"></i>
                                    <p class="small mb-1 fw-bold text-muted text-uppercase">Material Kayu</p>
                                    <strong class="text-dark">{{ $product->wood_type }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="col-6 col-md-4">
                            <div class="card border-0 bg-white shadow-sm text-center p-3 h-100 rounded-3">
                                <i class="bi bi-clock-history fs-3 text-warning mb-2" aria-hidden="true"></i>
                                <p class="small mb-1 fw-bold text-muted text-uppercase">Estimasi Produksi</p>
                                <strong class="text-dark">{{ $product->estimated_production_days ?? '2-4' }} Hari</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-body p-4 p-lg-5">
                            <h3 class="h5 fw-bold mb-3 d-flex align-items-center border-bottom pb-2">
                                <i class="bi bi-card-text text-primary me-2" aria-hidden="true"></i> Deskripsi Produk
                            </h3>
                            <div class="text-muted lh-lg" style="font-size: 1.05rem;">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Call to Action / Order Box --}}
                    @auth
                        @if (auth()->user()?->role?->name === 'customer' && $product->is_active)
                            @if ($product->base_price !== null)
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                                <div class="card-header border-0 p-4"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h3 class="h5 mb-0 text-white fw-bold d-flex align-items-center">
                                        <i class="bi bi-cart-plus-fill me-2" aria-hidden="true"></i>
                                        Pesan Sekarang
                                    </h3>
                                </div>
                                <div class="card-body p-4 bg-white">
                                    <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form"
                                        id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="product_name" value="{{ $product->name }}">
                                        <input type="hidden" name="price" value="{{ $product->base_price }}">

                                        {{-- Quantity --}}
                                        <div class="mb-4">
                                            <label for="quantityInput" class="form-label fw-bold text-dark mb-2">Jumlah
                                                Pesanan</label>
                                            <div class="input-group input-group-lg" style="max-width: 250px;">
                                                <button class="btn btn-light border text-primary fw-bold" type="button"
                                                    id="decreaseQty" aria-label="Kurangi jumlah">
                                                    <i class="bi bi-dash-lg" aria-hidden="true"></i>
                                                </button>
                                                <input type="number"
                                                    class="form-control text-center fw-bold fs-5 border-light bg-light"
                                                    name="quantity" id="quantityInput" value="1" min="1"
                                                    max="100" required aria-label="Jumlah produk">
                                                <button class="btn btn-light border text-primary fw-bold" type="button"
                                                    id="increaseQty" aria-label="Tambah jumlah">
                                                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-3">
                                            <button type="submit"
                                                class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm d-flex justify-content-center align-items-center">
                                                <i class="bi bi-cart-plus-fill me-2 fs-5" aria-hidden="true"></i> Tambah ke
                                                Keranjang
                                            </button>

                                            <div class="position-relative text-center my-2">
                                                <hr class="text-muted opacity-25">
                                                <span
                                                    class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-medium text-uppercase">Atau</span>
                                            </div>

                                            <a href="{{ route('customer.orders.custom', ['product_id' => $product->id]) }}"
                                                class="btn btn-outline-primary btn-lg rounded-3 fw-bold border-2">
                                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i> Pesan dengan
                                                Custom Desain
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @else
                            {{-- No Price — WhatsApp CTA --}}
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                                <div class="card-header border-0 p-4"
                                    style="background: linear-gradient(135deg, #25d366 0%, #128C7E 100%);">
                                    <h3 class="h5 mb-0 text-white fw-bold d-flex align-items-center">
                                        <i class="bi bi-whatsapp me-2 fs-4" aria-hidden="true"></i>
                                        Tanya Harga Produk Ini
                                    </h3>
                                </div>
                                <div class="card-body p-4 bg-white">
                                    <p class="text-muted mb-4 lh-base">
                                        Harga produk <strong class="text-dark">{{ $product->name }}</strong> belum ditentukan. Hubungi kami melalui WhatsApp untuk mendapatkan penawaran harga terbaik yang sesuai dengan kebutuhan Anda.
                                    </p>
                                    <div class="d-grid gap-3">
                                        <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20menanyakan%20harga%20produk%20%22{{ urlencode($product->name) }}%22.%20Bisakah%20Anda%20memberikan%20informasi%20harga%3F"
                                            target="_blank" rel="noopener noreferrer"
                                            class="btn btn-success btn-lg rounded-3 fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                            <i class="bi bi-whatsapp fs-5" aria-hidden="true"></i>
                                            Tanya Harga via WhatsApp
                                        </a>
                                        <a href="{{ route('customer.orders.custom', ['product_id' => $product->id]) }}"
                                            class="btn btn-outline-success btn-lg rounded-3 fw-bold border-2">
                                            <i class="bi bi-pencil-square me-2" aria-hidden="true"></i> Ajukan Desain Custom
                                        </a>
                                    </div>
                                    <div class="mt-3 p-3 bg-success bg-opacity-10 rounded-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-clock text-success" aria-hidden="true"></i>
                                        <small class="text-muted">Kami biasanya membalas dalam 1-2 jam kerja.</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @elseif (!$product->is_active)
                            <div class="alert alert-warning rounded-4 shadow-sm border-0 p-4 d-flex align-items-start"
                                role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3 flex-shrink-0"
                                    aria-hidden="true"></i>
                                <div>
                                    <h4 class="alert-heading h5 fw-bold text-dark mb-2">Produk Tidak Tersedia</h4>
                                    <p class="mb-3 text-muted">Mohon maaf, produk ini sedang tidak dapat dipesan atau sedang
                                        dalam tahap produksi penuh.</p>
                                    <a href="https://wa.me/6285290505442?text=Halo%20Bisa%20Furniture,%20apakah%20produk%20{{ $product->name }}%20masih%20tersedia?"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn btn-warning fw-bold shadow-sm rounded-pill px-4">
                                        <i class="bi bi-whatsapp me-2" aria-hidden="true"></i> Tanya Admin via WhatsApp
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Guest State --}}
                        <div class="card border-0 shadow-sm rounded-4 bg-light overflow-hidden">
                            <div class="card-body text-center p-5">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-4"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-lock-fill fs-1 text-primary" aria-hidden="true"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3 text-dark">Akses Terbatas</h3>
                                <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                                    Silakan masuk ke akun Anda terlebih dahulu untuk melihat fitur pemesanan dan kustomisasi
                                    dimensi produk.
                                </p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('login') }}"
                                        class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i> Login
                                    </a>
                                    <a href="{{ route('register') }}"
                                        class="btn btn-outline-primary px-4 py-2 rounded-pill fw-medium bg-white">
                                        Daftar Akun
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth

                </main>
            </div>
        </div>
    </section>

    {{-- ===== RELATED PRODUCTS ===== --}}
    @if (isset($relatedProducts) && $relatedProducts->isNotEmpty())
        <section class="related-products-section bg-light" aria-label="Produk terkait">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 mb-3 d-inline-block rounded-pill">
                        <i class="bi bi-stars me-1" aria-hidden="true"></i> Rekomendasi
                    </span>
                    <h2 class="h2 fw-bold mb-3 text-dark">Produk Serupa Lainnya</h2>
                    <p class="text-muted mb-0">Mungkin Anda juga tertarik dengan koleksi berikut</p>
                </div>

                <div class="row g-4 justify-content-center" role="list" aria-label="Daftar produk terkait">
                    @foreach ($relatedProducts as $rel)
                        <div class="col-12 col-sm-6 col-lg-3" role="listitem">
                            <article class="product-card card h-100 border-0 shadow-sm rounded-4 animate-on-scroll">
                                <div class="product-image-wrapper position-relative overflow-hidden bg-white">
                                    @php
                                        $relatedImage = null;
                                        if (
                                            is_array($rel->images ?? null) &&
                                            count($rel->images) > 0 &&
                                            is_string($rel->images[0]) &&
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists($rel->images[0])
                                        ) {
                                            $relatedImage = asset('storage/' . $rel->images[0]);
                                        }
                                    @endphp

                                    @if ($relatedImage)
                                        <img src="{{ $relatedImage }}" class="card-img-top product-image w-100"
                                            alt="{{ $rel->name }}" loading="lazy"
                                            style="height: 220px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="card-img-top d-none align-items-center justify-content-center text-white"
                                            style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <div class="text-center">
                                                <i class="bi bi-image display-4 mb-2 opacity-50" aria-hidden="true"></i>
                                                <p class="small mb-0 opacity-75 px-3">{{ Str::limit($rel->name, 20) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center text-white"
                                            style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <div class="text-center">
                                                <i class="bi bi-image display-4 mb-2 opacity-50" aria-hidden="true"></i>
                                                <p class="small mb-0 opacity-75 px-3">{{ Str::limit($rel->name, 20) }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="position-absolute bottom-0 start-0 w-100 p-3"
                                        style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                                        <span class="badge bg-white text-dark shadow-sm">
                                            {{ $rel->category->name ?? 'Kategori Umum' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column p-4">
                                    <h3 class="h6 card-title mb-2 fw-bold text-dark">{{ $rel->name }}</h3>
                                    <div class="price-box mb-3 p-2 bg-light rounded-3 text-center mt-auto border">
                                        @if ($rel->base_price !== null)
                                            <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">MULAI
                                                DARI</small>
                                            <span class="fw-bold text-primary">Rp
                                                {{ number_format($rel->base_price, 0, ',', '.') }}</span>
                                        @else
                                            <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20menanyakan%20harga%20produk%20{{ urlencode($rel->name) }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="btn btn-success btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-whatsapp" aria-hidden="true"></i> Tanya Harga
                                            </a>
                                        @endif
                                    </div>
                                    <a href="{{ route('products.show', $rel->slug ?? $rel->id) }}"
                                        class="btn btn-outline-primary w-100 rounded-3 fw-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('products.index') }}"
                        class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-grid-fill me-2" aria-hidden="true"></i> Jelajahi Semua Produk
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ===== IMAGE MODAL ===== --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4 border-0 shadow-lg bg-dark text-white">
                <div class="modal-header border-0 bg-transparent position-absolute top-0 w-100" style="z-index: 10;">
                    <h5 class="modal-title drop-shadow" id="imageModalLabel">
                        {{ $product->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup Galeri"></button>
                </div>
                <div class="modal-body p-0 position-relative d-flex align-items-center justify-content-center"
                    style="min-height: 50vh;">
                    <div class="image-loading position-absolute" style="display: none; z-index: 1;">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                    <img src="" id="modalImage" class="img-fluid" alt="Gambar produk ukuran penuh"
                        style="max-height: 85vh; width: auto; object-fit: contain;">
                </div>
                <div class="modal-footer border-0 justify-content-between position-absolute bottom-0 w-100 bg-gradient-dark"
                    style="z-index: 10;">
                    <button type="button" class="btn btn-dark bg-opacity-50 border-0 rounded-circle p-2" id="prevImage"
                        aria-label="Gambar Sebelumnya">
                        <i class="bi bi-chevron-left fs-4" aria-hidden="true"></i>
                    </button>
                    <span class="fw-bold bg-dark bg-opacity-75 px-3 py-1 rounded-pill" id="imageCounter">1 / 1</span>
                    <button type="button" class="btn btn-dark bg-opacity-50 border-0 rounded-circle p-2" id="nextImage"
                        aria-label="Gambar Selanjutnya">
                        <i class="bi bi-chevron-right fs-4" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* HERO */
        .hero-product-detail {
            padding-top: 9rem;
            padding-bottom: 7rem;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .hero-product-detail .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: rgba(255, 255, 255, 0.7);
        }

        .hover-opacity {
            transition: opacity 0.3s ease;
        }

        .hover-opacity:hover {
            opacity: 0.8;
        }

        /* WAVE */
        .wave-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 1;
        }

        .wave-bottom svg {
            display: block;
            width: 100%;
            height: 80px;
        }

        /* SECTIONS */
        .product-detail-section {
            padding-top: 2rem;
            padding-bottom: 5rem;
        }

        .related-products-section {
            padding-top: 4rem;
            padding-bottom: 5rem;
        }

        /* GALLERY */
        .sticky-gallery {
            position: sticky;
            top: 100px;
        }

        .rounded-top-4 {
            border-radius: 1rem 1rem 0 0 !important;
        }

        .rounded-bottom-4 {
            border-radius: 0 0 1rem 1rem !important;
        }

        .product-main-image {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-item:hover .product-main-image {
            transform: scale(1.03);
        }

        .image-zoom-overlay {
            pointer-events: none;
            background: rgba(0, 0, 0, 0.4);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .carousel-item:hover .image-zoom-overlay {
            opacity: 1;
        }

        .thumbnail-item {
            transition: all 0.3s ease;
            border: 2px solid transparent !important;
        }

        .thumbnail-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            border-color: #667eea !important;
        }

        /* PRODUCT CARDS */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.1) !important;
        }

        .product-image-wrapper {
            border-radius: 1rem 1rem 0 0;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }

        /* INPUTS & BUTTONS */
        #quantityInput {
            border: none !important;
            font-size: 1.25rem;
        }

        #quantityInput::-webkit-inner-spin-button,
        #quantityInput::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        #quantityInput:focus {
            box-shadow: none;
            outline: none;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63408a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
        }

        /* MODAL */
        #modalImage {
            transition: opacity 0.3s ease;
        }

        #modalImage.loading {
            opacity: 0.3;
        }

        .bg-gradient-dark {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 2rem 1rem 1rem 1rem;
        }

        .drop-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* ANIMATIONS */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .sticky-gallery {
                position: relative !important;
                top: 0 !important;
            }

            .wave-bottom svg {
                height: 60px;
            }

            .product-detail-section {
                padding-top: 3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-product-detail {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .product-detail-section {
                padding-top: 2rem;
            }

            .product-main-image {
                height: 350px !important;
            }

            .product-card:hover {
                transform: translateY(-4px);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // ============================================
            // QUANTITY CONTROLS
            // ============================================
            const qtyInput = document.getElementById('quantityInput');
            const decreaseBtn = document.getElementById('decreaseQty');
            const increaseBtn = document.getElementById('increaseQty');

            if (qtyInput && decreaseBtn && increaseBtn) {
                decreaseBtn.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val > 1) qtyInput.value = val - 1;
                });

                increaseBtn.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value) || 1;
                    const max = parseInt(qtyInput.max) || 100;
                    if (val < max) qtyInput.value = val + 1;
                });

                qtyInput.addEventListener('change', function() {
                    const min = parseInt(this.min) || 1;
                    const max = parseInt(this.max) || 100;
                    let val = parseInt(this.value) || 1;
                    this.value = Math.min(Math.max(val, min), max);
                });
            }

            // ============================================
            // ADD TO CART - SWEETALERT
            // ============================================
            const addToCartForm = document.getElementById('addToCartForm');
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = this.querySelector('input[name="product_name"]').value;
                    const qty = qtyInput ? qtyInput.value : 1;

                    Swal.fire({
                        title: 'Memproses...',
                        html: `<div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0">Menambahkan <strong>${name}</strong> (${qty} unit) ke keranjang...</p>
                               </div>`,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });

                    this.submit();
                });
            }

            // ============================================
            // IMAGE MODAL LOGIC
            // ============================================
            const productImages = @json($displayImages ?? []);
            let currentImageIndex = 0;

            window.openImageModal = function(imageSrc, index) {
                if (productImages.length === 0) return;
                currentImageIndex = index;

                const modalEl = document.getElementById('imageModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                const modalImg = document.getElementById('modalImage');
                const loading = modalEl.querySelector('.image-loading');
                const counter = document.getElementById('imageCounter');

                if (loading) loading.style.display = 'block';
                modalImg.classList.add('loading');
                modalImg.src = imageSrc;

                if (counter) counter.textContent = `${index + 1} / ${productImages.length}`;

                modalImg.onload = function() {
                    if (loading) loading.style.display = 'none';
                    modalImg.classList.remove('loading');
                };

                updateModalNav();
                modal.show();
            };

            function navigateModal(direction) {
                const newIndex = currentImageIndex + direction;
                if (newIndex < 0 || newIndex >= productImages.length) return;

                currentImageIndex = newIndex;
                const modalImg = document.getElementById('modalImage');
                const loading = document.querySelector('#imageModal .image-loading');
                const counter = document.getElementById('imageCounter');

                if (loading) loading.style.display = 'block';
                modalImg.classList.add('loading');
                modalImg.src = productImages[currentImageIndex];

                if (counter) counter.textContent = `${currentImageIndex + 1} / ${productImages.length}`;

                modalImg.onload = function() {
                    if (loading) loading.style.display = 'none';
                    modalImg.classList.remove('loading');
                };

                updateModalNav();
            }

            function updateModalNav() {
                const prevBtn = document.getElementById('prevImage');
                const nextBtn = document.getElementById('nextImage');
                if (prevBtn) prevBtn.disabled = currentImageIndex === 0;
                if (nextBtn) nextBtn.disabled = currentImageIndex === productImages.length - 1;
            }

            document.getElementById('prevImage')?.addEventListener('click', () => navigateModal(-1));
            document.getElementById('nextImage')?.addEventListener('click', () => navigateModal(1));

            document.getElementById('imageModal')?.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') navigateModal(-1);
                if (e.key === 'ArrowRight') navigateModal(1);
            });

            // Cleanup backdrop bug
            document.getElementById('imageModal')?.addEventListener('hidden.bs.modal', function() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            });

            // ============================================
            // SCROLL ANIMATION - RELATED PRODUCTS
            // ============================================
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry, index) {
                    if (entry.isIntersecting) {
                        setTimeout(function() {
                            entry.target.classList.add('visible');
                        }, index * 100);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
        });
    </script>
@endpush
