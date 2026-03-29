@extends('layouts.app')

@section('title', 'Katalog Produk - UD Bisa Furniture')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-products position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
        aria-label="Products hero section">

        {{-- Background Pattern --}}
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center text-white">
                    <nav aria-label="Breadcrumb" class="mb-4">
                        <ol class="breadcrumb justify-content-center mb-0 bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}" class="text-white text-decoration-none hover-opacity">
                                    <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Katalog Produk
                            </li>
                        </ol>
                    </nav>

                    <span
                        class="badge bg-white bg-opacity-25 text-white px-4 py-2 mb-3 d-inline-block rounded-pill shadow-sm">
                        <i class="bi bi-shop-window me-2" aria-hidden="true"></i>Furniture Berkualitas Premium
                    </span>

                    <h1 class="display-4 fw-bold mb-3 fade-in">Katalog Produk Kami</h1>

                    <p class="lead mb-0 fade-in-up" style="opacity: 0.95;">
                        Jelajahi koleksi furniture berkualitas tinggi dengan desain modern dan elegan untuk kebutuhan rumah
                        dan kantor Anda.
                    </p>
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

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="products-section bg-white" aria-label="Katalog produk">
        <div class="container">
            <div class="row">

                {{-- ===== SIDEBAR FILTER ===== --}}
                <aside class="col-lg-3 mb-4" role="complementary" aria-label="Filter produk">
                    <div class="card border-0 shadow-sm rounded-4 sticky-sidebar bg-light border border-light">
                        <div class="card-header border-0 py-3 rounded-top-4"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h2 class="h5 mb-0 fw-bold text-white d-flex align-items-center">
                                <i class="bi bi-funnel-fill me-2" aria-hidden="true"></i> Filter Produk
                            </h2>
                        </div>

                        <div class="card-body p-4 bg-white rounded-bottom-4">
                            <h3 class="h6 fw-bold mb-3 text-dark d-flex align-items-center">
                                <i class="bi bi-grid-3x3-gap-fill me-2 text-primary" aria-hidden="true"></i>
                                Kategori Produk
                            </h3>

                            <div class="list-group list-group-flush">
                                {{-- All Products --}}
                                <a href="{{ route('products.index') }}"
                                    class="list-group-item list-group-item-action border-0 rounded-3 mb-2 px-3 py-2 {{ !request('category_id') ? 'active shadow-sm' : 'bg-light' }}"
                                    aria-current="{{ !request('category_id') ? 'page' : 'false' }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-medium d-flex align-items-center">
                                            <i class="bi bi-grid-fill me-2" aria-hidden="true"></i> Semua Produk
                                        </span>
                                        <span
                                            class="badge {{ !request('category_id') ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill">
                                            {{ $categories->sum('products_count') }}
                                        </span>
                                    </div>
                                </a>

                                {{-- Per Category --}}
                                @foreach ($categories as $category)
                                    <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
                                        class="list-group-item list-group-item-action border-0 rounded-3 mb-2 px-3 py-2 {{ request('category_id') == $category->id ? 'active shadow-sm' : 'bg-light' }}"
                                        aria-current="{{ request('category_id') == $category->id ? 'page' : 'false' }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-medium d-flex align-items-center">
                                                @switch($category->name)
                                                    @case('Meja')
                                                        <i class="bi bi-table me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Kursi')
                                                        <i class="bi bi-easel me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Lemari')
                                                        <i class="bi bi-door-closed me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Tempat Tidur')
                                                        <i class="bi bi-moon-stars me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Rak')
                                                        <i class="bi bi-bookshelf me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Sofa')
                                                        <i class="bi bi-basket2 me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Nakas')
                                                        <i class="bi bi-box2 me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Bufet')
                                                        <i class="bi bi-tv me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Kitchen Set')
                                                        <i class="bi bi-cup-hot me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Partisi')
                                                        <i class="bi bi-columns-gap me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Meja Konsol')
                                                        <i class="bi bi-reception-4 me-2" aria-hidden="true"></i>
                                                    @break

                                                    @case('Credenza')
                                                        <i class="bi bi-cabinet-file me-2" aria-hidden="true"></i>
                                                    @break

                                                    @default
                                                        <i class="bi bi-box-seam me-2" aria-hidden="true"></i>
                                                @endswitch
                                                {{ $category->name }}
                                            </span>
                                            @if ($category->products_count > 0)
                                                <span
                                                    class="badge {{ request('category_id') == $category->id ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill">
                                                    {{ $category->products_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            @if (request()->hasAny(['category_id', 'search', 'sort']))
                                <div class="d-grid mt-4 pt-3 border-top">
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-outline-danger rounded-3 hover-lift fw-medium"
                                        aria-label="Reset semua filter">
                                        <i class="bi bi-x-circle me-2" aria-hidden="true"></i> Reset Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </aside>

                {{-- ===== PRODUCT MAIN AREA ===== --}}
                <main class="col-lg-9" role="main">

                    {{-- Stats & Search Header --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                        <div class="card-body p-4 bg-white rounded-4 border border-light">
                            <div class="row align-items-center mb-4">
                                <div class="col-md-7">
                                    <h2 class="h3 fw-bold mb-2 d-flex align-items-center text-dark">
                                        @if (request('category_id'))
                                            <i class="bi bi-tag-fill text-primary me-2" aria-hidden="true"></i>
                                            {{ $categories->firstWhere('id', request('category_id'))?->name ?? 'Produk' }}
                                        @else
                                            <i class="bi bi-grid-fill text-primary me-2" aria-hidden="true"></i>
                                            Semua Produk
                                        @endif
                                    </h2>
                                    <p class="text-muted mb-0 small">
                                        Menampilkan <strong class="text-primary">{{ $products->count() }}</strong>
                                        dari <strong>{{ $products->total() }}</strong> produk
                                    </p>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    @if (request()->hasAny(['category_id', 'search']))
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-funnel-fill me-1" aria-hidden="true"></i> Filter Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i> Semua Kategori
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Search & Sort Form --}}
                            <form action="{{ route('products.index') }}" method="GET"
                                class="row g-3 bg-light p-3 rounded-4" role="search"
                                aria-label="Pencarian dan filter produk">
                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">

                                <div class="col-lg-6">
                                    <label for="searchInput"
                                        class="form-label small fw-bold text-muted text-uppercase mb-2">Cari Produk</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-search" aria-hidden="true"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="searchInput"
                                            name="search" placeholder="Masukkan nama atau kata kunci..."
                                            value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-8">
                                    <label for="sortSelect"
                                        class="form-label small fw-bold text-muted text-uppercase mb-2">Urutkan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-sort-down" aria-hidden="true"></i></span>
                                        <select class="form-select border-start-0" id="sortSelect" name="sort"
                                            onchange="this.form.submit()" aria-label="Urutkan produk">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                                Terbaru</option>
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama
                                                A-Z</option>
                                            <option value="price_low"
                                                {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah
                                            </option>
                                            <option value="price_high"
                                                {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                    <button type="submit"
                                        class="btn btn-primary w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center"
                                        style="height: 38px;">
                                        Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Products Grid --}}
                    @if ($products->isNotEmpty())
                        <div class="row g-4 mb-5" role="list" aria-label="Daftar produk">
                            @foreach ($products as $product)
                                <div class="col-md-6 col-lg-4" role="listitem">
                                    <x-product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($products->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Navigasi halaman produk">
                                    {{ $products->links('pagination::bootstrap-5') }}
                                </nav>
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body text-center py-5 px-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 mb-4"
                                    style="width: 120px; height: 120px;">
                                    <i class="bi bi-inbox display-1 text-primary" aria-hidden="true"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Produk Tidak Ditemukan</h3>
                                <p class="text-muted mb-4 mx-auto lh-base" style="max-width: 500px;">
                                    @if (request('search'))
                                        Maaf, tidak ada produk yang sesuai dengan pencarian "<strong
                                            class="text-dark">{{ request('search') }}</strong>".
                                        Coba gunakan kata kunci yang berbeda.
                                    @else
                                        Belum ada produk di kategori ini. Silakan pilih kategori lain atau lihat semua
                                        koleksi kami.
                                    @endif
                                </p>
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-primary px-4 rounded-3 shadow-sm fw-bold">
                                        <i class="bi bi-grid-fill me-2" aria-hidden="true"></i> Lihat Semua Produk
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </main>

            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* ============================================
                   HERO
                ============================================ */
        .hero-products {
            padding-top: 9rem;
            padding-bottom: 8rem;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .hero-products .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: rgba(255, 255, 255, 0.7);
        }

        .hover-opacity {
            transition: opacity 0.3s ease;
        }

        .hover-opacity:hover {
            opacity: 0.8;
        }

        /* Wave */
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
            height: 100px;
        }

        /* ============================================
                   PRODUCTS SECTION
                ============================================ */
        .products-section {
            padding-top: 2rem;
            padding-bottom: 5rem;
        }

        /* ============================================
                   SIDEBAR
                ============================================ */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .rounded-top-4 {
            border-radius: 1rem 1rem 0 0 !important;
        }

        .list-group-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .list-group-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: #fff;
            font-weight: 600;
        }

        .list-group-item:hover:not(.active) {
            background-color: #f8f9fa !important;
            transform: translateX(4px);
        }

        /* ============================================
                   PRODUCT CARDS
                ============================================ */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }

        .product-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15) !important;
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

        .price-box {
            transition: background-color 0.3s ease;
        }

        /* ============================================
                   BUTTONS & FORMS
                ============================================ */
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63408a 100%);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #667eea;
        }

        /* ============================================
                   PAGINATION
                ============================================ */
        .pagination {
            gap: 0.25rem;
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            color: #667eea;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
        }

        /* ============================================
                   ANIMATIONS
                ============================================ */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out 0.2s both;
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

        /* ============================================
                   RESPONSIVE
                ============================================ */
        @media (max-width: 991px) {
            .sticky-sidebar {
                position: relative !important;
                top: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .hero-products {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 50px;
            }

            .product-card:hover {
                transform: translateY(-4px);
            }

            .list-group-item:hover:not(.active) {
                transform: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // ADD TO CART - SWEETALERT
            document.querySelectorAll('.add-to-cart-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const productName = this.querySelector('input[name="product_name"]').value;

                    Swal.fire({
                        title: 'Memproses...',
                        html: `<div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0">Menambahkan <strong>${productName}</strong> ke keranjang...</p>
                               </div>`,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });

                    this.submit();
                });
            });

            // SCROLL ANIMATION - PRODUCT CARDS
            const scrollObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry, index) {
                    if (entry.isIntersecting) {
                        setTimeout(function() {
                            entry.target.classList.add('visible');
                            entry.target.style.transition =
                                `opacity 0.5s ease ${index * 0.05}s, transform 0.5s ease ${index * 0.05}s`;
                        }, index * 50);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.product-card').forEach(function(card) {
                scrollObserver.observe(card);
            });

            // IMAGE ERROR HANDLING
            document.querySelectorAll('img[loading="lazy"]').forEach(function(img) {
                img.addEventListener('error', function() {
                    const fallbackHtml = `
                        <div class="d-flex align-items-center justify-content-center text-white" style="height: 240px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%;">
                            <div class="text-center">
                                <i class="bi bi-image display-4 mb-2 opacity-50" aria-hidden="true"></i>
                                <p class="small mb-0 opacity-75">Gambar tidak tersedia</p>
                            </div>
                        </div>`;

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = fallbackHtml.trim();
                    this.replaceWith(wrapper.firstChild);
                });
            });

            // SCROLL TO ACTIVE FILTER
            const activeFilter = document.querySelector('.list-group-item.active');
            if (activeFilter && window.innerWidth < 992) {
                // Hanya scroll jika di mobile/tablet agar pengguna tidak bingung
                activeFilter.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    </script>
@endpush
