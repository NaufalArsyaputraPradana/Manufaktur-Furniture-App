@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section position-relative text-white" aria-label="Keranjang belanja hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"
                            class="text-white text-decoration-none opacity-75 hover-opacity-100 transition-all">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Keranjang Belanja</li>
                </ol>
            </nav>

            <div class="text-center fade-in">
                <h1 class="display-4 fw-bold mb-3 text-white">
                    <i class="bi bi-cart3 me-2" aria-hidden="true"></i>Keranjang Belanja
                </h1>
                <p class="lead mb-0 opacity-90">
                    @if (!empty($cart))
                        Anda memiliki <span class="fw-bold">{{ count($cart) }} item</span> di keranjang
                    @else
                        Keranjang belanja Anda masih kosong
                    @endif
                </p>
            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" focusable="false">
                <path
                    d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                    class="shape-fill" fill="#f8f9fa"></path>
            </svg>
        </div>
    </section>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="cart-section py-5 bg-light" aria-label="Isi keranjang">
        <div class="container">

            @if (!empty($cart))
                <div class="row g-4">

                    {{-- ===== CART ITEMS ===== --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-on-scroll bg-white">
                            <div class="card-header bg-gradient-primary text-white border-0 p-4 rounded-top-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h2 class="h5 mb-0 fw-bold text-white d-flex align-items-center">
                                        <i class="bi bi-basket-fill me-2" aria-hidden="true"></i>Item Keranjang
                                        <span class="badge bg-white text-primary ms-2 shadow-sm">{{ count($cart) }}</span>
                                    </h2>
                                    <button type="button" class="btn btn-sm btn-light hover-lift fw-medium"
                                        data-bs-toggle="modal" data-bs-target="#clearCartModal"
                                        aria-label="Kosongkan keranjang">
                                        <i class="bi bi-trash me-1" aria-hidden="true"></i>Kosongkan Keranjang
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="list-group list-group-flush rounded-bottom-4">
                                    {{-- Menggunakan $itemKey agar update/hapus sinkron dengan identifier unik custom di controller --}}
                                    @foreach ($cart as $itemKey => $item)
                                        <div class="list-group-item p-4 cart-item border-bottom">
                                            <div class="row align-items-start g-4">

                                                {{-- Product Image & Info --}}
                                                <div class="col-lg-5">
                                                    <div class="d-flex gap-3 align-items-start">
                                                        <div class="flex-shrink-0">
                                                            @php
                                                                $imagePath = null;
                                                                if (!empty($item['image'])) {
                                                                    // Jika path sudah lengkap dengan asset URL
                                                                    if (str_starts_with($item['image'], 'http')) {
                                                                        $imagePath = $item['image'];
                                                                    } else {
                                                                        // Jika path relatif, tambahkan storage
                                                                        $imagePath = asset('storage/' . ltrim($item['image'], '/'));
                                                                    }
                                                                }
                                                            @endphp
                                                            @if ($imagePath)
                                                                <img src="{{ $imagePath }}"
                                                                    alt="{{ $item['name'] }}"
                                                                    class="cart-product-image rounded-3 shadow-sm"
                                                                    loading="lazy" style="cursor:zoom-in;"
                                                                    onclick="openImageModal('{{ $imagePath }}', '{{ addslashes($item['name']) }}')"
                                                                    onerror="this.classList.add('img-error')">
                                                            @else
                                                                <div
                                                                    class="cart-product-image rounded-3 shadow-sm bg-gradient-primary d-flex align-items-center justify-content-center text-white">
                                                                    <i class="bi bi-image display-6 opacity-50"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            @endif
                                                            @if ($imagePath)
                                                                <div class="text-center mt-1">
                                                                    <small class="text-muted zoom-hint"
                                                                        style="font-size:0.7rem;">
                                                                        <i class="bi bi-zoom-in" aria-hidden="true"></i>
                                                                        Klik perbesar
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="flex-grow-1 min-w-0">
                                                            <h3 class="h6 fw-bold mb-2 text-dark">{{ $item['name'] }}</h3>

                                                            @if (!empty($item['custom_dimensions']))
                                                                <span
                                                                    class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info mb-2">
                                                                    <i class="bi bi-rulers me-1"
                                                                        aria-hidden="true"></i>Ukuran Custom
                                                                </span>
                                                                <div
                                                                    class="small text-muted mt-2 bg-light p-2 rounded-2 border border-light">
                                                                    @if (is_array($item['custom_dimensions']))
                                                                        @foreach ($item['custom_dimensions'] as $key => $value)
                                                                            <div class="mb-1">
                                                                                <i class="bi bi-check2 text-success me-1"
                                                                                    aria-hidden="true"></i>
                                                                                {{ ucfirst($key) }}: <strong
                                                                                    class="text-dark">{{ $value }}</strong>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div>{{ $item['custom_dimensions'] }}</div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span
                                                                    class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary mb-2">
                                                                    Ukuran Standar
                                                                </span>
                                                            @endif

                                                            {{-- Price (Mobile view only) --}}
                                                            <div class="d-lg-none mt-3 pt-2 border-top">
                                                                <div class="text-muted small">Harga per unit</div>
                                                                <div class="fw-bold text-primary">
                                                                    <span class="price-convert"
                                                                        data-price="{{ $item['price'] }}"
                                                                        data-currency="IDR">
                                                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Price (Desktop view only) --}}
                                                <div class="col-lg-2 d-none d-lg-block text-center">
                                                    <label
                                                        class="text-muted small fw-bold text-uppercase mb-2 d-block">Harga</label>
                                                    <div class="fw-bold text-primary">
                                                        <span class="price-convert" data-price="{{ $item['price'] }}"
                                                            data-currency="IDR">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">per unit</small>
                                                </div>

                                                {{-- Quantity Controls --}}
                                                <div class="col-6 col-lg-2">
                                                    <label
                                                        class="text-muted small fw-bold text-uppercase mb-2 d-block text-center">Jumlah</label>
                                                    <div
                                                        class="input-group input-group-sm justify-content-center shadow-sm rounded-3 overflow-hidden">
                                                        <button class="btn btn-light border-end-0 quantity-btn text-primary"
                                                            type="button" data-action="decrease"
                                                            data-item-key="{{ $itemKey }}"
                                                            aria-label="Kurangi jumlah">
                                                            <i class="bi bi-dash-lg" aria-hidden="true"></i>
                                                        </button>
                                                        <input type="number"
                                                            class="form-control text-center fw-bold quantity-input border-start-0 border-end-0 bg-white"
                                                            value="{{ $item['quantity'] }}" min="1"
                                                            max="100" data-item-key="{{ $itemKey }}"
                                                            data-price="{{ $item['price'] }}" readonly
                                                            aria-label="Jumlah item" style="max-width:50px;">
                                                        <button
                                                            class="btn btn-light border-start-0 quantity-btn text-primary"
                                                            type="button" data-action="increase"
                                                            data-item-key="{{ $itemKey }}"
                                                            aria-label="Tambah jumlah">
                                                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Subtotal & Remove --}}
                                                <div class="col-6 col-lg-3 text-center text-lg-end">
                                                    <label
                                                        class="text-muted small fw-bold text-uppercase mb-2 d-block">Subtotal</label>
                                                    <div class="fw-bold fs-5 text-dark item-subtotal mb-2"
                                                        data-item-key="{{ $itemKey }}">
                                                        <span class="price-convert"
                                                            data-price="{{ $item['price'] * $item['quantity'] }}"
                                                            data-currency="IDR">
                                                            Rp
                                                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    <form
                                                        action="{{ route('customer.cart.remove', ['itemKey' => $itemKey]) }}"
                                                        method="POST" class="d-inline remove-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger hover-lift btn-delete fw-medium"
                                                            data-name="{{ $item['name'] }}"
                                                            aria-label="Hapus {{ $item['name'] }} dari keranjang">
                                                            <i class="bi bi-trash me-1" aria-hidden="true"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('products.index') }}"
                            class="btn btn-outline-primary btn-lg hover-lift px-4 fw-bold shadow-sm">
                            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Lanjut Belanja
                        </a>
                    </div>

                    {{-- ===== CART SUMMARY ===== --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 sticky-summary animate-on-scroll bg-white">
                            <div class="card-header bg-gradient-primary text-white border-0 p-4 rounded-top-4">
                                <h2 class="h5 mb-0 fw-bold text-white d-flex align-items-center">
                                    <i class="bi bi-receipt me-2" aria-hidden="true"></i>Ringkasan Belanja
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <span class="text-muted fw-medium">Total Item:</span>
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fs-6 border border-primary border-opacity-25"
                                        id="total-items">
                                        {{ count($cart) }}
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="fw-bold fs-6 text-dark d-block mb-1">Total Belanja:</span>
                                    <div class="fw-bold fs-3 text-primary" id="cart-total">
                                        <span class="price-convert" data-price="{{ $total }}"
                                            data-currency="IDR">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="alert alert-info bg-opacity-10 border-info mb-4 rounded-3 p-3" role="alert">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle-fill text-info mt-1" aria-hidden="true"></i>
                                        <small class="text-dark">Belum termasuk ongkos kirim dan biaya tambahan operasional
                                            lainnya.</small>
                                    </div>
                                </div>

                                <div class="d-grid mb-4">
                                    <a href="{{ route('customer.checkout.index') }}"
                                        class="btn btn-primary btn-lg hover-lift fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                        <i class="bi bi-credit-card-fill me-2" aria-hidden="true"></i> Lanjut ke Checkout
                                    </a>
                                </div>

                                <div class="p-3 bg-light rounded-3 border border-light">
                                    <h3 class="h6 fw-bold mb-3 d-flex align-items-center">
                                        <i class="bi bi-shield-check text-primary me-2" aria-hidden="true"></i>Keuntungan
                                        Belanja
                                    </h3>
                                    <ul class="list-unstyled small mb-0 text-muted">
                                        @foreach (['Kualitas material terjamin', 'Ukuran custom presisi', 'Garansi produksi 1 tahun', 'Pengiriman aman terpercaya'] as $benefit)
                                            <li class="mb-2 d-flex align-items-start {{ $loop->last ? '' : 'mb-2' }}">
                                                <i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0 mt-1"
                                                    aria-hidden="true"></i>
                                                <span>{{ $benefit }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ===== CLEAR CART MODAL ===== --}}
                <div class="modal fade" id="clearCartModal" tabindex="-1" aria-labelledby="clearCartModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-danger text-white border-0 p-4">
                                <h2 class="modal-title h5 fw-bold" id="clearCartModalLabel">
                                    <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>Kosongkan
                                    Keranjang
                                </h2>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-4 bg-white">
                                <div class="alert alert-warning bg-opacity-10 border-warning mb-4 rounded-3 p-3"
                                    role="alert">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-exclamation-circle-fill text-warning mt-1" aria-hidden="true"></i>
                                        <div class="text-dark small"><strong>Perhatian!</strong> Semua item akan dihapus
                                            secara permanen dari keranjang belanja Anda.</div>
                                    </div>
                                </div>
                                <p class="mb-3 text-center text-dark">Apakah Anda yakin ingin melanjutkan?</p>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="row text-center g-3">
                                        <div class="col-6 border-end">
                                            <div class="fw-bold text-dark fs-4">{{ count($cart) }}</div>
                                            <small class="text-muted fw-medium text-uppercase">Total Item</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold text-primary fs-5">
                                                <span class="price-convert" data-price="{{ $total }}"
                                                    data-currency="IDR">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                            </div>
                                            <small class="text-muted fw-medium text-uppercase">Total Harga</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0 bg-white">
                                <button type="button" class="btn btn-light border hover-lift fw-medium px-4"
                                    data-bs-dismiss="modal">
                                    Batal
                                </button>
                                <form action="{{ route('customer.cart.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger hover-lift fw-bold px-4 shadow-sm">
                                        <i class="bi bi-trash-fill me-2" aria-hidden="true"></i>Ya, Kosongkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- ===== EMPTY CART ===== --}}
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 text-center py-5 animate-on-scroll bg-white">
                            <div class="card-body p-5">
                                <div class="mb-4">
                                    <i class="bi bi-cart-x display-1 text-muted empty-cart-icon opacity-50"
                                        aria-hidden="true"></i>
                                </div>
                                <h2 class="h3 fw-bold mb-3 text-dark">Keranjang Belanja Kosong</h2>
                                <p class="text-muted mb-4 lead fs-6 mx-auto" style="max-width: 450px;">
                                    Belum ada produk di keranjang Anda. Mulai jelajahi katalog kami dan temukan furniture
                                    eksklusif untuk rumah impian Anda!
                                </p>
                                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-primary btn-lg hover-lift px-5 fw-bold shadow-sm">
                                        <i class="bi bi-shop me-2" aria-hidden="true"></i>Mulai Belanja
                                    </a>
                                    <a href="{{ route('home') }}"
                                        class="btn btn-outline-secondary btn-lg hover-lift px-4 fw-medium">
                                        <i class="bi bi-house me-2" aria-hidden="true"></i>Kembali ke Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== IMAGE MODAL ===== --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
                <div class="modal-header border-0 bg-dark text-white p-3">
                    <h5 class="modal-title h6 fw-bold" id="imageModalLabel">
                        <i class="bi bi-image me-2" aria-hidden="true"></i>
                        <span id="modalProductName">Gambar Produk</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 bg-light position-relative d-flex justify-content-center align-items-center"
                    style="min-height: 40vh;">
                    <div class="position-absolute image-loading" style="display:none; z-index:1;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                    <img src="" id="modalImage" class="img-fluid" alt="Full size product image"
                        style="max-height:75vh; width:100%; object-fit:contain;">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* ============================================
                   HERO & GLOBAL
                ============================================ */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 9rem 0 7rem 0;
            /* Jarak aman navbar fixed */
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .wave-bottom {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 1;
        }

        .wave-bottom svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-opacity-100 {
            opacity: 0.75;
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        /* ============================================
                   CART ITEMS
                ============================================ */
        .cart-section {
            min-height: 60vh;
        }

        .cart-item {
            transition: background-color 0.3s ease, border-left-color 0.3s ease;
            border-left: 3px solid transparent;
        }

        .cart-item:hover {
            background-color: #f8f9fa;
            border-left-color: #667eea;
        }

        .cart-product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #f1f5f9;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .cart-product-image:hover {
            transform: scale(1.05);
            border-color: #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2) !important;
        }

        .cart-product-image.img-error {
            display: none;
        }

        .zoom-hint {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .flex-shrink-0:hover .zoom-hint {
            opacity: 1;
        }

        /* ============================================
                   QUANTITY CONTROLS
                ============================================ */
        .quantity-btn {
            width: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border-color: #dee2e6;
            background: #f8f9fa;
        }

        .quantity-btn:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white !important;
        }

        .quantity-btn:active {
            transform: scale(0.95);
        }

        .quantity-input {
            font-size: 1rem;
            pointer-events: none;
        }

        .quantity-input::-webkit-inner-spin-button,
        .quantity-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* ============================================
                   UTILITIES & BUTTONS
                ============================================ */
        .item-subtotal {
            transition: all 0.3s ease;
        }

        .item-subtotal.updated {
            color: #10b981 !important;
            transform: scale(1.05);
        }

        .sticky-summary {
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .hover-lift:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63408a 100%);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .empty-cart-icon {
            animation: floatIcon 3s ease-in-out infinite;
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
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

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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
            .sticky-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .cart-product-image {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .cart-product-image {
                width: 80px;
                height: 80px;
            }

            .quantity-btn {
                width: 30px;
            }

            .zoom-hint {
                display: none !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            document.addEventListener('DOMContentLoaded', function() {
                initScrollAnimations();
                initQuantityControls();
                initDeleteButtons();
                initImageModal();
                showSessionMessages();
            });

            // ============================================
            // IMAGE MODAL
            // ============================================
            function initImageModal() {
                const imageModal = document.getElementById('imageModal');
                if (imageModal) {
                    imageModal.addEventListener('hidden.bs.modal', cleanupModal);
                }
            }

            window.openImageModal = function(imageSrc, productName) {
                const modalEl = document.getElementById('imageModal');
                const modalImage = document.getElementById('modalImage');
                const modalName = document.getElementById('modalProductName');
                const imageLoading = modalEl.querySelector('.image-loading');

                if (imageLoading) imageLoading.style.display = 'block';
                modalImage.classList.add('opacity-50');
                modalImage.src = imageSrc;
                if (modalName) modalName.textContent = productName;

                modalImage.onload = function() {
                    if (imageLoading) imageLoading.style.display = 'none';
                    modalImage.classList.remove('opacity-50');
                };

                modalImage.onerror = function() {
                    if (imageLoading) imageLoading.style.display = 'none';
                    modalImage.classList.remove('opacity-50');
                    modalImage.src =
                        'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23f8f9fa%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%236c757d%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 style=%22font-size:24px; font-family:sans-serif%22%3EGambar tidak tersedia%3C/text%3E%3C/svg%3E';
                };

                new bootstrap.Modal(modalEl).show();
            };

            function cleanupModal() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            }

            // ============================================
            // SCROLL ANIMATIONS
            // ============================================
            function initScrollAnimations() {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
            }

            // ============================================
            // SESSION MESSAGES (SWEETALERT)
            // ============================================
            function showSessionMessages() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#667eea',
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#dc3545'
                    });
                @endif

                @if (session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: '{{ session('warning') }}',
                        confirmButtonColor: '#f59e0b'
                    });
                @endif
            }

            // ============================================
            // QUANTITY CONTROLS & AJAX
            // ============================================
            function initQuantityControls() {
                document.querySelectorAll('.quantity-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const itemKey = this.dataset.itemKey;
                        const input = document.querySelector(
                            `.quantity-input[data-item-key="${itemKey}"]`);
                        if (!input) return;

                        const qty = parseInt(input.value) || 1;

                        if (this.dataset.action === 'increase') {
                            handleIncrease(itemKey, qty, input);
                        } else {
                            handleDecrease(itemKey, qty, input, this);
                        }
                    });
                });
            }

            function handleIncrease(itemKey, qty, input) {
                if (qty >= 100) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Maksimal',
                        text: 'Maksimal pembelian adalah 100 unit per item',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                input.value = qty + 1;
                updateCartDisplay(itemKey, qty + 1);
                updateCartOnServer(itemKey, qty + 1);
            }

            function handleDecrease(itemKey, qty, input, buttonEl) {
                if (qty === 1) {
                    // Memicu form hapus jika qty mencapai 1 lalu di-minus
                    const removeBtn = buttonEl.closest('.cart-item').querySelector('.btn-delete');
                    if (removeBtn) removeBtn.click();
                } else {
                    input.value = qty - 1;
                    updateCartDisplay(itemKey, qty - 1);
                    updateCartOnServer(itemKey, qty - 1);
                }
            }

            // ============================================
            // DELETE CONFIRMATION
            // ============================================
            function initDeleteButtons() {
                document.querySelectorAll('.btn-delete').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        Swal.fire({
                            title: 'Hapus Item?',
                            html: `Apakah Anda yakin ingin menghapus <strong>${this.dataset.name}</strong> dari keranjang?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'btn btn-danger me-2',
                                cancelButton: 'btn btn-light border'
                            },
                            buttonsStyling: false
                        }).then(function(result) {
                            if (result.isConfirmed && form) {
                                form.submit();
                            }
                        });
                    });
                });
            }

            // ============================================
            // AJAX SERVER UPDATE
            // ============================================
            function updateCartOnServer(itemKey, quantity) {
                const baseUrl = '{{ route('customer.cart.index') }}';
                const url = baseUrl.replace(/\/$/, '') + '/' + encodeURIComponent(itemKey);
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) return;

                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            quantity
                        })
                    })
                    .then(res => {
                        console.log('Response Status:', res.status);
                        if (!res.ok) {
                            return res.json().then(data => {
                                throw new Error(data.message || `HTTP ${res.status}`);
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Update Success:', data);
                    })
                    .catch(function(error) {
                        console.error('Update Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Update',
                            text: 'Gagal mengupdate keranjang. Halaman akan dimuat ulang.',
                            confirmButtonColor: '#dc3545',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 2000);
                    });
            }

            // ============================================
            // UPDATE DISPLAY CLIENT SIDE
            // ============================================
            function updateCartDisplay(itemKey, quantity) {
                const input = document.querySelector(`.quantity-input[data-item-key="${itemKey}"]`);
                if (!input) return;

                const price = parseFloat(input.dataset.price);
                const subtotal = document.querySelector(`.item-subtotal[data-item-key="${itemKey}"]`);

                // Update individual subtotal
                if (subtotal) {
                    subtotal.innerHTML =
                        `<span class="price-convert" data-price="${price * quantity}" data-currency="IDR">${formatCurrency(price * quantity)}</span>`;
                    subtotal.classList.add('updated');
                    setTimeout(() => subtotal.classList.remove('updated'), 500);
                }

                // Update grand total
                let total = 0;
                document.querySelectorAll('.quantity-input').forEach(function(inp) {
                    total += parseFloat(inp.dataset.price) * parseInt(inp.value);
                });

                const totalEl = document.getElementById('cart-total');
                if (totalEl) {
                    totalEl.innerHTML =
                        `<span class="price-convert" data-price="${total}" data-currency="IDR">${formatCurrency(total)}</span>`;
                    totalEl.style.transform = 'scale(1.05)';
                    setTimeout(() => totalEl.style.transform = 'scale(1)', 300);
                }
            }

            function formatCurrency(amount) {
                return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
            }

        })();
    </script>
@endpush
