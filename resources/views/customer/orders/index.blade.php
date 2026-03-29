@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-orders position-relative text-white" aria-label="Pesanan saya hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index: 2;">
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0 p-0 bg-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Pesanan Saya</li>
                </ol>
            </nav>

            <div class="text-center fade-in">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-box-seam-fill me-2" aria-hidden="true"></i>Pesanan Saya
                </h1>
                <p class="lead mb-0 opacity-90">
                    @if ($orders->count() > 0)
                        Anda memiliki <strong>{{ $orders->total() }} pesanan</strong> aktif/riwayat
                    @else
                        Kelola dan lacak semua pesanan furniture Anda
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

    {{-- ===== ORDERS SECTION ===== --}}
    <section class="orders-section bg-light" aria-label="Daftar pesanan">
        <div class="container">

            @if ($orders->isNotEmpty())
                <div class="row g-4">
                    @foreach ($orders as $order)

                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 order-card animate-on-scroll">

                                {{-- Header --}}
                                <div
                                    class="card-header bg-white border-bottom border-light d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 rounded-top-4">
                                    <div>
                                        <h2 class="h5 mb-1 fw-bold text-dark">
                                            <i class="bi bi-receipt-cutoff me-2 text-primary" aria-hidden="true"></i>
                                            #{{ $order->order_number }}
                                        </h2>
                                        <small class="text-muted fw-medium">
                                            <i class="bi bi-calendar-event-fill me-1" aria-hidden="true"></i>
                                            {{ $order->created_at->format('d M Y, H:i') }} WIB
                                        </small>
                                    </div>
                                    <x-order-status-badge :status="$order->status" :payment="$order->payment" size="lg" />
                                </div>

                                {{-- Body --}}
                                <div class="card-body p-4">
                                    <div class="row g-4">

                                        {{-- Items --}}
                                        <div class="col-md-8 border-end border-light">
                                            <h6 class="text-muted mb-3 fw-bold text-uppercase small">
                                                <i class="bi bi-cart3 me-2" aria-hidden="true"></i>Daftar Produk:
                                            </h6>

                                            @forelse ($order->orderDetails as $detail)
                                                <x-order-item-card :detail="$detail" />
                                            @empty
                                                <div class="alert alert-warning rounded-3 border-0" role="alert">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"
                                                        aria-hidden="true"></i>Tidak ada detail item pesanan
                                                </div>
                                            @endforelse

                                            @if ($order->customer_notes)
                                                <div
                                                    class="p-3 bg-warning bg-opacity-10 rounded-3 border-start border-warning border-4 mt-3">
                                                    <small class="text-dark">
                                                        <i class="bi bi-chat-left-text-fill me-2 text-warning"
                                                            aria-hidden="true"></i>
                                                        <strong>Catatan Pesanan:</strong> {{ $order->customer_notes }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Summary & Actions --}}
                                        <div class="col-md-4">
                                            <div
                                                class="bg-light border border-light p-4 rounded-4 mb-3 h-100 d-flex flex-column">
                                                <h6 class="mb-3 fw-bold text-uppercase small text-muted">
                                                    <i class="bi bi-receipt me-2" aria-hidden="true"></i>Ringkasan
                                                </h6>

                                                <div class="d-flex justify-content-between mb-2 small">
                                                    <span class="text-muted">Subtotal Produk:</span>
                                                    <strong class="text-dark price-convert"
                                                        data-price="{{ $order->subtotal }}" data-currency="IDR">
                                                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                                                    </strong>
                                                </div>
                                                <div
                                                    class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white small">
                                                    <span class="text-muted">Ongkos Kirim:</span>
                                                    <strong class="text-success fst-italic">Sesuai Konfirmasi
                                                        Admin</strong>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <strong class="fs-6">Total Akhir:</strong>
                                                    <strong class="text-primary fs-4 price-convert"
                                                        data-price="{{ $order->total }}" data-currency="IDR">
                                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                                    </strong>
                                                </div>

                                                @if ($order->expected_completion_date)
                                                    <div class="alert alert-info bg-opacity-10 border-info mb-4 py-2 px-3 small rounded-3"
                                                        role="alert">
                                                        <i class="bi bi-calendar-check-fill text-info me-2"
                                                            aria-hidden="true"></i>
                                                        <strong>Estimasi Selesai:</strong><br>
                                                        <span
                                                            class="ms-4">{{ $order->expected_completion_date->format('d M Y') }}</span>
                                                    </div>
                                                @endif

                                                <div class="mt-auto d-grid gap-2">
                                                    <a href="{{ route('customer.orders.show', $order) }}"
                                                        class="btn btn-primary fw-bold hover-lift shadow-sm">
                                                        <i class="bi bi-eye-fill me-2" aria-hidden="true"></i>Lihat Detail
                                                        Penuh
                                                    </a>

                                                    @if ($order->status === 'pending' && !$isPaid)
                                                        <a href="{{ route('customer.orders.payment', $order) }}"
                                                            class="btn btn-success fw-bold hover-lift shadow-sm">
                                                            <i class="bi bi-credit-card-fill me-2"
                                                                aria-hidden="true"></i>Bayar Sekarang
                                                        </a>
                                                    @endif

                                                    @if ($order->status === 'pending')
                                                        <button type="button"
                                                            class="btn btn-light text-danger border fw-medium btn-cancel-order hover-lift mt-2"
                                                            data-order-id="{{ $order->id }}"
                                                            data-order-number="{{ $order->order_number }}">
                                                            <i class="bi bi-x-circle-fill me-2"
                                                                aria-hidden="true"></i>Batalkan Pesanan
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-secondary d-block mb-4 opacity-50" style="font-size:7rem;"
                        aria-hidden="true"></i>
                    <h2 class="display-6 fw-bold mb-3 text-dark">Belum Ada Pesanan</h2>
                    <p class="text-muted lead mb-4 mx-auto" style="max-width:500px;">
                        Anda belum memiliki riwayat pesanan. Mulai berbelanja dan wujudkan furniture impian untuk rumah
                        Anda!
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('products.index') }}"
                            class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold hover-lift">
                            <i class="bi bi-shop me-2" aria-hidden="true"></i>Mulai Belanja
                        </a>
                        <a href="{{ route('home') }}"
                            class="btn btn-outline-secondary btn-lg px-4 rounded-pill hover-lift fw-medium">
                            <i class="bi bi-house-door me-2" aria-hidden="true"></i>Kembali ke Home
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* ============================================
                   HERO & GLOBAL
                ============================================ */
        .hero-orders {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            padding-top: 9rem;
            padding-bottom: 7rem;
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
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .wave-bottom .shape-fill {
            fill: #f8f9fa;
        }

        .orders-section {
            padding-top: 2rem;
            padding-bottom: 5rem;
            min-height: 60vh;
        }

        /* ============================================
                   CARDS & EFFECTS
                ============================================ */
        .order-card {
            transition: box-shadow 0.3s ease;
            border: 1px solid transparent;
        }

        .order-card:hover {
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.12) !important;
            border-color: rgba(102, 126, 234, 0.2);
        }

        .order-item-card {
            transition: all 0.3s ease;
            border-color: #f1f5f9 !important;
        }

        .order-item-card:hover {
            background-color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #e2e8f0 !important;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .hover-lift:active {
            transform: translateY(0);
            box-shadow: none !important;
        }

        .rounded-modern {
            border-radius: 1rem !important;
        }

        .rounded-top-4 {
            border-radius: 1rem 1rem 0 0 !important;
        }

        /* ANIMATIONS */
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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-orders {
                padding-top: 7rem;
                padding-bottom: 4rem;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .order-card .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .order-card .badge {
                align-self: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            document.addEventListener('DOMContentLoaded', function() {

                // SCROLL ANIMATIONS
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

                // CANCEL ORDER SWEETALERT & FORM SUBMIT
                document.querySelectorAll('.btn-cancel-order').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const orderId = this.dataset.orderId;
                        const orderNumber = this.dataset.orderNumber;
                        if (!orderId || !orderNumber) return;

                        Swal.fire({
                            title: 'Batalkan Pesanan?',
                            html: `Apakah Anda yakin ingin membatalkan pesanan <strong class="text-danger">#${orderNumber}</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dikembalikan.</small>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-x-circle-fill me-1"></i> Ya, Batalkan',
                            cancelButtonText: 'Tidak, Kembali',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'btn btn-danger me-2 shadow-sm',
                                cancelButton: 'btn btn-light border fw-medium'
                            },
                            buttonsStyling: false
                        }).then(function(result) {
                            if (!result.isConfirmed) return;

                            Swal.fire({
                                title: 'Membatalkan Pesanan...',
                                html: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-4'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            // Create and submit form dynamically using Laravel Named Route pattern
                            const form = document.createElement('form');
                            form.method = 'POST';
                            // Utilize named route replacement for absolute path safety
                            const baseUrl =
                                "{{ route('customer.orders.cancel', ':id') }}";
                            form.action = baseUrl.replace(':id', orderId);

                            form.innerHTML = `
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PATCH">
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        });
                    });
                });
            });
        })();
    </script>
@endpush
