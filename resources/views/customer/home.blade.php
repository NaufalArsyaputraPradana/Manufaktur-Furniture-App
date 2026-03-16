@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    {{-- ===== HERO SECTION ===== --}}
    <section class="hero position-relative" aria-label="Hero section"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">

        {{-- Decorative Background Pattern --}}
        <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.05; pointer-events: none;"
            aria-hidden="true">
            <svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="hero-pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                        <circle cx="50" cy="50" r="40" fill="white" opacity="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#hero-pattern)" />
            </svg>
        </div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4 g-lg-5">
                {{-- Left: Text --}}
                <div class="col-lg-6 text-white fade-in">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 mb-3 d-inline-block shadow-sm">
                        <i class="bi bi-star-fill me-1 text-warning" aria-hidden="true"></i> Kualitas Premium
                    </span>
                    <h1 class="display-4 fw-bold mb-3" style="line-height: 1.2;">
                        Furniture Berkualitas<br><span style="color: #FFD600;">Tinggi</span>
                    </h1>
                    <p class="lead mb-4" style="font-size: 1.1rem; opacity: 0.95;">
                        Produksi furniture custom dengan material terbaik dan craftsmanship profesional langsung dari pusat
                        pengrajin Jepara.
                    </p>
                    <div class="d-flex gap-2 gap-md-3 flex-wrap hero-buttons">
                        <a href="{{ route('products.index') }}"
                            class="btn btn-light btn-lg shadow-lg px-3 px-md-4 py-2 py-md-3 rounded-modern flex-fill flex-md-grow-0 text-center text-primary fw-bold"
                            aria-label="Lihat katalog furniture">
                            <i class="bi bi-shop me-1 me-md-2" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline">Lihat Katalog</span><span class="d-sm-none">Katalog</span>
                        </a>
                        @auth
                            <a href="{{ route('customer.orders.custom') }}"
                                class="btn btn-outline-light btn-lg px-3 px-md-4 py-2 py-md-3 rounded-modern flex-fill flex-md-grow-0 text-center fw-medium"
                                aria-label="Buat pesanan custom">
                                <i class="bi bi-pencil-square me-1 me-md-2" aria-hidden="true"></i>
                                <span class="d-none d-sm-inline">Pesanan Custom</span><span class="d-sm-none">Custom</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn btn-outline-light btn-lg px-3 px-md-4 py-2 py-md-3 rounded-modern flex-fill flex-md-grow-0 text-center fw-medium"
                                aria-label="Login ke akun Anda">
                                <i class="bi bi-box-arrow-in-right me-1 me-md-2" aria-hidden="true"></i> Login
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right: Image --}}
                <div class="col-lg-6 slide-in">
                    <div class="position-relative" style="max-width: 520px; margin: 0 auto;">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-modern"
                            style="transform: rotate(-6deg); opacity: 0.1; z-index: 0;" aria-hidden="true"></div>
                        <div class="position-absolute bottom-0 end-0 rounded-circle bg-white"
                            style="width: 150px; height: 150px; opacity: 0.08; transform: translate(30%, 30%); z-index: 0;"
                            aria-hidden="true"></div>
                        <div class="position-relative" style="z-index: 1;">
                            <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&h=600&fit=crop"
                                class="img-fluid rounded-modern shadow-xl"
                                alt="Furniture berkualitas premium untuk rumah modern" loading="lazy"
                                style="height: 400px; width: 100%; object-fit: cover; transform: rotate(2deg);">
                        </div>
                        <div class="position-absolute bg-white rounded-circle"
                            style="top: -20px; left: -20px; width: 40px; height: 40px; opacity: 0.2; z-index: 0;"
                            aria-hidden="true"></div>
                        <div class="position-absolute bg-white rounded-circle"
                            style="bottom: -10px; right: -10px; width: 60px; height: 60px; opacity: 0.15; z-index: 0;"
                            aria-hidden="true"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                <defs>
                    <linearGradient id="hero-wave-bottom" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#e3f2fd;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#bbdefb;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <path fill="url(#hero-wave-bottom)"
                    d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
            </svg>
        </div>
    </section>

    {{-- ===== QUICK ACCESS SECTION ===== --}}
    <section class="quick-access position-relative" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);"
        aria-label="Navigasi cepat">
        <div class="container position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-center gap-2 flex-wrap" style="max-width: 600px; margin: 0 auto;">

                @auth
                    @if (in_array(auth()->user()?->role?->name, ['customer', 'admin']))
                        <a href="{{ route('customer.cart.index') }}" class="text-decoration-none"
                            aria-label="Keranjang belanja - {{ $cartCount ?? 0 }} item">
                            <div class="quick-card card border-0 shadow-sm rounded-3"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100px;">
                                <div class="card-body p-2 text-center text-white">
                                    <i class="bi bi-cart3 fs-4 d-block mb-1" aria-hidden="true"></i>
                                    <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Keranjang</p>
                                    <small class="opacity-75" style="font-size: 0.6rem;">{{ $cartCount ?? 0 }} item</small>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none"
                            aria-label="Pesanan saya">
                            <div class="quick-card card border-0 shadow-sm rounded-3"
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 100px;">
                                <div class="card-body p-2 text-center text-white">
                                    <i class="bi bi-box-seam fs-4 d-block mb-1" aria-hidden="true"></i>
                                    <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Pesanan</p>
                                    <small class="opacity-75" style="font-size: 0.6rem;">Lihat</small>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('customer.orders.custom') }}" class="text-decoration-none"
                            aria-label="Buat desain custom">
                            <div class="quick-card card border-0 shadow-sm rounded-3"
                                style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 100px;">
                                <div class="card-body p-2 text-center text-white">
                                    <i class="bi bi-pencil-square fs-4 d-block mb-1" aria-hidden="true"></i>
                                    <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Custom</p>
                                    <small class="opacity-75" style="font-size: 0.6rem;">Desain</small>
                                </div>
                            </div>
                        </a>
                    @endif
                @else
                    {{-- Quick Access for Guest (Wrapped in A tags for better UX) --}}
                    <a href="{{ route('login') }}" class="text-decoration-none" aria-label="Login untuk melihat Keranjang">
                        <div class="quick-card card border-0 shadow-sm rounded-3"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100px;">
                            <div class="card-body p-2 text-center text-white">
                                <i class="bi bi-cart3 fs-4 d-block mb-1" aria-hidden="true"></i>
                                <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Keranjang</p>
                                <small class="opacity-75 text-white text-decoration-underline"
                                    style="font-size: 0.6rem;">Login</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('login') }}" class="text-decoration-none" aria-label="Login untuk melihat Pesanan">
                        <div class="quick-card card border-0 shadow-sm rounded-3"
                            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 100px;">
                            <div class="card-body p-2 text-center text-white">
                                <i class="bi bi-box-seam fs-4 d-block mb-1" aria-hidden="true"></i>
                                <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Pesanan</p>
                                <small class="opacity-75 text-white text-decoration-underline"
                                    style="font-size: 0.6rem;">Login</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('login') }}" class="text-decoration-none" aria-label="Login untuk pesanan Custom">
                        <div class="quick-card card border-0 shadow-sm rounded-3"
                            style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 100px;">
                            <div class="card-body p-2 text-center text-white">
                                <i class="bi bi-pencil-square fs-4 d-block mb-1" aria-hidden="true"></i>
                                <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Custom</p>
                                <small class="opacity-75 text-white text-decoration-underline"
                                    style="font-size: 0.6rem;">Login</small>
                            </div>
                        </div>
                    </a>
                @endauth

                {{-- General Access --}}
                <a href="{{ route('products.index') }}" class="text-decoration-none" aria-label="Lihat katalog produk">
                    <div class="quick-card card border-0 shadow-sm rounded-3"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); width: 100px;">
                        <div class="card-body p-2 text-center text-white">
                            <i class="bi bi-shop fs-4 d-block mb-1" aria-hidden="true"></i>
                            <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Katalog</p>
                            <small class="opacity-75" style="font-size: 0.6rem;">Produk</small>
                        </div>
                    </div>
                </a>
                <a href="https://wa.me/6285290505442?text=Halo%20Bisa%20Furniture%2C%20saya%20ingin%20bertanya"
                    target="_blank" rel="noopener noreferrer" class="text-decoration-none"
                    aria-label="Hubungi kami via WhatsApp">
                    <div class="quick-card card border-0 shadow-sm rounded-3"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 100px;">
                        <div class="card-body p-2 text-center text-white">
                            <i class="bi bi-whatsapp fs-4 d-block mb-1" aria-hidden="true"></i>
                            <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Kontak</p>
                            <small class="opacity-75" style="font-size: 0.6rem;">WhatsApp</small>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                <path fill="#ffffff"
                    d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
            </svg>
        </div>
    </section>

    {{-- ===== CATEGORIES SECTION ===== --}}
    <section class="categories position-relative py-4 bg-white" aria-label="Kategori produk">
        <div class="container position-relative" style="z-index: 2;">
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-2">Kategori Produk</h2>
                <p class="text-muted small">Jelajahi koleksi furniture berdasarkan kategori</p>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-3 justify-content-center">
                @forelse ($categories as $category)
                    <div class="col d-flex justify-content-center">
                        <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
                            class="text-decoration-none w-100" aria-label="Lihat produk kategori {{ $category->name }}">
                            <div class="card text-center border-0 shadow-sm hover-lift rounded-3 h-100"
                                style="min-width: 130px; min-height: 150px;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="category-icon mb-2" style="font-size: 2.2rem;" aria-hidden="true">
                                        @switch($category->name)
                                            @case('Meja')
                                                <i class="bi bi-table text-primary"></i>
                                            @break

                                            @case('Kursi')
                                                <i class="bi bi-easel text-success"></i>
                                            @break

                                            @case('Lemari')
                                                <i class="bi bi-door-closed text-info"></i>
                                            @break

                                            @case('Tempat Tidur')
                                                <i class="bi bi-moon-stars text-warning"></i>
                                            @break

                                            @case('Rak')
                                                <i class="bi bi-bookshelf text-secondary"></i>
                                            @break

                                            @case('Sofa')
                                                <i class="bi bi-basket2 text-danger"></i>
                                            @break

                                            @case('Nakas')
                                                <i class="bi bi-box2 text-success"></i>
                                            @break

                                            @case('Bufet')
                                                <i class="bi bi-tv text-primary"></i>
                                            @break

                                            @case('Kitchen Set')
                                                <i class="bi bi-cup-hot text-warning"></i>
                                            @break

                                            @case('Partisi')
                                                <i class="bi bi-columns-gap text-info"></i>
                                            @break

                                            @case('Meja Konsol')
                                                <i class="bi bi-reception-4 text-secondary"></i>
                                            @break

                                            @case('Credenza')
                                                <i class="bi bi-box-seam text-dark"></i>
                                            @break

                                            @default
                                                <i class="bi bi-box-seam text-secondary"></i>
                                        @endswitch
                                    </div>
                                    <p class="mb-0 fw-bold text-dark" style="font-size: 1.05rem;">{{ $category->name }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center mb-0 rounded-3 w-100 shadow-sm border-0">
                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i> Belum ada kategori tersedia saat
                                ini.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===== FEATURED PRODUCTS SECTION ===== --}}
        <section class="featured-products position-relative bg-light" aria-label="Produk terbaru">
            <div class="wave-top" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#ffffff"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>

            <div class="container position-relative" style="z-index: 2;">
                <div class="d-flex justify-content-between align-items-center mb-4 mb-md-5">
                    <div>
                        <h2 class="fw-bold mb-1">Produk Terbaru</h2>
                        <p class="text-muted mb-0">Koleksi furniture terbaru kami</p>
                    </div>
                    <a href="{{ route('products.index') }}"
                        class="btn btn-outline-primary rounded-modern d-none d-sm-inline-block"
                        aria-label="Lihat semua produk">
                        Lihat Semua <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @forelse ($products as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <article class="card h-100 border-0 shadow-sm hover-lift rounded-modern">
                                <div class="position-relative overflow-hidden rounded-top bg-light">
                                    @php
                                        $productImage = null;
                                        $imgPath = null;

                                        // Cleaned logic for resolving image path
                                        if ($product->primaryImage?->image_path) {
                                            $imgPath = $product->primaryImage->image_path;
                                        } elseif (
                                            $product->relationLoaded('images') &&
                                            $product->images->first()?->image_path
                                        ) {
                                            $imgPath = $product->images->first()->image_path;
                                        } elseif (
                                            is_array($product->images ?? null) &&
                                            count($product->images) > 0 &&
                                            is_string($product->images[0])
                                        ) {
                                            $imgPath = $product->images[0];
                                        }

                                        // Verify existence in local storage
                                        if (
                                            $imgPath &&
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                                        ) {
                                            $productImage = asset('storage/' . $imgPath);
                                        }
                                    @endphp

                                    @if ($productImage)
                                        <img src="{{ $productImage }}" class="card-img-top w-100"
                                            alt="{{ $product->name }}" loading="lazy"
                                            style="height: 200px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="d-none align-items-center justify-content-center bg-gradient-primary text-white"
                                            style="height: 200px;">
                                            <div class="text-center">
                                                <i class="bi bi-image display-4 mb-2 opacity-50" aria-hidden="true"></i>
                                                <p class="small mb-0 opacity-75 px-3">{{ Str::limit($product->name, 25) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-gradient-primary text-white"
                                            style="height: 200px;">
                                            <div class="text-center">
                                                <i class="bi bi-image display-4 mb-2 opacity-50" aria-hidden="true"></i>
                                                <p class="small mb-0 opacity-75 px-3">{{ Str::limit($product->name, 25) }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($product->category)
                                        <span class="badge bg-primary shadow-sm position-absolute top-0 end-0 m-2">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column p-3">
                                    <h3 class="h6 card-title fw-bold mb-2">{{ $product->name }}</h3>
                                    <p class="card-text text-muted small mb-3 flex-fill">
                                        {{ Str::limit($product->description, 60) }}
                                    </p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="h6 text-primary mb-0 fw-bold">
                                                @if ($product->base_price !== null)
                                                    <span class="price-convert" data-price="{{ $product->base_price }}"
                                                        data-currency="IDR">
                                                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20menanyakan%20harga%20produk%20{{ urlencode($product->name) }}"
                                                        target="_blank" rel="noopener noreferrer"
                                                        class="btn btn-success btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-whatsapp" aria-hidden="true"></i> Tanya Harga
                                                    </a>
                                                @endif
                                            </span>
                                        </div>
                                        <a href="{{ route('products.show', $product->slug ?? $product->id) }}"
                                            class="btn btn-primary btn-sm w-100 rounded-modern fw-medium"
                                            aria-label="Lihat detail {{ $product->name }}">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center mb-0 rounded-modern shadow-sm border-0">
                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i> Belum ada produk tersedia saat ini.
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="text-center mt-4 d-sm-none">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-modern w-100">
                        Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </section>

        {{-- ===== CUSTOM ORDER CTA SECTION ===== --}}
        <section class="custom-order-cta position-relative"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" aria-label="Pesan Custom Furniture">
            <div class="wave-top" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#f8f9fa"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>

            <div class="container position-relative" style="z-index: 2;">
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-8">
                        <h2 class="h3 text-white fw-bold mb-3">
                            <i class="bi bi-pencil-square me-2" aria-hidden="true"></i> Butuh Furniture Custom?
                        </h2>
                        <p class="text-white mb-4" style="font-size: 1.05rem; opacity: 0.95;">
                            Kami siap mewujudkan desain impian Anda! Pesan furniture sesuai dimensi, material, dan finishing
                            yang Anda inginkan.
                        </p>
                        <div class="row g-3 text-white">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5" aria-hidden="true"></i>
                                    <span>Konsultasi gratis dengan tim desain</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5" aria-hidden="true"></i>
                                    <span>Material berkualitas tinggi</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5" aria-hidden="true"></i>
                                    <span>Estimasi harga transparan</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2 fs-5" aria-hidden="true"></i>
                                    <span>Progress tracking real-time</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        @auth
                            <a href="{{ route('customer.orders.custom') }}"
                                class="btn btn-light btn-lg px-4 py-3 rounded-modern shadow-lg d-block d-lg-inline-block fw-bold"
                                aria-label="Buat pesanan furniture custom">
                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i> Buat Pesanan Custom
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn btn-light btn-lg px-4 py-3 rounded-modern shadow-lg mb-2 d-block fw-bold"
                                aria-label="Login untuk membuat pesanan custom">
                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i> Buat Pesanan Custom
                            </a>
                            <p class="text-white small mb-0" style="opacity: 0.85;">
                                <i class="bi bi-info-circle me-1" aria-hidden="true"></i> Login untuk membuat pesanan
                            </p>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="wave-bottom" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#ffffff"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>
        </section>

        {{-- ===== FEATURES SECTION ===== --}}
        <section class="features position-relative py-5 bg-white" aria-label="Keunggulan kami">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">Mengapa Memilih Kami?</h2>
                    <p class="text-muted">Komitmen kami untuk memberikan pelayanan terbaik</p>
                </div>
                <div class="row g-4">
                    <div class="col-6 col-md-3 text-center">
                        <article class="feature-item">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3" aria-hidden="true">
                                <i class="bi bi-award fs-1 text-primary"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2 text-dark">Kualitas Premium</h3>
                            <p class="text-muted small mb-0">Material berkualitas tinggi dan proses produksi profesional</p>
                        </article>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <article class="feature-item">
                            <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3" aria-hidden="true">
                                <i class="bi bi-tools fs-1 text-success"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2 text-dark">Custom Design</h3>
                            <p class="text-muted small mb-0">Sesuaikan dimensi dan desain sesuai kebutuhan Anda</p>
                        </article>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <article class="feature-item">
                            <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3" aria-hidden="true">
                                <i class="bi bi-truck fs-1 text-info"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2 text-dark">Pengiriman Cepat</h3>
                            <p class="text-muted small mb-0">Estimasi pengerjaan 2-4 minggu dengan tracking real-time</p>
                        </article>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <article class="feature-item">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3" aria-hidden="true">
                                <i class="bi bi-shield-check fs-1 text-warning"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2 text-dark">Garansi Produk</h3>
                            <p class="text-muted small mb-0">Garansi kualitas dan kepuasan pelanggan terjamin</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== PROCESS TIMELINE SECTION ===== --}}
        <section class="process-timeline position-relative"
            style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);"
            aria-label="Alur Proses Pembuatan Furniture">
            <div class="wave-top" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#ffffff"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>

            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5 pb-2">
                    <h2 class="fw-bold mb-3 fs-1">Alur Pembuatan Furniture</h2>
                    <p class="text-muted fs-5 mb-0">Dari konsep hingga pengiriman, kami pastikan kualitas terbaik untuk Anda
                    </p>
                </div>

                <div class="row g-4 justify-content-center mb-5">
                    @php
                        $timelineSteps = [
                            [
                                'num' => 1,
                                'color' => 'primary',
                                'icon' => 'chat-dots',
                                'title' => 'Konsultasi',
                                'desc' => 'Diskusi desain dan spesifikasi furniture impian Anda dengan tim ahli kami.',
                            ],
                            [
                                'num' => 2,
                                'color' => 'success',
                                'icon' => 'pencil-square',
                                'title' => 'Desain',
                                'desc' => 'Tim desainer profesional membuat referensi/mockup sesuai kebutuhan Anda.',
                            ],
                            [
                                'num' => 3,
                                'color' => 'info',
                                'icon' => 'hammer',
                                'title' => 'Produksi',
                                'desc' =>
                                    'Proses pembuatan dengan material berkualitas tinggi dan craftmanship terbaik.',
                            ],
                            [
                                'num' => 4,
                                'color' => 'warning',
                                'icon' => 'truck',
                                'title' => 'Pengiriman',
                                'desc' => 'Furniture dikirim dengan aman dan langsung dipasang di lokasi Anda.',
                            ],
                        ];
                    @endphp

                    @foreach ($timelineSteps as $step)
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="timeline-card h-100 text-center p-4 bg-white rounded-3 position-relative shadow-sm">
                                <span
                                    class="timeline-badge position-absolute top-0 start-50 translate-middle badge rounded-circle bg-{{ $step['color'] }} fs-6 fw-bold shadow"
                                    aria-label="Tahap {{ $step['num'] }}">
                                    {{ $step['num'] }}
                                </span>
                                <div class="mx-auto mb-3 mt-4">
                                    <div
                                        class="timeline-icon-circle rounded-circle bg-{{ $step['color'] }} bg-opacity-10 d-inline-flex align-items-center justify-content-center p-4">
                                        <i class="bi bi-{{ $step['icon'] }} fs-1 text-{{ $step['color'] }}"
                                            aria-hidden="true"></i>
                                    </div>
                                </div>
                                <h3 class="h5 fw-bold mb-3 text-dark">{{ $step['title'] }}</h3>
                                <p class="text-muted mb-0 lh-base small">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5 pt-3">
                    <div class="d-inline-block bg-white rounded-3 px-4 py-3 mb-4 shadow-sm">
                        <i class="bi bi-clock-history me-2 text-primary fs-5" aria-hidden="true"></i>
                        <span class="text-dark fw-semibold">Estimasi Waktu:</span>
                        <span class="text-muted ms-2">2-4 minggu tergantung kompleksitas desain</span>
                    </div>
                    <div>
                        @auth
                            <a href="{{ route('customer.orders.custom') }}"
                                class="btn btn-primary btn-lg rounded-modern shadow-sm px-4 px-sm-5 py-3 fw-bold"
                                aria-label="Mulai pesanan furniture custom sekarang">
                                <i class="bi bi-arrow-right-circle me-2" aria-hidden="true"></i> Mulai Pesanan Sekarang
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn btn-primary btn-lg rounded-modern shadow-sm px-4 px-sm-5 py-3 fw-bold"
                                aria-label="Login untuk memulai pesanan">
                                <i class="bi bi-arrow-right-circle me-2" aria-hidden="true"></i> Login untuk Memulai
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="wave-bottom" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#ffffff"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>
        </section>

        {{-- ===== TESTIMONIALS SECTION ===== --}}
        <section class="testimonials position-relative bg-white" aria-label="Testimoni pelanggan">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">Apa Kata Pelanggan Kami?</h2>
                    <p class="text-muted">Kepuasan pelanggan adalah prioritas utama produksi kami</p>
                </div>

                <div class="row g-4">
                    @php
                        $testimonials = [
                            [
                                'name' => 'Budi Santoso',
                                'city' => 'Jakarta',
                                'color' => 'primary',
                                'text' =>
                                    'Kualitas furniture sangat memuaskan! Desain custom sesuai dengan keinginan saya. Proses pembuatan juga transparan dan bisa dipantau real-time.',
                            ],
                            [
                                'name' => 'Siti Nurhaliza',
                                'city' => 'Bandung',
                                'color' => 'success',
                                'text' =>
                                    'Pelayanan sangat profesional dari konsultasi hingga pengiriman. Material yang digunakan premium dan finishing-nya sangat rapi. Highly recommended!',
                            ],
                            [
                                'name' => 'Ahmad Rizki',
                                'city' => 'Surabaya',
                                'color' => 'info',
                                'text' =>
                                    'Saya pesan lemari pakaian custom. Hasilnya melebihi ekspektasi! Dimensinya sangat pas dengan ruangan, kualitas kayu kokoh, dan harga sangat bersaing.',
                            ],
                        ];
                    @endphp
                    @foreach ($testimonials as $testimonial)
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100 hover-lift rounded-modern bg-light">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="mb-3" aria-label="Rating 5 bintang">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="bi bi-star-fill text-warning" aria-hidden="true"></i>
                                        @endfor
                                    </div>
                                    <p class="mb-4 text-muted flex-grow-1 fst-italic">"{{ $testimonial['text'] }}"</p>
                                    <div class="d-flex align-items-center mt-auto pt-3 border-top">
                                        <div class="rounded-circle bg-{{ $testimonial['color'] }} text-{{ $testimonial['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center fw-bold fs-5"
                                            style="width: 50px; height: 50px;" aria-hidden="true">
                                            {{ substr($testimonial['name'], 0, 1) }}
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-bold text-dark">{{ $testimonial['name'] }}</h6>
                                            <small class="text-muted">{{ $testimonial['city'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5">
                    <p class="text-muted mb-0">
                        <i class="bi bi-people-fill me-2 text-primary fs-5" aria-hidden="true"></i>
                        Telah dipercaya oleh lebih dari <strong>500+</strong> pelanggan di seluruh Indonesia
                    </p>
                </div>
            </div>

            <div class="wave-bottom" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#f8f9fa"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>
        </section>

        {{-- ===== FAQ SECTION ===== --}}
        <section class="faq position-relative py-5 bg-light" aria-label="Pertanyaan Umum">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">Pertanyaan Seputar Layanan Kami</h2>
                    <p class="text-muted">Jawaban cepat untuk pertanyaan yang sering diajukan</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion shadow-sm rounded-modern" id="faqAccordion">
                            @php
                                $faqs = [
                                    [
                                        'id' => 'faq1',
                                        'open' => true,
                                        'question' => 'Berapa lama estimasi waktu pengerjaan furniture custom?',
                                        'answer' =>
                                            'Waktu pengerjaan normal berkisar antara <strong>2-4 minggu</strong>. Hal ini bergantung pada tingkat kerumitan desain, ukuran, dan ketersediaan material khusus. Anda dapat memantau setiap progres pengerjaan secara transparan (real-time) melalui menu tracking di dashboard akun Anda.',
                                    ],
                                    [
                                        'id' => 'faq2',
                                        'open' => false,
                                        'question' => 'Apakah UD Bisa Furniture memberikan jaminan/garansi?',
                                        'answer' =>
                                            'Ya, kami selalu berkomitmen pada kualitas. Kami memberikan <strong>garansi 1 tahun</strong> penuh untuk kerusakan produksi (seperti retak kayu alami atau cacat konstruksi). Namun, garansi tidak berlaku untuk kerusakan akibat kelalaian pemakaian (terbentur benda keras, basah berlebih) atau force majeure.',
                                    ],
                                    [
                                        'id' => 'faq3',
                                        'open' => false,
                                        'question' => 'Metode pembayaran apa saja yang tersedia?',
                                        'answer' =>
                                            'Sistem kami terintegrasi dengan Midtrans, sehingga Anda dapat membayar menggunakan <strong>Virtual Account (BCA, BNI, Mandiri, BRI), E-Wallet (GoPay, OVO, ShopeePay), maupun Kartu Kredit</strong>. Untuk pesanan custom skala besar, pembayaran dapat dicicil (DP minimal 50% di awal, sisa pelunasan sebelum barang dikirim).',
                                    ],
                                    [
                                        'id' => 'faq4',
                                        'open' => false,
                                        'question' => 'Dapatkah saya meminta/request material kayu secara khusus?',
                                        'answer' =>
                                            'Tentu saja! Mengingat kami berbasis di Jepara, kami dapat menyediakan berbagai pilihan kayu premium seperti <strong>Kayu Jati (Perhutani kelas A/B/C), Mahoni, Sungkai, hingga material plywood HPL</strong>. Tim ahli kami siap merekomendasikan opsi terbaik yang sesuai dengan anggaran dan desain Anda.',
                                    ],
                                    [
                                        'id' => 'faq5',
                                        'open' => false,
                                        'question' => 'Bagaimana sistem dan biaya pengirimannya?',
                                        'answer' =>
                                            'Biaya pengiriman akan disesuaikan dengan volume metrik (ukuran barang) dan jarak kota tujuan. Khusus untuk area eks-Karesidenan Jepara & Semarang, kami memberikan <strong>gratis ongkos kirim</strong> untuk total transaksi di atas Rp 5.000.000. Untuk pengiriman luar pulau, kami bekerja sama dengan ekspedisi cargo terpercaya.',
                                    ],
                                ];
                            @endphp

                            @foreach ($faqs as $faq)
                                <div class="accordion-item border-0 mb-2 rounded-modern overflow-hidden">
                                    <h3 class="accordion-header">
                                        <button
                                            class="accordion-button {{ $faq['open'] ? '' : 'collapsed' }} fw-bold bg-white"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#{{ $faq['id'] }}"
                                            aria-expanded="{{ $faq['open'] ? 'true' : 'false' }}"
                                            aria-controls="{{ $faq['id'] }}">
                                            <i class="bi bi-question-circle me-3 text-primary fs-5" aria-hidden="true"></i>
                                            {{ $faq['question'] }}
                                        </button>
                                    </h3>
                                    <div id="{{ $faq['id'] }}"
                                        class="accordion-collapse collapse {{ $faq['open'] ? 'show' : '' }}"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted px-5 bg-white border-top">
                                            {!! $faq['answer'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mt-5">
                            <p class="text-muted mb-3 small">Masih memiliki pertanyaan spesifik lainnya?</p>
                            <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture,%20saya%20punya%20pertanyaan%20terkait%20layanan%20Anda."
                                target="_blank" rel="noopener noreferrer"
                                class="btn btn-outline-success rounded-modern fw-medium shadow-sm px-4"
                                aria-label="Konsultasi langsung via WhatsApp">
                                <i class="bi bi-whatsapp me-2" aria-hidden="true"></i> Konsultasi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== CTA BANNER SECTION ===== --}}
        <section class="cta-banner position-relative text-white"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" aria-label="Call to action banner">
            <div class="wave-top" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false">
                    <path fill="#f8f9fa"
                        d="M0,96L48,112C96,128,192,160,288,170.7C384,181,480,171,576,149.3C672,128,768,96,864,96C960,96,1056,128,1152,149.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                </svg>
            </div>

            <div class="container text-center position-relative" style="z-index: 2;">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <i class="bi bi-stars fs-1 mb-3 d-block cta-stars text-warning" aria-hidden="true"></i>
                        <h2 class="h2 fw-bold mb-3">Siap Wujudkan Furniture Impian Anda?</h2>
                        <p class="lead mb-5" style="opacity: 0.95;">
                            Dapatkan konsultasi gratis dari tim desainer profesional kami dan realisasikan furniture eksklusif
                            sesuai karakter rumah Anda.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            @auth
                                <a href="{{ route('customer.orders.custom') }}"
                                    class="btn btn-light btn-lg px-4 px-sm-5 py-3 rounded-modern shadow-lg text-primary fw-bold">
                                    <i class="bi bi-rocket-takeoff me-2" aria-hidden="true"></i> Mulai Desain Sekarang
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="btn btn-light btn-lg px-4 px-sm-5 py-3 rounded-modern shadow-lg text-primary fw-bold">
                                    <i class="bi bi-person-plus-fill me-2" aria-hidden="true"></i> Daftar Akun Gratis
                                </a>
                                <a href="{{ route('login') }}"
                                    class="btn btn-outline-light btn-lg px-4 px-sm-5 py-3 rounded-modern fw-medium">
                                    <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i> Login
                                </a>
                            @endauth
                        </div>
                        <p class="mt-4 pt-3 mb-0 small" style="opacity: 0.9;">
                            <i class="bi bi-shield-check me-2" aria-hidden="true"></i> Jaminan 100% Kualitas Kayu Asli Jepara
                        </p>
                    </div>
                </div>
            </div>
        </section>

    @push('styles')
        <style>
            /* ============================================
                           WAVE SHAPES
                        ============================================ */
            .wave-top,
            .wave-bottom {
                position: absolute;
                left: 0;
                width: 100%;
                overflow: hidden;
                line-height: 0;
                z-index: 1;
            }

            .wave-top {
                top: 0;
                transform: rotate(180deg);
            }

            .wave-bottom {
                bottom: 0;
            }

            .wave-top svg,
            .wave-bottom svg {
                display: block;
                width: 100%;
                height: 150px;
            }

            /* ============================================
                           SECTION BASE & PADDING
                        ============================================ */
            section {
                position: relative;
                overflow: hidden;
            }

            .hero {
                padding-top: 9rem;
                padding-bottom: 3rem;
            }

            .quick-access {
                padding-top: 2rem;
                padding-bottom: 10rem;
            }

            .featured-products {
                padding-top: 10rem;
                padding-bottom: 4rem;
            }

            .custom-order-cta {
                padding-top: 10rem;
                padding-bottom: 10rem;
            }

            .process-timeline {
                padding-top: 10rem;
                padding-bottom: 10rem;
            }

            .testimonials {
                padding-top: 3rem;
                padding-bottom: 10rem;
            }

            .cta-banner {
                padding-top: 10rem;
                padding-bottom: 5rem;
            }

            .hero-buttons .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* ============================================
                           CARDS & HOVER EFFECTS
                        ============================================ */
            .quick-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .quick-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15) !important;
            }

            .hover-lift {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .hover-lift:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            }

            .card-img-top {
                transition: transform 0.4s ease;
            }

            .card:hover .card-img-top {
                transform: scale(1.08);
            }

            .feature-item {
                transition: transform 0.3s ease;
            }

            .feature-item:hover {
                transform: scale(1.03);
            }

            .feature-item:hover .rounded-circle {
                animation: pulse 0.6s ease-in-out;
            }

            .timeline-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.06);
                overflow: visible;
            }

            .timeline-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
                border-color: rgba(0, 0, 0, 0.1);
            }

            .timeline-badge {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1;
            }

            .timeline-icon-circle {
                width: 90px;
                height: 90px;
                transition: all 0.3s ease;
            }

            .timeline-card:hover .timeline-icon-circle {
                transform: scale(1.1) rotate(5deg);
            }

            /* ============================================
                           FAQ ACCORDION
                        ============================================ */
            .accordion-button:not(.collapsed) {
                background-color: #f8f9fa;
                color: #667eea;
                box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .05);
            }

            .accordion-button:focus {
                box-shadow: none;
            }

            .accordion-body {
                line-height: 1.7;
            }

            /* ============================================
                           ANIMATIONS
                        ============================================ */
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

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.1);
                }
            }

            @keyframes ctaPulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.15);
                }
            }

            @keyframes shimmer {
                0% {
                    background-position: -200% 0;
                }

                100% {
                    background-position: 200% 0;
                }
            }

            .fade-in {
                animation: fadeInUp 0.8s ease-out;
            }

            .slide-in {
                animation: slideInRight 0.8s ease-out 0.2s both;
            }

            .cta-stars {
                animation: ctaPulse 2.5s ease-in-out infinite;
            }

            img[loading="lazy"] {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: shimmer 1.5s infinite;
            }

            /* ============================================
                           RESPONSIVE
                        ============================================ */
            @media (max-width: 768px) {

                .wave-top svg,
                .wave-bottom svg {
                    height: 70px;
                }

                .hero {
                    padding-bottom: 7rem;
                }

                .quick-access {
                    padding-top: 1.5rem;
                    padding-bottom: 7rem;
                }

                .featured-products {
                    padding-top: 7rem;
                    padding-bottom: 3rem;
                }

                .custom-order-cta {
                    padding-top: 6rem;
                    padding-bottom: 6rem;
                }

                .process-timeline {
                    padding-top: 6rem;
                    padding-bottom: 6rem;
                }

                .testimonials {
                    padding-top: 2rem;
                    padding-bottom: 6rem;
                }

                .cta-banner {
                    padding-top: 6rem;
                }

                .display-4 {
                    font-size: 2.25rem;
                }
            }

            @media (max-width: 576px) {
                .hero {
                    padding-top: 6rem;
                    padding-bottom: 5.5rem;
                }

                .hero img {
                    height: 250px !important;
                }

                .hero h1 {
                    font-size: 1.85rem !important;
                }

                .quick-access {
                    padding-top: 1rem;
                    padding-bottom: 5rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                'use strict';

                document.addEventListener('DOMContentLoaded', function() {
                    // Scroll Animation Observer (Lazy load effect on scroll)
                    const observerOptions = {
                        threshold: 0.1,
                        rootMargin: '0px 0px -50px 0px'
                    };

                    const scrollObserver = new IntersectionObserver(function(entries, obs) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('fade-in');
                                obs.unobserve(entry.target);
                            }
                        });
                    }, observerOptions);

                    document.querySelectorAll('.feature-item, .featured-products .card, .timeline-card').forEach(
                        function(element) {
                            scrollObserver.observe(element);
                        }
                    );

                    // Image fallback handler
                    document.querySelectorAll('img[loading="lazy"]').forEach(function(img) {
                        img.addEventListener('error', function() {
                            if (!this.dataset.retried) {
                                this.dataset.retried = 'true';
                                const originalSrc = this.src;
                                const self = this;
                                setTimeout(function() {
                                    self.src = originalSrc;
                                }, 1000);
                            } else {
                                this.src =
                                    'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="400" height="300" fill="%23f8f9fa"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="%236c757d"%3EGambar tidak tersedia%3C/text%3E%3C/svg%3E';
                            }
                        });
                    });

                    @if (config('app.debug'))
                        console.log(
                            'Homepage successfully loaded. Categories: {{ $categories->count() }}, Products: {{ $products->count() }}'
                        );
                    @endif
                });
            })();
        </script>
    @endpush
@endsection
