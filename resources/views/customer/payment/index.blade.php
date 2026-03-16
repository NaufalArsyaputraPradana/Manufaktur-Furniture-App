@extends('layouts.app')

@section('title', 'Pembayaran - ' . $order->order_number)

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section position-relative text-white" aria-label="Pembayaran hero">
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
                        <a href="{{ route('customer.orders.index') }}"
                            class="text-white text-decoration-none opacity-75 hover-opacity-100">
                            <i class="bi bi-receipt-cutoff me-1" aria-hidden="true"></i>Pesanan Saya
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="text-white text-decoration-none opacity-75 hover-opacity-100">
                            {{ $order->order_number }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Pembayaran</li>
                </ol>
            </nav>

            <div class="text-center">
                <h1 class="display-5 fw-bold mb-3 text-white">
                    <i class="bi bi-credit-card-fill me-3 text-warning" aria-hidden="true"></i>Pembayaran Pesanan
                </h1>
                <p class="lead mb-0 text-white opacity-90">
                    Selesaikan pembayaran untuk pesanan {{ $order->order_number }}
                </p>
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
    <section class="payment-section py-5 bg-light" aria-label="Form pembayaran">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    {{-- Session Alerts --}}
                    @foreach (['success' => ['check-circle-fill', 'alert-success'], 'error' => ['exclamation-triangle-fill', 'alert-danger']] as $type => [$icon, $class])
                        @if (session($type))
                            <div class="alert {{ $class }} alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
                                role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-{{ $icon }} me-2 mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="flex-grow-1">{{ session($type) }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Order Summary --}}
                    <div class="card shadow-sm rounded-4 border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white border-0 p-4">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Ringkasan Pesanan
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Nomor Pesanan</small>
                                    <strong>{{ $order->order_number }}</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block mb-1">Tanggal Pesanan</small>
                                    <strong>{{ $order->created_at->format('d M Y') }}</strong>
                                </div>
                            </div>
                            <hr>
                            @php
                                $calculatedTotal = $order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                            @endphp
                            <div class="bg-light p-4 rounded-4 text-center">
                                <small class="text-muted d-block mb-2">Total Pembayaran</small>
                                <h2 class="mb-0 text-success fw-bold price-convert" data-price="{{ $calculatedTotal }}"
                                    data-currency="IDR">Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</h2>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Form --}}
                    <div class="card shadow-sm rounded-4 border-0">
                        <div class="card-header bg-success text-white border-0 p-4">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-credit-card-2-front-fill me-2" aria-hidden="true"></i>Metode Pembayaran
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('customer.orders.payment.process', $order) }}" method="POST"
                                enctype="multipart/form-data" id="paymentForm">
                                @csrf

                                {{-- Payment Method Selection --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        Pilih Metode Pembayaran <span class="text-danger" aria-hidden="true">*</span>
                                    </label>
                                    <div class="row g-3">
                                        @foreach ([['id' => 'transfer', 'value' => 'transfer', 'icon' => 'bi-bank2', 'color' => 'text-primary', 'label' => 'Transfer Bank', 'sub' => 'Rekomendasi'], ['id' => 'credit_card', 'value' => 'credit_card', 'icon' => 'bi-credit-card-fill', 'color' => 'text-success', 'label' => 'Kartu Kredit', 'sub' => 'Visa, Mastercard'], ['id' => 'cash', 'value' => 'cash', 'icon' => 'bi-cash-stack', 'color' => 'text-warning', 'label' => 'Tunai', 'sub' => 'Bayar di tempat']] as $method)
                                            <div class="col-md-4">
                                                <input class="form-check-input d-none" type="radio" name="payment_method"
                                                    id="{{ $method['id'] }}" value="{{ $method['value'] }}"
                                                    {{ $loop->first ? 'required' : '' }}>
                                                <label class="payment-method-card d-block h-100" for="{{ $method['id'] }}">
                                                    <div class="card h-100 p-4 border-2">
                                                        <div class="text-center">
                                                            <i class="bi {{ $method['icon'] }} display-3 {{ $method['color'] }} mb-3 d-block"
                                                                aria-hidden="true"></i>
                                                            <strong
                                                                class="d-block mb-1 fs-6">{{ $method['label'] }}</strong>
                                                            <small class="text-muted">{{ $method['sub'] }}</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('payment_method')
                                        <div class="text-danger mt-2 small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Bank Info --}}
                                <div class="alert alert-info rounded-4 border-0 mb-4" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill fs-4 me-3 flex-shrink-0" aria-hidden="true"></i>
                                        <div>
                                            <h6 class="fw-bold mb-2">Informasi Rekening</h6>
                                            <hr class="my-2">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Bank BCA</strong></p>
                                                    <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                                                    <p class="mb-0">a.n. <strong>UD Bisa Furniture</strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Bank BNI</strong></p>
                                                    <p class="mb-1">No. Rekening: <strong>0987654321</strong></p>
                                                    <p class="mb-0">a.n. <strong>UD Bisa Furniture</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Proof Upload --}}
                                <div class="mb-4">
                                    <label for="payment_proof" class="form-label fw-bold">
                                        <i class="bi bi-paperclip me-1 text-primary" aria-hidden="true"></i>
                                        Upload Bukti Pembayaran (Opsional)
                                    </label>
                                    <input type="file"
                                        class="form-control form-control-lg @error('payment_proof') is-invalid @enderror"
                                        id="payment_proof" name="payment_proof" accept="image/*"
                                        onchange="previewPaymentProof(this)">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Format: JPG, PNG (Max
                                        2MB)
                                    </small>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    {{-- Image Preview --}}
                                    <div class="payment-proof-preview mt-3" id="paymentProofPreview"
                                        style="display:none;">
                                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                            <div
                                                class="card-header bg-light border-0 p-3 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-bold text-dark">
                                                    <i class="bi bi-image me-2 text-primary"
                                                        aria-hidden="true"></i>Preview Bukti Pembayaran
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removePaymentProof()">
                                                    <i class="bi bi-x-lg" aria-hidden="true"></i> Hapus
                                                </button>
                                            </div>
                                            <div class="card-body p-3">
                                                <img src="" id="paymentProofImage" alt="Preview Bukti Pembayaran"
                                                    class="img-fluid rounded border w-100"
                                                    style="max-height:400px; object-fit:contain; cursor:pointer;"
                                                    onclick="openPaymentProofModal()">
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted">
                                                        <i class="bi bi-file-earmark-image me-1" aria-hidden="true"></i>
                                                        <span id="fileName">-</span> (<span id="fileSize">-</span>)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Buttons --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill" id="submitBtn">
                                        <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>Konfirmasi
                                        Pembayaran
                                    </button>
                                    <a href="{{ route('customer.orders.show', $order) }}"
                                        class="btn btn-outline-secondary btn-lg rounded-pill">
                                        <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Kembali ke Detail Pesanan
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="alert alert-warning rounded-4 border-0 mt-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <div>
                                <strong>Catatan:</strong> Setelah melakukan pembayaran, pesanan Anda akan segera diproses
                                oleh tim kami. Konfirmasi pembayaran biasanya memakan waktu 1-2 hari kerja.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Payment Proof Modal --}}
    <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="paymentProofModalLabel">
                        <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 bg-light">
                    <div class="text-center p-4">
                        <img src="" id="modalPaymentProofImage" class="img-fluid rounded"
                            alt="Full size payment proof" style="max-height:70vh; max-width:100%; object-fit:contain;">
                    </div>
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
            min-height: 280px;
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
            z-index: 2;
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
           PAYMENT SECTION
           ============================================ */
        .payment-section {
            min-height: 60vh;
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
            transition: color 0.3s ease;
        }

        .payment-method-card:hover .card strong,
        .payment-method-card.selected .card strong {
            color: #667eea;
        }

        /* ============================================
           PAYMENT PROOF PREVIEW
           ============================================ */
        .payment-proof-preview {
            animation: fadeSlideIn 0.3s ease;
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

        #paymentProofImage {
            transition: transform 0.3s ease;
            background: #f8f9fa;
        }

        #paymentProofImage:hover {
            transform: scale(1.02);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 768px) {
            .hero-section {
                padding-top: 6rem;
                padding-bottom: 4rem;
                min-height: 240px;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .hero-section h1 {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding-top: 6rem;
                min-height: 200px;
            }

            .hero-section h1 {
                font-size: 1.5rem;
            }

            .wave-bottom svg {
                height: 30px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            function init() {
                initPaymentMethodCards();
                initFormSubmission();
                initModalCleanup();
            }

            // ============================================
            // PAYMENT METHOD CARDS
            // ============================================
            function initPaymentMethodCards() {
                document.querySelectorAll('.payment-method-card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        const radio = document.getElementById(this.getAttribute('for'));
                        if (radio) {
                            radio.checked = true;
                            updateCardStates();
                        }
                    });
                });
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                    radio.addEventListener('change', updateCardStates);
                });
            }

            function updateCardStates() {
                document.querySelectorAll('.payment-method-card').forEach(function(card) {
                    const radio = document.getElementById(card.getAttribute('for'));
                    card.classList.toggle('selected', !!(radio && radio.checked));
                });
            }

            // ============================================
            // PAYMENT PROOF PREVIEW
            // ============================================
            window.previewPaymentProof = function(input) {
                const file = input.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal adalah 2MB',
                        confirmButtonColor: '#667eea'
                    });
                    input.value = '';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Valid',
                        text: 'Harap upload file gambar (JPG, PNG)',
                        confirmButtonColor: '#667eea'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('paymentProofImage').src = e.target.result;
                    document.getElementById('paymentProofPreview').style.display = 'block';
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('fileSize').textContent = formatFileSize(file.size);
                };
                reader.readAsDataURL(file);
            };

            window.removePaymentProof = function() {
                document.getElementById('payment_proof').value = '';
                document.getElementById('paymentProofImage').src = '';
                document.getElementById('paymentProofPreview').style.display = 'none';
                document.getElementById('fileName').textContent = '-';
                document.getElementById('fileSize').textContent = '-';
            };

            window.openPaymentProofModal = function() {
                const modalImg = document.getElementById('modalPaymentProofImage');
                const modalEl = document.getElementById('paymentProofModal');
                if (!modalImg || !modalEl) return;
                modalImg.src = document.getElementById('paymentProofImage').src;
                new bootstrap.Modal(modalEl).show();
            };

            function formatFileSize(bytes) {
                if (!bytes) return '0 Bytes';
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
                return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
            }

            // ============================================
            // FORM SUBMISSION
            // ============================================
            function initFormSubmission() {
                const form = document.getElementById('paymentForm');
                const submitBtn = document.getElementById('submitBtn');
                if (!form) return;

                form.addEventListener('submit', function(e) {
                    if (!document.querySelector('input[name="payment_method"]:checked')) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Metode Pembayaran',
                            text: 'Silakan pilih metode pembayaran terlebih dahulu',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses...';
                    }
                    Swal.fire({
                        title: 'Memproses Pembayaran',
                        text: 'Mohon tunggu sebentar...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                });
            }

            // ============================================
            // MODAL CLEANUP
            // ============================================
            function initModalCleanup() {
                document.getElementById('paymentProofModal')?.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                });
            }

        })();
    </script>
@endpush
