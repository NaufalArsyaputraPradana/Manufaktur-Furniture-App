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
                <div class="col-lg-6">

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
                    <div class="card shadow-lg rounded-4 border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white border-0 p-4">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Ringkasan Pesanan
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Nomor Pesanan</small>
                                    <h6 class="mb-0 text-dark">{{ $order->order_number }}</h6>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Tanggal Pesanan</small>
                                    <small class="text-dark">{{ $order->created_at->format('d M Y') }}</small>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block mb-1">Status</small>
                                    <small class="badge bg-warning">Menunggu Pembayaran</small>
                                </div>
                            </div>
                            <hr>
                            @php
                                $calculatedTotal = $order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
                            @endphp
                            <div class="bg-light p-4 rounded-4 text-center border-2 border-success border-opacity-25">
                                <small class="text-muted d-block mb-2">Jumlah Pembayaran</small>
                                <h2 class="mb-0 text-success fw-bold price-convert" data-price="{{ $calculatedTotal }}"
                                    data-currency="IDR">Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</h2>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Info --}}
                    <div class="card shadow-lg rounded-4 border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="alert alert-info rounded-4 border-0 mb-0" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-shield-check fs-5 me-3 text-info shrink-0" aria-hidden="true"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2 text-dark">
                                            <i class="bi bi-lightning-fill me-1 text-warning"></i>Pembayaran Aman & Terpercaya
                                        </h6>
                                        <p class="mb-2 text-dark small">Pilih metode pembayaran pilihan Anda di jendela berikutnya yang disediakan oleh Midtrans:</p>
                                        <ul class="mb-0 ps-3 small text-dark">
                                            <li class="mb-1">💳 <strong>E-Wallet:</strong> GoPay, OVO, DANA, Link Aja</li>
                                            <li class="mb-1">🏦 <strong>Virtual Account:</strong> BCA, BNI, Mandiri, Permata, CIMB</li>
                                            <li class="mb-1">📱 <strong>QRIS:</strong> Scan & bayar langsung</li>
                                            <li>✓ <strong>Pembayaran otomatis</strong> — Status diperbarui langsung</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Confirmation Form --}}
                    <div class="card shadow-lg rounded-4 border-0">
                        <div class="card-body p-4">
                            <form action="{{ route('customer.orders.payment.process', $order) }}" method="POST"
                                enctype="multipart/form-data" id="paymentForm">
                                @csrf

                                {{-- Confirmation Button --}}
                                <div class="d-grid gap-3 mb-3">
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold" id="submitBtn">
                                        <i class="bi bi-credit-card-fill me-2" aria-hidden="true"></i>Lanjutkan ke Pembayaran
                                    </button>
                                    <a href="{{ route('customer.orders.show', $order) }}"
                                        class="btn btn-outline-secondary btn-lg rounded-pill fw-bold">
                                        <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Kembali
                                    </a>
                                </div>

                                {{-- HIDDEN: Midtrans default method --}}
                                <input type="hidden" name="payment_method" value="midtrans">
                            </form>
                        </div>
                    </div>

                    <div class="alert alert-warning rounded-4 border-0 mt-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1 shrink-0" aria-hidden="true"></i>
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

        /* ============================================
           PAYMENT METHOD BADGES (Modern Grid Style)
           ============================================ */
        .payment-method-badge {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-badge-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: #fff;
            border: 2px solid #dee2e6;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            min-height: 140px;
            position: relative;
        }

        .payment-badge-img {
            max-height: 50px;
            max-width: 100%;
            object-fit: contain;
            filter: grayscale(0%);
            transition: all 0.3s ease;
        }

        .payment-method-badge:hover .payment-badge-card {
            border-color: #667eea;
            background-color: #f8f9ff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-4px);
        }

        input[type="radio"][name="payment_method"]:checked + .payment-method-badge .payment-badge-card {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.25);
            transform: translateY(-4px);
        }

        input[type="radio"][name="payment_method"]:checked + .payment-method-badge .payment-badge-card::after {
            content: "✓";
            position: absolute;
            top: -8px;
            right: -8px;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            animation: checkmark-pop 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .payment-method-badge:hover .payment-badge-img {
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        /* ============================================
           PAYMENT SECTION HEADERS
           ============================================ */
        .payment-section-header {
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #dee2e6;
        }

        .payment-section-header h6 {
            margin-bottom: 0;
            color: #495057;
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
                initMidtrans();
            }

            // ============================================
            // PAYMENT METHOD CARDS
            // ============================================
            function initPaymentMethodCards() {
                document.querySelectorAll('.payment-method-badge').forEach(function(card) {
                    card.addEventListener('click', function() {
                        const radio = document.getElementById(this.previousElementSibling.id);
                        if (radio) {
                            radio.checked = true;
                            radio.dispatchEvent(new Event('change', { bubbles: true }));
                            updatePaymentMethodUI();
                        }
                    });
                });
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                    radio.addEventListener('change', updatePaymentMethodUI);
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

                const orderId = '{{ $order->id }}';
                const orderShowUrl = '{{ route('customer.orders.show', $order) }}';
                const csrfToken = '{{ csrf_token() }}';

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show loading dialog
                    Swal.fire({
                        title: 'Memproses Pembayaran',
                        text: 'Mohon tunggu sebentar...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Disable button to prevent double-click
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses...';
                    }
                    
                    // Call Midtrans payment
                    handleMidtransPayment(orderId, orderShowUrl, csrfToken);
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

            // ============================================
            // PAYMENT METHOD UI TOGGLES
            // ============================================
            function updatePaymentMethodUI() {
                const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
                const bankInfo = document.getElementById('bankInfo');
                const midtransInfo = document.getElementById('midtransInfo');
                const paymentProofSection = document.getElementById('paymentProofSection');
                const proofInput = document.querySelector('input[name="payment_proof"]');

                // Midtrans methods: gopay, ovo, dana, linkaja, bca_va, bni_va, mandiri_va, permata_va, cimb_va, qris
                const midtransMethods = ['gopay', 'ovo', 'dana', 'linkaja', 'bca_va', 'bni_va', 'mandiri_va', 'permata_va', 'cimb_va', 'qris'];
                const isMidtrans = midtransMethods.includes(selected);

                if (isMidtrans) {
                    // Show Midtrans info, hide bank info and payment proof
                    if (bankInfo) bankInfo.style.display = 'none';
                    if (midtransInfo) midtransInfo.style.display = 'block';
                    if (paymentProofSection) paymentProofSection.style.display = 'none';
                    if (proofInput) {
                        proofInput.removeAttribute('required');
                        proofInput.value = '';
                    }
                } else {
                    // Show bank info and payment proof, hide Midtrans info
                    if (bankInfo) bankInfo.style.display = 'block';
                    if (midtransInfo) midtransInfo.style.display = 'none';
                    if (paymentProofSection) paymentProofSection.style.display = 'block';
                    if (proofInput) {
                        proofInput.setAttribute('required', 'required');
                    }
                }
            }

            // MIDTRANS SNAP INTEGRATION
            function initMidtrans() {
                // Initialize payment method UI on page load (Midtrans is always the method)
                updatePaymentMethodUI();
            }

            function handleMidtransPayment(orderId, orderShowUrl, csrfToken) {
                console.log('🔄 Starting Midtrans payment for order:', orderId);
                
                // Request snap token
                fetch(`/api/payment/midtrans/token/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({}),
                    credentials: 'include'  // Include cookies for authentication
                }).then(r => {
                    console.log('✅ Response Status:', r.status);
                    if (!r.ok) {
                        return r.json().then(data => {
                            throw new Error(`HTTP ${r.status}: ${data?.message || 'Unknown error'}`);
                        });
                    }
                    return r.json();
                }).then(data => {
                    console.log('📦 Response Data:', data);
                    
                    if (!data || data.status !== 'success') {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal membuat transaksi',
                            text: data?.message || 'Coba lagi',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }

                    const snapToken = data.data.snap_token;
                    const scriptUrl = data.data.script_url;
                    const clientKey = data.data.client_key;

                    console.log('🎫 Snap Token:', snapToken);

                    // Inject snap script if not loaded
                    if (!document.querySelector('script[data-midtrans-snap]')) {
                        console.log('📥 Injecting Midtrans Snap script...');
                        const s = document.createElement('script');
                        s.setAttribute('src', scriptUrl);
                        s.setAttribute('data-client-key', clientKey);
                        s.setAttribute('data-midtrans-snap', '1');
                        document.head.appendChild(s);
                        s.onload = () => {
                            console.log('✅ Snap script loaded, opening popup...');
                            openSnap(snapToken);
                        };
                        s.onerror = () => {
                            console.error('❌ Failed to load Snap script');
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal memuat Midtrans SDK',
                                confirmButtonColor: '#667eea'
                            });
                        };
                    } else {
                        console.log('✅ Snap script already loaded, opening popup...');
                        openSnap(snapToken);
                    }

                    function openSnap(token) {
                        if (!window.snap) {
                            console.error('❌ window.snap is not available');
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Midtrans SDK belum siap',
                                text: 'Silakan coba lagi',
                                confirmButtonColor: '#667eea'
                            });
                            return;
                        }

                        console.log('🚀 Calling snap.pay()...');
                        Swal.close(); // Close the loading dialog
                        
                        window.snap.pay(token, {
                            onSuccess: function(result) {
                                console.log('✅ Payment Success:', result);
                                // Redirect to order page; backend webhook will update status
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Berhasil!',
                                    text: 'Pesanan Anda sedang diproses',
                                    confirmButtonColor: '#667eea',
                                    allowOutsideClick: false,
                                    willClose: () => {
                                        window.location.href = orderShowUrl + '?payment=success';
                                    }
                                });
                            },
                            onPending: function(result) {
                                console.log('⏳ Payment Pending:', result);
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Tertunda',
                                    text: 'Pembayaran Anda sedang diverifikasi',
                                    confirmButtonColor: '#667eea',
                                    allowOutsideClick: false,
                                    willClose: () => {
                                        window.location.href = orderShowUrl + '?payment=pending';
                                    }
                                });
                            },
                            onError: function(result) {
                                console.error('❌ Payment Error:', result);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: 'Terjadi kesalahan saat memproses pembayaran.',
                                    confirmButtonColor: '#667eea'
                                });
                            },
                            onClose: function() {
                                console.log('🚪 Payment popup closed by user');
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Dibatalkan',
                                    text: 'Anda menutup jendela pembayaran. Silakan coba lagi.',
                                    confirmButtonColor: '#667eea'
                                });
                            }
                        });
                    }

                }).catch(err => {
                    console.error('❌ Fetch Error:', err);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.message || 'Tidak dapat membuat transaksi. Pastikan Anda sudah login.',
                        confirmButtonColor: '#667eea'
                    });
                    
                    // Re-enable submit button
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML =
                            '<i class="bi bi-credit-card-fill me-2" aria-hidden="true"></i>Lanjutkan ke Pembayaran';
                    }
                });
            }

        })();
    </script>
@endpush
