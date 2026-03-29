@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section position-relative text-white" aria-label="Checkout hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.cart.index') }}"
                            class="text-white text-decoration-none opacity-75 hover-opacity-100">
                            <i class="bi bi-cart3 me-1" aria-hidden="true"></i>Keranjang
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Checkout</li>
                </ol>
            </nav>

            <div class="text-center">
                <h1 class="display-4 fw-bold mb-3 text-white">
                    <i class="bi bi-credit-card-fill me-2" aria-hidden="true"></i>Checkout
                </h1>
                <p class="lead mb-0 opacity-90">Selesaikan pesanan Anda dengan mudah dan aman</p>
            </div>
        </div>

        <div class="wave-bottom" aria-hidden="true">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path
                    d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                    class="shape-fill"></path>
            </svg>
        </div>
    </section>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="checkout-section py-5" aria-label="Form checkout">
        <div class="container">
            <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row g-4">

                    {{-- ===== FORM COLUMN ===== --}}
                    <div class="col-lg-8">

                        {{-- Customer Information --}}
                        <div class="card shadow-sm rounded-4 border-0 mb-4 animate-on-scroll">
                            <div class="card-header bg-gradient-primary text-white border-0 p-4">
                                <h2 class="h5 mb-0 fw-bold text-white">
                                    <i class="bi bi-person-badge-fill me-2" aria-hidden="true"></i>Informasi Pelanggan
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-person me-1 text-primary" aria-hidden="true"></i>Nama Lengkap
                                        </label>
                                        <input type="text" class="form-control form-control-lg"
                                            value="{{ auth()->user()->name }}" readonly aria-label="Nama lengkap">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-envelope me-1 text-primary" aria-hidden="true"></i>Email
                                        </label>
                                        <input type="email" class="form-control form-control-lg"
                                            value="{{ auth()->user()->email }}" readonly aria-label="Alamat email">
                                    </div>
                                    <div class="col-md-12">
                                        <x-form-input 
                                            name="phone" 
                                            type="tel"
                                            label="Nomor Telepon"
                                            placeholder="Contoh: 082123456789"
                                            :value="old('phone', auth()->user()->phone ?? '')"
                                        />
                                    </div>
                                </div>
                                <div class="alert alert-info bg-opacity-10 border-info mt-3 mb-0" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill me-2 mt-1 shrink-0" aria-hidden="true"></i>
                                        <small>
                                            <strong>Info:</strong> Nama dan Email diambil dari profil Anda. Anda bisa mengubah Nomor Telepon di bawah jika berbeda.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Shipping Information --}}
                        <div class="card shadow-sm rounded-4 border-0 mb-4 animate-on-scroll">
                            <div class="card-header bg-success text-white border-0 p-4">
                                <h2 class="h5 mb-0 fw-bold text-white">
                                    <i class="bi bi-truck-fill me-2" aria-hidden="true"></i>Alamat Pengiriman
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <label for="shipping_address" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt-fill me-1 text-success" aria-hidden="true"></i>
                                    Alamat Lengkap <span class="text-danger" aria-hidden="true">*</span>
                                </label>
                                <textarea class="form-control form-control-lg @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                    name="shipping_address" rows="4" required
                                    placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 05, Kelurahan Sentosa, Kecamatan Makmur, Kota Jakarta, 12345">{{ old('shipping_address', auth()->user()->address ?? '') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-lightbulb-fill me-1 flex-shrink-0" aria-hidden="true"></i>
                                    Pastikan alamat lengkap dan jelas (Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos)
                                </small>
                            </div>
                        </div>

                        {{-- Order Notes --}}
                        <div class="card shadow-sm rounded-4 border-0 mb-4 animate-on-scroll">
                            <div class="card-header bg-warning text-dark border-0 p-4">
                                <h2 class="h5 mb-0 fw-bold">
                                    <i class="bi bi-chat-left-text-fill me-2" aria-hidden="true"></i>Catatan Pesanan
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <label for="customer_notes" class="form-label fw-semibold">
                                    <i class="bi bi-pencil-fill me-1 text-warning" aria-hidden="true"></i>Catatan (Opsional)
                                </label>
                                <textarea class="form-control form-control-lg @error('customer_notes') is-invalid @enderror" id="customer_notes"
                                    name="customer_notes" rows="3"
                                    placeholder="Contoh: Saya ingin warna coklat tua untuk meja, mohon hubungi saya sebelum dikirim, dll.">{{ old('customer_notes') }}</textarea>
                                @error('customer_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-info-circle-fill me-1 flex-shrink-0" aria-hidden="true"></i>
                                    Catatan ini akan dilihat oleh tim kami untuk memproses pesanan Anda dengan lebih baik
                                </small>
                            </div>
                        </div>



                    </div>

                    {{-- ===== SUMMARY SIDEBAR ===== --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm rounded-4 border-0 sticky-summary animate-on-scroll">
                            <div class="card-header bg-gradient-primary text-white border-0 p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h2 class="h5 mb-0 fw-bold text-white">
                                        <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Ringkasan Pesanan
                                    </h2>
                                    <span class="badge bg-light text-primary fs-6 px-3 py-2">{{ count($cart) }} Item</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                {{-- Items List --}}
                                <div class="order-items-list border-bottom">
                                    @foreach ($cart as $item)
                                        @php
                                            $imagePath = null;
                                            if (!empty($item['image'])) {
                                                if (str_starts_with($item['image'], 'http')) {
                                                    $imagePath = $item['image'];
                                                } else {
                                                    $imagePath = asset('storage/' . ltrim($item['image'], '/'));
                                                }
                                            }
                                        @endphp
                                        <div class="order-item-card p-3 border-bottom">
                                            {{-- Product Image & Basic Info --}}
                                            <div class="d-flex gap-3 mb-2">
                                                {{-- Image --}}
                                                <div class="position-relative flex-shrink-0">
                                                    @if ($imagePath)
                                                        <img src="{{ $imagePath }}"
                                                            alt="{{ $item['name'] ?? 'Produk' }}"
                                                            class="order-item-image rounded-2 shadow-sm cursor-pointer"
                                                            loading="lazy"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModal"
                                                            onclick="showImageModal('{{ $imagePath }}', '{{ $item['name'] ?? 'Produk' }}')"
                                                            onerror="this.classList.add('img-error')"
                                                            style="cursor: pointer; transition: all 0.3s ease;"
                                                            title="Klik untuk lihat gambar full">
                                                    @else
                                                        <div class="order-item-placeholder bg-light rounded-2 d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-image text-muted" aria-hidden="true"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Product Details --}}
                                                <div class="flex-grow-1 min-w-0">
                                                    <h6 class="mb-1 text-dark fw-semibold text-truncate" title="{{ $item['name'] ?? 'Produk' }}">
                                                        {{ Str::limit($item['name'] ?? 'Produk', 25) }}
                                                    </h6>
                                                    <small class="text-muted d-block mb-1">
                                                        <span class="price-convert" data-price="{{ $item['price'] ?? 0 }}" data-currency="IDR">
                                                            Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                                        </span> / item
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <strong class="text-dark">×{{ $item['quantity'] ?? 1 }}</strong> item
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- Description (if available) --}}
                                            @if (!empty($item['description']))
                                                <div class="mb-2 ps-0">
                                                    <small class="text-muted d-block" style="font-size: 0.8rem; line-height: 1.3; max-height: 2.6rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                        {{ $item['description'] }}
                                                    </small>
                                                </div>
                                            @endif

                                            {{-- Specifications --}}
                                            @if (!empty($item['custom_dimensions']))
                                                <div class="mb-2 ps-0">
                                                    <small class="text-info d-block">
                                                        <i class="bi bi-rulers me-1" aria-hidden="true"></i>
                                                        @if (is_array($item['custom_dimensions']))
                                                            @foreach ($item['custom_dimensions'] as $key => $value)
                                                                <span>{{ ucfirst($key) }}: <strong>{{ $value }}</strong></span>{{ !$loop->last ? ' • ' : '' }}
                                                            @endforeach
                                                        @else
                                                            {{ $item['custom_dimensions'] }}
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif

                                            {{-- Subtotal --}}
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <small class="text-muted">Subtotal:</small>
                                                <strong class="text-primary price-convert"
                                                    data-price="{{ ($item['price'] ?? 0) * ($item['quantity'] ?? 1) }}"
                                                    data-currency="IDR">
                                                    Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                                </strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Price Summary --}}
                                <div class="p-4">
                                    {{-- Subtotal --}}
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal:</span>
                                        <span class="price-convert" data-price="{{ $subtotal }}" data-currency="IDR">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- Shipping Cost --}}
                                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                        <span class="text-muted">
                                            <i class="bi bi-truck me-1" aria-hidden="true"></i>Ongkos Kirim:
                                        </span>
                                        <span class="text-muted small"><em>Dihitung setelah checkout</em></span>
                                    </div>

                                    {{-- Total --}}
                                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                                        <strong class="fs-6">Total Pembayaran:</strong>
                                        <div class="text-end">
                                            <strong class="fs-5 text-success price-convert" data-price="{{ $total }}" data-currency="IDR">
                                                Rp {{ number_format($total, 0, ',', '.') }}
                                            </strong>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-grid gap-2 mb-3">
                                        <button type="submit" class="btn btn-success btn-lg hover-lift rounded-3" id="btnCheckout">
                                            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>Lanjutkan ke Pembayaran
                                        </button>
                                        <a href="{{ route('customer.cart.index') }}" class="btn btn-outline-secondary rounded-3 hover-lift">
                                            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Edit Keranjang
                                        </a>
                                    </div>

                                    {{-- Security Info --}}
                                    <div class="alert alert-light border border-success-subtle rounded-3 p-3 mb-0">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-shield-check text-success mt-1 flex-shrink-0" aria-hidden="true"></i>
                                            <div>
                                                <small class="text-muted d-block">
                                                    <strong class="text-dark">Pembayaran Aman</strong><br>
                                                    Semua transaksi terenkripsi dan terlindungi
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

    {{-- ===== IMAGE MODAL ===== --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0 p-4">
                    <h5 class="modal-title fw-bold" id="imageModalLabel">
                        <i class="bi bi-image me-2" aria-hidden="true"></i>Gambar Produk
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    <img id="modalImage" src="" alt="Gambar Produk" class="img-fluid rounded-2" style="max-height: 500px; max-width: 100%; object-fit: contain;">
                </div>
                <div class="modal-footer border-top bg-light p-4">
                    <span id="modalImageName" class="text-muted flex-grow-1"></span>
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* ============================================
           HERO
           ============================================ */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            padding-top: 8.5rem;
            padding-bottom: 8rem;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
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

        .wave-bottom .shape-fill {
            fill: #f8f9fa;
        }

        /* ============================================
           GRADIENT UTILITIES
           ============================================ */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        /* ============================================
           CHECKOUT SECTION
           ============================================ */
        .checkout-section {
            min-height: 60vh;
        }

        /* ============================================
           FORM
           ============================================ */
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .form-control-lg {
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }

        /* ============================================
           PAYMENT METHOD CARDS
           ============================================ */
        .payment-method-card {
            cursor: pointer;
        }

        .payment-method-card .card {
            border-color: #dee2e6;
            background-color: #fff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .payment-method-card:hover .card {
            border-color: #667eea;
            background-color: #f8f9ff;
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .payment-method-card.selected .card {
            border-color: #667eea !important;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%) !important;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.25) !important;
            transform: translateY(-2px);
        }

        .payment-method-card.selected .card::after {
            content: "✓";
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
            animation: checkmark-pop 0.3s ease;
        }

        @keyframes checkmark-pop {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .payment-method-card .card i {
            transition: transform 0.3s ease;
        }

        .payment-method-card:hover .card i,
        .payment-method-card.selected .card i {
            transform: scale(1.1);
        }

        .payment-method-card .card strong {
            color: #212529;
            transition: color 0.3s ease;
        }

        .payment-method-card:hover .card strong,
        .payment-method-card.selected .card strong {
            color: #667eea;
        }

        /* ============================================
           ORDER SUMMARY
           ============================================ */
        .sticky-summary {
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        .order-items-list {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #ffffff;
        }

        .order-items-list::-webkit-scrollbar {
            width: 6px;
        }

        .order-items-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .order-items-list::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 3px;
        }

        .order-items-list::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        .order-item-card {
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .order-item-card:hover {
            background-color: #f8f9ff;
        }

        .order-item-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            display: block;
            cursor: pointer;
        }

        .order-item-image:hover {
            transform: scale(1.15);
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            filter: brightness(1.05);
        }

        .order-item-image:active {
            transform: scale(1.1);
        }

        .order-item-placeholder {
            width: 70px;
            height: 70px;
            min-width: 70px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-item-card .badge.bg-danger {
            font-size: 0.65rem;
            font-weight: 600;
        }

        /* ============================================
           IMAGE MODAL STYLING
           ============================================ */
        .modal-content {
            backdrop-filter: blur(10px);
        }

        .modal-body {
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }

        #modalImage {
            animation: zoomIn 0.3s ease-out;
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal.fade .modal-dialog {
            transition: all 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* ============================================
           ANIMATIONS
           ============================================ */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }

        .hover-lift:active {
            transform: translateY(0);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
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
                padding-top: 80px;
                padding-bottom: 80px;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .order-item-image,
            .order-item-placeholder {
                width: 50px;
                height: 50px;
                min-width: 50px;
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 1.75rem;
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
                initFormValidation();
                showSessionMessages();
            });

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
            // SESSION MESSAGES
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
            // FORM VALIDATION
            // ============================================
            function initFormValidation() {
                const form = document.getElementById('checkoutForm');
                const btnSubmit = document.getElementById('btnCheckout');
                if (!form || !btnSubmit) return;

                form.addEventListener('submit', function(e) {
                    const phone = document.getElementById('phone');
                    const shippingAddress = document.getElementById('shipping_address');

                    // Validate Phone Number
                    if (!phone?.value.trim()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nomor Telepon Kosong',
                            text: 'Silakan isi nomor telepon terlebih dahulu',
                            confirmButtonColor: '#667eea'
                        });
                        phone?.focus();
                        return;
                    }

                    // Validate Phone Format (basic validation)
                    const phoneRegex = /^(\+62|62|0)[0-9]{9,12}$/;
                    if (!phoneRegex.test(phone.value.replace(/\s+/g, ''))) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Format Nomor Telepon Tidak Valid',
                            text: 'Format: 08xx-xxxx-xxxx atau +62',
                            confirmButtonColor: '#667eea'
                        });
                        phone?.focus();
                        return;
                    }

                    // Validate Shipping Address
                    if (!shippingAddress?.value.trim()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alamat Pengiriman Kosong',
                            text: 'Silakan isi alamat pengiriman terlebih dahulu',
                            confirmButtonColor: '#667eea'
                        });
                        shippingAddress?.focus();
                        return;
                    }

                    if (shippingAddress.value.trim().length < 20) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alamat Terlalu Singkat',
                            text: 'Alamat pengiriman harus minimal 20 karakter',
                            confirmButtonColor: '#667eea'
                        });
                        shippingAddress.focus();
                        return;
                    }

                    // All validations passed
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses...';
                    Swal.fire({
                        title: 'Memproses Pesanan',
                        html: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });
                });
            }

            // ============================================
            // IMAGE MODAL
            // ============================================
            window.showImageModal = function(imagePath, imageName) {
                const modalImage = document.getElementById('modalImage');
                const modalImageName = document.getElementById('modalImageName');
                
                if (modalImage) {
                    modalImage.src = imagePath;
                    modalImage.alt = imageName;
                }
                if (modalImageName) {
                    modalImageName.textContent = imageName;
                }
            };

        })();
    </script>
@endpush
