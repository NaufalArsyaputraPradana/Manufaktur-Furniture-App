@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_number)

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section class="hero-section position-relative text-white" aria-label="Invoice hero">
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
                    <li class="breadcrumb-item active text-white" aria-current="page">Invoice</li>
                </ol>
            </nav>

            <div class="text-center">
                <h1 class="display-5 fw-bold mb-3 text-white">
                    <i class="bi bi-file-earmark-pdf me-3 text-warning" aria-hidden="true"></i>Invoice Pesanan
                </h1>
                <p class="lead mb-0 text-white opacity-90">
                    {{ $order->order_number }} · Pratinjau—nota yang sama digunakan untuk PDF dan cetak.
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
    <section class="invoice-section py-5 bg-light" aria-label="Invoice">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div></div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('customer.orders.invoice.download', $order) }}" class="btn btn-primary rounded-pill hover-lift fw-bold shadow-sm">
                        <i class="bi bi-download me-1"></i>Unduh PDF
                    </a>
                    <button type="button" class="btn btn-outline-secondary rounded-pill hover-lift fw-bold" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light border rounded-pill hover-lift fw-bold">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 print-area invoice-doc animate-on-scroll">
                <div class="card-body p-4 p-md-5">
                    @include('customer.invoices.partials.content')
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* ============================================
           HERO SECTION
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
           INVOICE SECTION
           ============================================ */
        .invoice-section {
            min-height: 60vh;
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

        /* ============================================
           INVOICE DOCUMENT STYLES
           ============================================ */
        .invoice-doc .hdr {
            border-bottom: 2px solid #4e73df;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .invoice-doc .muted {
            color: #666;
            font-size: 0.9rem;
        }

        .invoice-doc table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .invoice-doc table.items th,
        .invoice-doc table.items td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }

        .invoice-doc table.items th {
            background: #f8f9fc;
            font-weight: 600;
        }

        .invoice-doc .right {
            text-align: right;
        }

        .invoice-doc .total {
            font-size: 1.15rem;
            font-weight: 700;
            margin-top: 12px;
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

            .invoice-doc .p-md-5 {
                padding: 1.5rem !important;
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

            .invoice-doc table.items th,
            .invoice-doc table.items td {
                padding: 6px;
                font-size: 0.85rem;
            }
        }

        @media print {
            .navbar, footer, .whatsapp-float, .back-to-top-btn, .btn, .hero-section, .wave-bottom {
                display: none !important;
            }

            .print-area {
                box-shadow: none !important;
            }

            body {
                background: white;
            }

            .invoice-section {
                background: white;
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

        })();
    </script>
@endpush
