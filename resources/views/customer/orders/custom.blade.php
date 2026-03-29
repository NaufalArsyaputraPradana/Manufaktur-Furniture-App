@extends('layouts.app')

@section('title', 'Buat Pesanan Custom')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-orders position-relative text-white" aria-label="Pesanan custom hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0 p-0 bg-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.orders.index') }}"
                            class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-receipt-cutoff me-1" aria-hidden="true"></i>Pesanan Saya
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Pesanan Custom</li>
                </ol>
            </nav>

            <div class="text-center fade-in">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-cart-plus-fill me-2" aria-hidden="true"></i>Buat Pesanan Custom
                </h1>
                <p class="lead mb-0 opacity-90">
                    Pilih produk dari katalog atau buat pesanan dengan spesifikasi desain khusus Anda sendiri.
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
    <section class="custom-order-section bg-light" aria-label="Form pesanan custom">
        <div class="container">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 flex-shrink-0" aria-hidden="true"></i>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-2">Terjadi Kesalahan:</h6>
                            <ul class="mb-0 px-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('customer.orders.custom.store') }}" method="POST" id="orderForm"
                enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row g-4">

                    {{-- ===== LEFT: Order Items ===== --}}
                    <div class="col-lg-8">
                        <div class="card shadow-sm rounded-4 border-0 mb-4 bg-white">
                            <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center gap-2 rounded-top-4"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h5 class="mb-0 fw-bold text-white">
                                    <i class="bi bi-cart-fill me-2" aria-hidden="true"></i>Item Pesanan
                                </h5>
                                <button type="button"
                                    class="btn btn-light btn-sm rounded-pill px-3 hover-lift fw-bold text-primary shadow-sm"
                                    onclick="addOrderItem()">
                                    <i class="bi bi-plus-circle-fill me-1" aria-hidden="true"></i>Tambah Item
                                </button>
                            </div>
                            <div class="card-body p-4 bg-light rounded-bottom-4">
                                <div id="orderItemsContainer"></div>
                                <div class="alert alert-info rounded-4 border-0 shadow-sm mb-0" role="alert"
                                    id="emptyItemsAlert">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle-fill fs-4 me-3" aria-hidden="true"></i>
                                        <div>Klik <strong class="mx-1">"Tambah Item"</strong> untuk menambahkan produk ke
                                            dalam form pesanan Anda.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== RIGHT: Info & Summary ===== --}}
                    <div class="col-lg-4">
                        {{-- Shipping Info --}}
                        <div class="card shadow-sm rounded-4 border-0 mb-4 bg-white animate-on-scroll">
                            <div class="card-header bg-info text-white border-0 p-4 rounded-top-4">
                                <h5 class="mb-0 fw-bold text-white d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill me-2" aria-hidden="true"></i>Informasi Pengiriman
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="shipping_address"
                                        class="form-label fw-bold small text-muted text-uppercase mb-2">
                                        Alamat Lengkap Pengiriman <span class="text-danger" aria-hidden="true">*</span>
                                    </label>
                                    <textarea class="form-control form-control-lg bg-light @error('shipping_address') is-invalid @enderror"
                                        id="shipping_address" name="shipping_address" rows="3" placeholder="Jl. Merdeka No. 123..." required>{{ old('shipping_address', auth()->user()?->address ?? '') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-0">
                                    <label for="notes" class="form-label fw-bold small text-muted text-uppercase mb-2">
                                        Catatan Keseluruhan <span
                                            class="text-secondary fw-normal text-lowercase">(Opsional)</span>
                                    </label>
                                    <textarea class="form-control bg-light @error('notes') is-invalid @enderror" id="notes" name="notes"
                                        rows="3" placeholder="Catatan tambahan untuk tim admin kami...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Order Summary (sticky) --}}
                        <div class="card shadow-sm rounded-4 border-0 sticky-sidebar bg-white animate-on-scroll">
                            <div class="card-header bg-success text-white border-0 p-4 rounded-top-4">
                                <h5 class="mb-0 fw-bold text-white d-flex align-items-center">
                                    <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Ringkasan Form
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                {{-- User Preview Info --}}
                                <div class="mb-4 pb-3 border-bottom border-light">
                                    <small class="text-muted d-block fw-bold text-uppercase mb-1">Pemesan:</small>
                                    <div class="fw-bold text-dark"><i class="bi bi-person-circle me-2 text-primary"
                                            aria-hidden="true"></i>{{ auth()->user()?->name }}</div>
                                </div>

                                <div class="bg-light p-3 rounded-4 mb-4 border border-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">Total Estimasi:</strong>
                                        <strong class="fs-4 text-success" id="totalDisplay">Rp 0</strong>
                                    </div>
                                    <small class="text-muted d-block mt-2 lh-sm" style="font-size: 0.75rem;">*Harga item
                                        custom akan menjadi 0 sampai dikonfirmasi oleh Admin.</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit"
                                        class="btn btn-success btn-lg rounded-pill hover-lift shadow-sm fw-bold"
                                        id="submitBtn">
                                        <i class="bi bi-send-fill me-2" aria-hidden="true"></i>Kirim Permintaan
                                    </button>
                                    <a href="{{ route('home') }}"
                                        class="btn btn-light border rounded-pill hover-lift fw-medium">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </section>

    {{-- ===== ORDER ITEM TEMPLATE ===== --}}
    <template id="orderItemTemplate">
        <div class="card mb-4 order-item rounded-4 border border-light shadow-sm bg-white overflow-hidden">
            <div
                class="card-header bg-light border-bottom border-light p-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary text-uppercase small">
                    <i class="bi bi-box-seam-fill me-2" aria-hidden="true"></i>Item <span class="item-number"></span>
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-medium px-3"
                    onclick="removeOrderItem(this)">
                    <i class="bi bi-trash-fill me-1" aria-hidden="true"></i>Hapus
                </button>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase">Pilih Basis Produk</label>
                        <select class="form-select form-select-lg product-select bg-light"
                            name="products[INDEX][product_id]" onchange="onProductChange(this)">
                            <option value="">-- Pilih dari Katalog --</option>
                            <option value="custom">🎨 Produk Custom (Spesifikasi Baru Penuh)</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-price="{{ $product->base_price ?? '' }}">
                                    {{ $product->name }}{{ $product->base_price !== null ? ' – Rp ' . number_format($product->base_price, 0, ',', '.') : ' – Tanya Harga' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nama Produk <span
                                class="text-danger" aria-hidden="true">*</span></label>
                        <input type="text" class="form-control form-control-lg product-name"
                            name="products[INDEX][product_name]" placeholder="Nama produk yang diinginkan" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Jumlah Pesanan <span
                                class="text-danger" aria-hidden="true">*</span></label>
                        <input type="number" class="form-control form-control-lg quantity-input"
                            name="products[INDEX][quantity]" min="1" value="1"
                            onchange="calculateItemTotal(this)" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase">Harga Satuan</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                            <input type="number" class="form-control price-input bg-light border-start-0 ps-0"
                                name="products[INDEX][unit_price]" min="0" step="1000"
                                onchange="calculateItemTotal(this)" readonly>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="d-flex align-items-center bg-light p-3 rounded-3 border border-light">
                            <strong class="me-auto text-muted text-uppercase small fw-bold">Subtotal Item:</strong>
                            <span class="subtotal-display text-primary fw-bold fs-5 price-convert" data-price="0"
                                data-currency="IDR">Rp 0</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input is-custom-check p-2" type="checkbox" role="switch"
                                name="products[INDEX][is_custom]" value="1" onchange="toggleCustomSpecs(this)"
                                style="cursor: pointer;">
                            <label class="form-check-label fw-bold text-dark ms-2 pt-1" style="cursor: pointer;">
                                <i class="bi bi-magic me-1 text-warning" aria-hidden="true"></i>Kustomisasi Spesifikasi
                            </label>
                        </div>
                    </div>

                    <div class="col-12 custom-specs-section" style="display:none;">
                        <div class="card border-warning bg-warning bg-opacity-10 rounded-4 border-0 mt-2 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom border-warning pb-2">
                                    <i class="bi bi-pencil-square me-2 text-warning" aria-hidden="true"></i>Detail
                                    Kustomisasi
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Deskripsi Perubahan
                                        <span class="text-danger" aria-hidden="true">*</span></label>
                                    <textarea class="form-control bg-white" name="products[INDEX][customizations][description]" rows="3"
                                        placeholder="Jelaskan detail desain atau spesifikasi secara spesifik..."></textarea>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Dimensi
                                            (cm)</label>
                                        <input type="text" class="form-control bg-white"
                                            name="products[INDEX][customizations][dimensions]"
                                            placeholder="P x L x T (cth: 200x80x75)">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Material
                                            Kayu</label>
                                        <input type="text" class="form-control bg-white"
                                            name="products[INDEX][customizations][material_type]"
                                            placeholder="Cth: Kayu Jati Perhutani">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Finishing /
                                            Warna</label>
                                        <input type="text" class="form-control bg-white"
                                            name="products[INDEX][customizations][color_finishing]"
                                            placeholder="Cth: Natural, Walnut, Melamic">
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Upload Gambar
                                        Referensi <span
                                            class="text-secondary fw-normal text-lowercase">(Opsional)</span></label>
                                    <input type="file" class="form-control bg-white image-upload"
                                        name="products[INDEX][customizations][design_image]"
                                        accept="image/jpeg,image/png,image/webp,application/pdf"
                                        onchange="previewImage(this)">
                                    <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Format
                                        JPG, PNG, atau PDF (maks. 2MB)</small>

                                    {{-- Image Preview --}}
                                    <div class="image-preview-container mt-3" style="display:none;">
                                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                            <div
                                                class="card-header bg-light border-0 p-3 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-bold small text-uppercase">Preview Gambar</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                                                    onclick="removeImagePreview(this)">
                                                    <i class="bi bi-x-lg" aria-hidden="true"></i> Hapus
                                                </button>
                                            </div>
                                            <div class="card-body p-3 text-center">
                                                <img src="" alt="Preview" class="image-preview rounded border"
                                                    style="max-height:200px; max-width:100%; object-fit:contain; cursor:pointer;"
                                                    onclick="openImagePreviewModal(this)">
                                                <div
                                                    class="mt-2 text-start p-2 bg-light rounded border-start border-3 border-primary">
                                                    <small class="text-muted d-block text-truncate">
                                                        <i class="bi bi-file-earmark-image me-1 text-primary"
                                                            aria-hidden="true"></i>
                                                        <span class="file-name fw-medium">-</span> (<span
                                                            class="file-size">-</span>)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Image Preview Modal --}}
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg bg-dark">
                <div class="modal-header border-0 bg-transparent position-absolute top-0 w-100 p-3" style="z-index: 10;">
                    <h5 class="modal-title text-white drop-shadow fw-bold">
                        <i class="bi bi-image me-2" aria-hidden="true"></i>Preview Referensi
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-sm" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height:50vh;">
                    <img id="modalPreviewImage" src="" alt="Full Preview" class="img-fluid"
                        style="max-height:85vh; object-fit:contain;">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* HERO */
        .hero-orders {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            padding-top: 9rem;
            padding-bottom: 5rem;
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
        }

        .hover-opacity {
            transition: opacity 0.3s ease;
        }

        .hover-opacity:hover {
            opacity: 0.8;
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
            width: 100%;
            height: 60px;
        }

        .wave-bottom .shape-fill {
            fill: #f8f9fa;
        }

        /* SECTION */
        .custom-order-section {
            padding-top: 2rem;
            padding-bottom: 5rem;
            min-height: 60vh;
        }

        .rounded-top-4 {
            border-radius: 1rem 1rem 0 0 !important;
        }

        .rounded-bottom-4 {
            border-radius: 0 0 1rem 1rem !important;
        }

        /* ITEMS */
        .order-item {
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        .order-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
            border-color: #cbd5e1 !important;
        }

        /* SIDEBAR */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 10;
        }

        /* FORM */
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            background-color: #fff !important;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        /* PREVIEW */
        .image-preview-container {
            animation: fadeSlideIn 0.4s ease;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .image-preview {
            transition: transform 0.3s ease;
        }

        .image-preview:hover {
            transform: scale(1.03);
            border-color: #667eea !important;
        }

        .drop-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        }

        /* BUTTONS */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .hover-lift:active {
            transform: translateY(0);
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
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .sticky-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-orders {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .custom-order-section {
                padding-top: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-orders h1 {
                font-size: 2rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            let itemIndex = 0;

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize first item
                addOrderItem();

                // Intersection Observer for scroll animations
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
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

                // Form Validation & Submit handling
                const orderForm = document.getElementById('orderForm');
                if (orderForm) {
                    orderForm.addEventListener('submit', handleFormSubmit);
                }

                // Modal Cleanup
                document.getElementById('imagePreviewModal')?.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                });
            });

            // ===== ADD / REMOVE ITEMS =====
            window.addOrderItem = function() {
                const template = document.getElementById('orderItemTemplate');
                const container = document.getElementById('orderItemsContainer');
                const emptyMsg = document.getElementById('emptyItemsAlert');
                if (!template || !container) return;

                const wrapper = document.createElement('div');
                wrapper.innerHTML = template.innerHTML.replace(/INDEX/g, itemIndex++);
                const clone = wrapper.firstElementChild;

                // Add tiny animation class
                clone.style.opacity = '0';
                clone.style.transform = 'translateY(15px)';
                container.appendChild(clone);

                // Trigger reflow
                void clone.offsetWidth;
                clone.style.transition = 'all 0.4s ease';
                clone.style.opacity = '1';
                clone.style.transform = 'translateY(0)';

                renumberItems();

                if (emptyMsg) emptyMsg.style.display = 'none';
            };

            window.removeOrderItem = function(button) {
                const item = button.closest('.order-item');
                if (!item) return;

                item.style.opacity = '0';
                item.style.transform = 'translateX(-30px)';

                setTimeout(() => {
                    item.remove();
                    renumberItems();

                    const emptyMsg = document.getElementById('emptyItemsAlert');
                    if (emptyMsg && document.querySelectorAll('.order-item').length === 0) {
                        emptyMsg.style.display = 'block';
                    }
                    calculateOrderTotal();
                }, 300);
            };

            function renumberItems() {
                document.querySelectorAll('.order-item').forEach((item, i) => {
                    const span = item.querySelector('.item-number');
                    if (span) span.textContent = i + 1;
                });
            }

            // ===== PRODUCT SELECTION =====
            window.onProductChange = function(select) {
                const item = select.closest('.order-item');
                const nameInput = item.querySelector('.product-name');
                const priceInput = item.querySelector('.price-input');
                const customCheck = item.querySelector('.is-custom-check');
                const opt = select.options[select.selectedIndex];

                if (select.value && select.value !== 'custom') {
                    nameInput.value = opt.dataset.name || '';
                    priceInput.value = opt.dataset.price || 0;
                    customCheck.checked = false;
                } else if (select.value === 'custom') {
                    nameInput.value = '';
                    priceInput.value = 0;
                    customCheck.checked = true;
                } else {
                    // Default empty
                    nameInput.value = '';
                    priceInput.value = 0;
                    customCheck.checked = false;
                }

                toggleCustomSpecs(customCheck);
                calculateItemTotal(select);
            };

            // ===== CUSTOM SPECS TOGGLE =====
            window.toggleCustomSpecs = function(checkbox) {
                const item = checkbox.closest('.order-item');
                const specsSection = item.querySelector('.custom-specs-section');
                const priceInput = item.querySelector('.price-input');

                if (checkbox.checked) {
                    specsSection.style.display = 'block';
                    priceInput.value = 0;
                    priceInput.readOnly = true;
                } else {
                    specsSection.style.display = 'none';
                    priceInput.readOnly = false;
                    const sel = item.querySelector('.product-select');
                    if (sel && sel.value && sel.value !== 'custom') {
                        const opt = sel.options[sel.selectedIndex];
                        priceInput.value = opt?.dataset?.price || 0;
                    }
                }

                calculateItemTotal(checkbox);
            };

            // ===== TOTAL CALCULATIONS =====
            window.calculateItemTotal = function(element) {
                const item = element.closest('.order-item');
                if (!item) return;

                const qty = parseFloat(item.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(item.querySelector('.price-input').value) || 0;
                const display = item.querySelector('.subtotal-display');

                if (display) {
                    display.textContent = 'Rp ' + formatNumber(qty * price);
                }

                calculateOrderTotal();
            };

            function calculateOrderTotal() {
                let total = 0;
                document.querySelectorAll('.order-item').forEach(item => {
                    const qty = parseFloat(item.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(item.querySelector('.price-input').value) || 0;
                    total += qty * price;
                });
                const totalEl = document.getElementById('totalDisplay');
                if (totalEl) {
                    totalEl.textContent = 'Rp ' + formatNumber(total);
                }
            }

            function formatNumber(n) {
                return Math.round(n).toLocaleString('id-ID');
            }

            // ===== IMAGE PREVIEW =====
            window.previewImage = function(input) {
                const item = input.closest('.order-item');
                if (!item) return;

                const file = input.files[0];
                if (!file) return;

                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal adalah 2MB.',
                        confirmButtonColor: '#dc3545'
                    });
                    input.value = '';
                    return;
                }

                if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Valid',
                        text: 'Hanya file gambar (JPG, PNG) atau dokumen (PDF) yang diperbolehkan.',
                        confirmButtonColor: '#dc3545'
                    });
                    input.value = '';
                    return;
                }

                const container = item.querySelector('.image-preview-container');
                const previewImg = item.querySelector('.image-preview');
                const nameSpan = item.querySelector('.file-name');
                const sizeSpan = item.querySelector('.file-size');

                if (nameSpan) nameSpan.textContent = file.name;
                if (sizeSpan) sizeSpan.textContent = formatFileSize(file.size);

                if (file.type === 'application/pdf') {
                    previewImg.src =
                        "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='none' stroke='%23dc3545' stroke-width='2'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'/%3E%3Cpolyline points='14 2 14 8 20 8'/%3E%3C/svg%3E";
                    if (container) container.style.display = 'block';
                } else {
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewImg.src = e.target.result;
                        if (container) container.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            };

            window.removeImagePreview = function(button) {
                const item = button.closest('.order-item');
                if (!item) return;

                const input = item.querySelector('.image-upload');
                const container = item.querySelector('.image-preview-container');
                const previewImg = item.querySelector('.image-preview');
                const nameSpan = item.querySelector('.file-name');
                const sizeSpan = item.querySelector('.file-size');

                if (container) {
                    container.style.opacity = '0';
                    setTimeout(() => {
                        if (input) input.value = '';
                        if (previewImg) previewImg.src = '';
                        if (nameSpan) nameSpan.textContent = '-';
                        if (sizeSpan) sizeSpan.textContent = '-';
                        container.style.display = 'none';
                        container.style.opacity = '1';
                    }, 300);
                }
            };

            window.openImagePreviewModal = function(imgEl) {
                const modal = document.getElementById('imagePreviewModal');
                const modalImg = document.getElementById('modalPreviewImage');
                if (!modal || !modalImg) return;

                modalImg.src = imgEl.src;
                if (typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(modal).show();
                }
            };

            function formatFileSize(bytes) {
                if (!bytes) return '0 Bytes';
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
                return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
            }

            // ===== FORM VALIDATION =====
            function handleFormSubmit(e) {
                const items = document.querySelectorAll('.order-item');

                if (items.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Item',
                        text: 'Silakan tambahkan minimal 1 item ke pesanan Anda!',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                let errorMsg = '';
                items.forEach(item => {
                    const isCustom = item.querySelector('.is-custom-check')?.checked;
                    if (isCustom) {
                        const desc = item.querySelector('[name*="[customizations][description]"]')?.value
                            ?.trim();
                        if (!desc) {
                            errorMsg =
                                'Untuk item produk custom, harap isi deskripsi spesifikasi secara detail!';
                        }
                    }
                });

                if (errorMsg) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Deskripsi Diperlukan',
                        text: errorMsg,
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // Show loading state
                const btn = document.getElementById('submitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses...';
                }

                Swal.fire({
                    title: 'Membuat Pesanan...',
                    html: 'Sistem sedang menyimpan spesifikasi pesanan Anda.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-4'
                    },
                    didOpen: () => Swal.showLoading()
                });
            }

        })();
    </script>
@endpush
