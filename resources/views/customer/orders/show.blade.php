@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')

    {{-- Pre-compute reusable values --}}
    @php
        $calculatedTotal = $order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
        $paySt = $order->payment?->payment_status;
        $isPaid = $paySt === \App\Models\Payment::STATUS_PAID;
        $isDpPaid = $paySt === \App\Models\Payment::STATUS_DP_PAID;
        $isFullPending = $paySt === \App\Models\Payment::STATUS_FULL_PENDING;

        $stageOrder = ['cutting', 'assembly', 'sanding', 'finishing', 'quality_control'];
        $stageLabels = [
            'cutting' => 'Pemotongan',
            'assembly' => 'Perakitan',
            'sanding' => 'Penghalusan',
            'finishing' => 'Finishing',
            'quality_control' => 'QC',
        ];
        $stageIcons = [
            'cutting' => 'bi-scissors',
            'assembly' => 'bi-tools',
            'sanding' => 'bi-hurricane',
            'finishing' => 'bi-brush',
            'quality_control' => 'bi-shield-check',
        ];

        $hasProcesses = $order->productionProcesses && $order->productionProcesses->count() > 0;
        $avgProgress = $hasProcesses ? round($order->productionProcesses->avg('progress_percentage')) : 0;

        // Check if payment proof exists (waiting for admin verification)
        // Kondisi: pembayaran status = pending DAN ada bukti pembayaran yang terupload
        $hasPendingPaymentProof = $order->payment && 
                                  $order->payment->payment_status === \App\Models\Payment::STATUS_PENDING && 
                                  !empty($order->payment->payment_proof);

        $statusBadge = match (true) {
            $order->status === 'pending' && $isPaid => ['bg-info', 'bi-cash-coin', 'Sudah Bayar'],
            $order->status === 'pending' && $isFullPending => ['bg-warning text-dark', 'bi-hourglass-split', 'Tunggu Konfirmasi'],
            $order->status === 'pending' && $isDpPaid => ['bg-info text-dark', 'bi-piggy-bank', 'DP diverifikasi'],
            $order->status === 'pending' && $hasPendingPaymentProof => ['bg-warning text-dark', 'bi-hourglass-split', 'Tunggu Konfirmasi'],
            $order->status === 'pending' => ['bg-warning text-dark', 'bi-clock-history', 'Menunggu Pembayaran'],
            $order->status === 'confirmed' => ['bg-info', 'bi-check-circle', 'Dikonfirmasi'],
            $order->status === 'in_production' => ['bg-light text-dark', 'bi-gear', 'Dalam Produksi'],
            $order->status === 'completed' => ['bg-success', 'bi-check-all', 'Selesai'],
            $order->status === 'cancelled' => ['bg-danger', 'bi-x-circle', 'Dibatalkan'],
            default => ['bg-secondary', 'bi-circle', ucfirst($order->status)],
        };
    @endphp

    {{-- ════════════════════════════════════════
     HERO
═════════════════════════════════════════════ --}}
    <section class="hero-orders position-relative text-white" aria-label="Detail pesanan hero">
        <div class="hero-pattern" aria-hidden="true"></div>

        <div class="container position-relative" style="z-index:2;">
            <nav aria-label="Breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-house-door-fill me-1" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.orders.index') }}"
                            class="text-white text-decoration-none hover-opacity">
                            <i class="bi bi-box-seam-fill me-1" aria-hidden="true"></i>Pesanan Saya
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $order->order_number }}</li>
                </ol>
            </nav>

            <div class="text-center">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-receipt-cutoff me-2" aria-hidden="true"></i>Detail Pesanan
                </h1>
                <p class="lead mb-0">Informasi lengkap pesanan <strong>{{ $order->order_number }}</strong></p>
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

    {{-- ════════════════════════════════════════
     MAIN CONTENT
═════════════════════════════════════════════ --}}
    <section class="order-detail-section bg-light" aria-label="Detail pesanan">
        <div class="container">

            <div class="mb-4">
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary hover-lift">
                    <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Kembali ke Daftar Pesanan
                </a>
            </div>

            {{-- Session Alerts --}}
            @foreach (['success' => 'alert-success bi-check-circle-fill', 'error' => 'alert-danger bi-exclamation-circle-fill', 'info' => 'alert-info bi-info-circle-fill'] as $key => $cls)
                @if (session($key))
                    @php [$alertClass, $icon] = explode(' ', $cls, 2); @endphp
                    <div class="alert {{ $alertClass }} alert-dismissible fade show" role="alert">
                        <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>{{ session($key) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endforeach

            {{-- ════════════════════════════════════════
             ORDER HEADER CARD
        ═════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 p-4 rounded-top-4"
                    style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-1 text-white fw-bold">
                                <i class="bi bi-receipt me-2" aria-hidden="true"></i>{{ $order->order_number }}
                            </h4>
                            <small class="text-white opacity-75">
                                <i class="bi bi-calendar-event me-1"
                                    aria-hidden="true"></i>{{ $order->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <span class="badge {{ $statusBadge[0] }} fs-6 px-4 py-2">
                                <i class="bi {{ $statusBadge[1] }} me-1" aria-hidden="true"></i>{{ $statusBadge[2] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Customer Info --}}
                        <div class="col-lg-4">
                            <div class="info-box">
                                <h6 class="info-box-title">
                                    <i class="bi bi-person-circle me-2" aria-hidden="true"></i>Informasi Pelanggan
                                </h6>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Nama</small>
                                    <strong>{{ $order->user?->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Email</small>
                                    <strong>{{ $order->user?->email ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Telepon</small>
                                    <strong>{{ $order->phone ?? $order->user?->phone ?? 'N/A' }}</strong>
                                </div>
                                @if ($order->user?->address)
                                    <div>
                                        <small class="text-muted d-block">Alamat Profil</small>
                                        <strong>{{ $order->user->address }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="col-lg-4">
                            <div class="info-box">
                                <h6 class="info-box-title">
                                    <i class="bi bi-truck me-2" aria-hidden="true"></i>Alamat Pengiriman
                                </h6>
                                @if ($order->shipping_address)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Tujuan Pengiriman</small>
                                        <p class="mb-0">{{ nl2br(e($order->shipping_address)) }}</p>
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">-</p>
                                @endif
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="col-lg-4">
                            <div class="info-box">
                                <h6 class="info-box-title">
                                    <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>Timeline
                                </h6>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Tanggal Order</small>
                                    <strong>{{ $order->created_at->format('d M Y, H:i') }}</strong>
                                </div>
                                @if ($order->expected_completion_date)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Estimasi Selesai</small>
                                        <strong
                                            class="text-primary">{{ $order->expected_completion_date->format('d M Y') }}</strong>
                                    </div>
                                @endif
                                @if ($order->actual_completion_date)
                                    <div>
                                        <small class="text-muted d-block">Selesai Pada</small>
                                        <strong
                                            class="text-success">{{ $order->actual_completion_date->format('d M Y') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($order->customer_notes)
                        <hr>
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-chat-left-text me-2" aria-hidden="true"></i>Catatan
                                Anda:</h6>
                            <p class="mb-0">{{ $order->customer_notes }}</p>
                        </div>
                    @endif

                    @if ($order->admin_notes)
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2" aria-hidden="true"></i>Catatan
                                Admin:</h6>
                            <p class="mb-0">{{ $order->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ORDER ITEMS CARD
        ═════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 p-4 rounded-top-4 d-flex justify-content-between align-items-center"
                    style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                    <h5 class="mb-0 text-white fw-bold">
                        <i class="bi bi-cart3 me-2" aria-hidden="true"></i>Item Pesanan
                    </h5>
                    <span class="badge bg-white text-primary fs-6 px-3 py-2">{{ $order->orderDetails->count() }}
                        Item</span>
                </div>

                <div class="card-body p-4">
                    @forelse ($order->orderDetails as $detail)
                        @php
                            $detailSpecs = null;
                            $customImagePath = null;

                            if ($detail->is_custom && $detail->custom_specifications) {
                                $detailSpecs = is_string($detail->custom_specifications)
                                    ? json_decode($detail->custom_specifications, true)
                                    : $detail->custom_specifications;
                                $customImagePath = $detailSpecs['design_image'] ?? null;
                            }

                            $productImagePath = null;
                            if (!$customImagePath && $detail->product?->images) {
                                $imgs = is_string($detail->product->images)
                                    ? json_decode($detail->product->images, true) ?? []
                                    : $detail->product->images;
                                $first = is_array($imgs) ? $imgs[0] ?? null : $imgs->first();
                                if ($first) {
                                    $productImagePath = is_object($first)
                                        ? $first->image_path ?? null
                                        : (is_array($first)
                                            ? $first['image_path'] ?? null
                                            : (is_string($first)
                                                ? $first
                                                : null));
                                }
                            }

                            $itemSubtotal = $detail->unit_price * $detail->quantity;
                        @endphp

                        <div class="card border-0 shadow-sm rounded-4 mb-4 order-item-card">
                            <div class="card-body p-4">
                                <div class="row g-4">

                                    {{-- Product Image --}}
                                    <div class="col-md-auto">
                                        @if ($customImagePath)
                                            <div class="product-image-container" data-bs-toggle="modal"
                                                data-bs-target="#imageModal{{ $detail->id }}">
                                                <img src="{{ asset('storage/' . $customImagePath) }}"
                                                    alt="Custom Design - {{ $detail->product_name }}"
                                                    class="product-thumbnail" loading="lazy"
                                                    onerror="this.parentElement.classList.add('img-error')">
                                                <span
                                                    class="position-absolute top-0 start-0 badge bg-warning text-dark m-2"
                                                    style="font-size:.7rem;z-index:2;">
                                                    <i class="bi bi-pencil-square me-1"></i>Custom
                                                </span>
                                                <div class="product-image-overlay">
                                                    <i class="bi bi-zoom-in fs-1 text-white mb-2" aria-hidden="true"></i>
                                                    <p class="text-white small mb-0 fw-bold">Klik untuk Memperbesar</p>
                                                </div>
                                            </div>
                                        @elseif ($productImagePath)
                                            <div class="product-image-container" data-bs-toggle="modal"
                                                data-bs-target="#productImageModal{{ $detail->id }}"
                                                style="cursor:pointer;">
                                                <img src="{{ asset('storage/' . $productImagePath) }}"
                                                    alt="{{ $detail->product?->name }}" class="product-thumbnail"
                                                    loading="lazy"
                                                    onerror="this.parentElement.classList.add('img-error')">
                                                <div class="product-image-overlay">
                                                    <i class="bi bi-zoom-in fs-1 text-white mb-2" aria-hidden="true"></i>
                                                    <p class="text-white small mb-0 fw-bold">Klik untuk Memperbesar</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="product-image-placeholder">
                                                <i class="bi bi-image fs-1 text-muted d-block mb-2"
                                                    aria-hidden="true"></i>
                                                <small class="text-muted">No Image</small>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Product Details --}}
                                    <div class="col-md">
                                        <div class="row h-100">
                                            <div class="col-lg-8 mb-3 mb-lg-0">
                                                <h5 class="mb-2 fw-bold text-primary">
                                                    {{ $detail->product?->name ?? $detail->product_name }}
                                                </h5>

                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                    @if ($detail->is_custom)
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-pencil-square me-1"
                                                                aria-hidden="true"></i>Custom Order
                                                        </span>
                                                    @endif
                                                    @if ($detail->product?->category)
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="bi bi-tag me-1"
                                                                aria-hidden="true"></i>{{ $detail->product->category->name }}
                                                        </span>
                                                    @endif
                                                    @if ($detail->product?->sku)
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="bi bi-upc me-1" aria-hidden="true"></i>SKU:
                                                            {{ $detail->product->sku }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($detail->product?->description)
                                                    <p class="text-muted small mb-3">
                                                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                                        {{ Str::limit($detail->product->description, 150) }}
                                                    </p>
                                                @endif

                                                {{-- Custom Specs --}}
                                                @if ($detail->is_custom && $detailSpecs)
                                                    <div class="card border-warning bg-warning bg-opacity-10 mb-3">
                                                        <div class="card-body p-3">
                                                            <h6 class="text-warning mb-3 fw-bold">
                                                                <i class="bi bi-rulers me-2"
                                                                    aria-hidden="true"></i>Spesifikasi Custom
                                                            </h6>
                                                            <div class="row g-2">
                                                                @if (!empty($detailSpecs['dimensions']))
                                                                    <div class="col-md-6">
                                                                        <div class="d-flex">
                                                                            <i
                                                                                class="bi bi-rulers text-warning me-2 mt-1"></i>
                                                                            <div>
                                                                                <small
                                                                                    class="text-muted d-block">Dimensi</small>
                                                                                <strong>{{ $detailSpecs['dimensions'] }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if (!empty($detailSpecs['material_type']))
                                                                    <div class="col-md-6">
                                                                        <div class="d-flex">
                                                                            <i
                                                                                class="bi bi-box text-warning me-2 mt-1"></i>
                                                                            <div>
                                                                                <small
                                                                                    class="text-muted d-block">Material</small>
                                                                                <strong>{{ $detailSpecs['material_type'] }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if (!empty($detailSpecs['color_finishing']))
                                                                    <div class="col-md-6">
                                                                        <div class="d-flex">
                                                                            <i
                                                                                class="bi bi-palette text-warning me-2 mt-1"></i>
                                                                            <div>
                                                                                <small
                                                                                    class="text-muted d-block">Finishing</small>
                                                                                <strong>{{ $detailSpecs['color_finishing'] }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Notes (skip JSON technical data) --}}
                                                @if ($detail->notes)
                                                    @php
                                                        $notesData = json_decode($detail->notes, true);
                                                        $isTechnical =
                                                            is_array($notesData) &&
                                                            array_intersect_key(
                                                                $notesData,
                                                                array_flip(['grade', 'kubikasi', 'harga_kayu']),
                                                            );
                                                    @endphp
                                                    @if (!$isTechnical)
                                                        <div class="alert alert-info py-2 px-3 mb-0 small">
                                                            <i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>
                                                            <strong>Catatan:</strong> {{ $detail->notes }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Pricing --}}
                                            <div class="col-lg-4">
                                                <div class="card border-0 bg-light h-100">
                                                    <div class="card-body p-3 text-center">
                                                        <small class="text-muted d-block mb-1">Harga Satuan</small>
                                                        @if ($detail->is_custom && $detail->unit_price == 0)
                                                            <h5 class="text-warning mb-0 fw-bold">
                                                                <i class="bi bi-hourglass-split me-1"
                                                                    aria-hidden="true"></i>Menunggu Penawaran
                                                            </h5>
                                                        @else
                                                            <h4 class="text-dark mb-0 fw-bold price-convert"
                                                                data-price="{{ $detail->unit_price }}"
                                                                data-currency="IDR">
                                                                Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                                            </h4>
                                                        @endif

                                                        <i class="bi bi-x text-muted fs-5 my-2 d-block"
                                                            aria-hidden="true"></i>

                                                        <small class="text-muted d-block mb-1">Jumlah</small>
                                                        <span class="badge bg-primary fs-5 px-4 py-2">
                                                            <i class="bi bi-box me-1"
                                                                aria-hidden="true"></i>{{ $detail->quantity }} Unit
                                                        </span>

                                                        <hr class="my-3">

                                                        <small class="text-muted d-block mb-1">Subtotal</small>
                                                        <h3 class="text-success mb-0 fw-bold">
                                                            <span class="price-convert" data-price="{{ $itemSubtotal }}"
                                                                data-currency="IDR">
                                                                Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                                            </span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Tidak ada detail pesanan</p>
                    @endforelse

                    {{-- Order Total --}}
                    @if ($order->orderDetails->count() > 0)
                        <div class="p-4 rounded-4 order-total-bar d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-muted">
                                <i class="bi bi-receipt me-2" aria-hidden="true"></i>Total Pesanan
                            </h6>
                            <h4 class="mb-0 text-primary fw-bold">
                                <span class="price-convert" data-price="{{ $calculatedTotal }}" data-currency="IDR">
                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                </span>
                            </h4>
                        </div>
                    @endif

                    @if ($order->shipping_status || $order->courier || $order->tracking_number)
                        @php
                            $custTrackUrl = app(\App\Support\CourierTrackingService::class)->publicTrackingUrl($order->courier, $order->tracking_number);
                        @endphp
                        <div class="p-4 rounded-4 border bg-white shadow-sm mt-4">
                            <h6 class="fw-bold mb-3 text-primary">
                                <i class="bi bi-truck me-2" aria-hidden="true"></i>Status pengiriman
                            </h6>
                            <div class="row g-3 small">
                                @if ($order->shipping_status)
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">Status</span>
                                        <strong>{{ $order->shipping_status_label }}</strong>
                                    </div>
                                @endif
                                @if ($order->courier)
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">Ekspedisi</span>
                                        <strong>{{ $order->courier }}</strong>
                                    </div>
                                @endif
                                @if ($order->tracking_number)
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">No. resi</span>
                                        <strong class="font-monospace">{{ $order->tracking_number }}</strong>
                                    </div>
                                @endif
                            </div>
                            @if ($custTrackUrl)
                                <a href="{{ $custTrackUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill mt-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Lacak paket
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Image Modals --}}
                    @foreach ($order->orderDetails as $detail)
                        @php
                            $modalCustomImg = null;
                            $modalProductImg = null;

                            if ($detail->is_custom && $detail->custom_specifications) {
                                $ms = is_string($detail->custom_specifications)
                                    ? json_decode($detail->custom_specifications, true)
                                    : $detail->custom_specifications;
                                if (!empty($ms['design_image'])) {
                                    $modalCustomImg = asset('storage/' . $ms['design_image']);
                                }
                            }

                            if (!$modalCustomImg && $detail->product?->images) {
                                $imgs = is_string($detail->product->images)
                                    ? json_decode($detail->product->images, true) ?? []
                                    : $detail->product->images;
                                $first = is_array($imgs) ? $imgs[0] ?? null : $imgs->first();
                                if ($first && !$detail->is_custom) {
                                    $p = is_object($first)
                                        ? $first->image_path ?? null
                                        : (is_array($first)
                                            ? $first['image_path'] ?? null
                                            : (is_string($first)
                                                ? $first
                                                : null));
                                    if ($p) {
                                        $modalProductImg = asset('storage/' . $p);
                                    }
                                }
                            }
                        @endphp

                        @if ($modalCustomImg)
                            <div class="modal fade" id="imageModal{{ $detail->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header border-0 text-white"
                                            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                            <h5 class="modal-title text-white">
                                                <i class="bi bi-image me-2" aria-hidden="true"></i>Custom Design -
                                                {{ $detail->product_name }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark">
                                            <img src="{{ $modalCustomImg }}" alt="Custom Design" class="img-fluid w-100"
                                                style="max-height:80vh;object-fit:contain;">
                                        </div>
                                        <div class="modal-footer bg-light d-flex justify-content-between">
                                            <small class="text-muted">
                                                <span class="badge bg-warning text-dark me-2"><i
                                                        class="bi bi-pencil-square"></i> Custom Order</span>
                                                <strong>{{ $detail->product_name }}</strong>
                                            </small>
                                            <div>
                                                <a href="{{ $modalCustomImg }}" download
                                                    class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg me-1"></i>Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($modalProductImg)
                            <div class="modal fade" id="productImageModal{{ $detail->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header border-0 text-white"
                                            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                            <h5 class="modal-title text-white">
                                                <i class="bi bi-image me-2"
                                                    aria-hidden="true"></i>{{ $detail->product?->name }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark">
                                            <img src="{{ $modalProductImg }}" alt="{{ $detail->product?->name }}"
                                                class="img-fluid w-100" style="max-height:80vh;object-fit:contain;">
                                        </div>
                                        <div class="modal-footer bg-light d-flex justify-content-between">
                                            <small
                                                class="text-muted"><strong>{{ $detail->product?->name }}</strong></small>
                                            <div>
                                                <a href="{{ $modalProductImg }}" download
                                                    class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg me-1"></i>Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ════════════════════════════════════════
             PAYMENT STATUS CARD
        ═════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 p-4 rounded-top-4"
                    style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                    <h5 class="mb-0 text-white fw-bold">
                        <i class="bi bi-credit-card me-2" aria-hidden="true"></i>Status Pembayaran
                    </h5>
                </div>

                <div class="card-body p-4">

                    {{-- PAYMENT STATUS FLOW: --}}
                    {{-- 1. FAILED - Pembayaran gagal --}}
                    {{-- 2. PENDING + PROOF - Menunggu konfirmasi (bukti ada) --}}
                    {{-- 3. DP_PAID - DP terverifikasi, tunggu pelunasan --}}
                    {{-- 4. FULL_PENDING - Pelunasan pending verifikasi --}}
                    {{-- 5. PAID - Lunas --}}
                    {{-- 6. PENDING - Belum bayar --}}
                    {{-- 7. CANCELLED order --}}

                    @if ($order->status === 'cancelled')
                        {{-- CANCELLED --}}
                        <div class="alert alert-secondary border-0 text-center py-4">
                            <i class="bi bi-x-circle text-secondary" style="font-size:4rem;" aria-hidden="true"></i>
                            <h5 class="mt-3 mb-1">Pesanan Dibatalkan</h5>
                            <p class="text-muted mb-0">Pesanan ini telah dibatalkan.</p>
                        </div>

                    @elseif ($order->payment?->payment_status === 'failed')
                        {{-- FAILED PAYMENT --}}
                        <div class="alert alert-danger border-0 shadow-sm">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-x-circle-fill fs-2 me-3 flex-shrink-0" aria-hidden="true"></i>
                                <div>
                                    <h5 class="alert-heading mb-2">Pembayaran Gagal</h5>
                                    <p class="mb-3">Terjadi kesalahan saat memproses pembayaran Anda.</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('customer.orders.payment', $order) }}" class="btn btn-danger">
                                            <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>Coba Lagi
                                        </a>
                                        <a href="https://wa.me/6285290505442?text=Halo,%20saya%20mengalami%20masalah%20pembayaran%20untuk%20pesanan%20{{ $order->order_number }}"
                                            target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger">
                                            <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Hubungi CS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($hasPendingPaymentProof)
                        {{-- PENDING PAYMENT WITH PROOF - WAITING FOR ADMIN VERIFICATION (DP or FULL) --}}
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="alert alert-warning border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white rounded-circle p-3 me-3 flex-shrink-0">
                                            <i class="bi bi-hourglass-split text-warning fs-2" aria-hidden="true"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 fw-bold">Menunggu Konfirmasi Pembayaran</h5>
                                            <p class="mb-0 small">Bukti pembayaran Anda telah diterima dan sedang dalam proses verifikasi. Tim kami akan memverifikasi dalam 1–2 hari kerja. Anda akan diberitahu setelah verifikasi selesai.</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->payment)
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body p-4">
                                            <h6 class="text-muted mb-3 fw-bold">
                                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Detail Pembayaran
                                            </h6>
                                            <div class="payment-detail-row">
                                                <span class="text-muted">ID Transaksi</span>
                                                <strong class="text-primary">{{ $order->payment->transaction_id ?? '-' }}</strong>
                                            </div>
                                            <div class="payment-detail-row">
                                                <span class="text-muted">Metode</span>
                                                <strong>
                                                    @if ($order->payment->payment_method === 'transfer')
                                                        <i class="bi bi-bank2 me-1" aria-hidden="true"></i>Transfer Bank
                                                    @elseif ($order->payment->payment_method === 'credit_card')
                                                        <i class="bi bi-credit-card me-1" aria-hidden="true"></i>Kartu Kredit
                                                    @elseif ($order->payment->payment_method === 'cash')
                                                        <i class="bi bi-cash me-1" aria-hidden="true"></i>Tunai
                                                    @else
                                                        {{ ucfirst($order->payment->payment_method) }}
                                                    @endif
                                                </strong>
                                            </div>
                                            <div class="payment-detail-row border-bottom">
                                                <span class="text-muted">Total Pesanan</span>
                                                <strong class="text-dark">
                                                    <span class="price-convert" data-price="{{ $calculatedTotal }}" data-currency="IDR">
                                                        Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                                    </span>
                                                </strong>
                                            </div>
                                            @if ($order->payment->payment_status === \App\Models\Payment::STATUS_FULL_PENDING)
                                                {{-- Full pending (DP already verified, now awaiting full payment verification) --}}
                                                @php
                                                    $dpAmount = round($calculatedTotal * 50 / 100, 2);
                                                    $remainingAmount = $calculatedTotal - $dpAmount;
                                                @endphp
                                                <div class="payment-detail-row pt-3">
                                                    <span class="text-muted small">DP Terverifikasi (50%)</span>
                                                    <strong class="text-success small">
                                                        <span class="price-convert" data-price="{{ $dpAmount }}" data-currency="IDR">
                                                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                                        </span>
                                                    </strong>
                                                </div>
                                                <div class="payment-detail-row border-0 pb-0">
                                                    <span class="text-muted small">Pelunasan Menunggu Verifikasi (50%)</span>
                                                    <strong class="text-warning small">
                                                        <span class="price-convert" data-price="{{ $remainingAmount }}" data-currency="IDR">
                                                            Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                                                        </span>
                                                    </strong>
                                                </div>
                                            @else
                                                {{-- DP pending verification --}}
                                                <div class="payment-detail-row border-0 pb-0 pt-2">
                                                    <span class="text-muted">Total DP (50%)</span>
                                                    <h5 class="mb-0 text-warning fw-bold">
                                                        <span class="price-convert" data-price="{{ round($calculatedTotal * 50 / 100, 2) }}" data-currency="IDR">
                                                            Rp {{ number_format(round($calculatedTotal * 50 / 100, 2), 0, ',', '.') }}
                                                        </span>
                                                    </h5>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-5">
                                <div class="card border-warning h-100">
                                    <div class="card-body p-4">
                                        <div class="text-center mb-4">
                                            <div style="width: 70px; height: 70px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 0 0 10px rgba(245, 158, 11, 0.1);">
                                                <i class="bi bi-hourglass-split text-warning" style="font-size:2rem;" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <h6 class="text-muted mb-3 fw-bold text-center">
                                            <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Transfer
                                        </h6>
                                        @php
                                            $pendingProofPath = null;
                                            if ($order->payment?->payment_proof) {
                                                $proof = $order->payment->payment_proof;
                                                if (str_starts_with($proof, 'storage/')) {
                                                    $pendingProofPath = asset($proof);
                                                } elseif (str_starts_with($proof, '/')) {
                                                    $pendingProofPath = asset('storage' . $proof);
                                                } else {
                                                    $pendingProofPath = asset('storage/' . $proof);
                                                }
                                            }
                                        @endphp
                                        @if ($pendingProofPath)
                                            <div class="payment-proof-wrapper" data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                <img src="{{ $pendingProofPath }}" alt="Bukti Pembayaran"
                                                    class="img-fluid rounded-3 shadow-sm w-100"
                                                    style="max-height:280px;object-fit:contain;">
                                                <div class="payment-proof-overlay">
                                                    <i class="bi bi-zoom-in text-white fs-1" aria-hidden="true"></i>
                                                    <p class="text-white mt-2 small">Klik untuk perbesar</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-center d-flex gap-2 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                </button>
                                                <a href="{{ $pendingProofPath }}" download="Bukti_Pembayaran_{{ $order->order_number }}.jpg" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="bi bi-image text-muted" style="font-size:3rem;" aria-hidden="true"></i>
                                                <p class="text-muted mt-3 small">Bukti tidak tersedia</p>
                                            </div>
                                        @endif
                                        <hr class="my-3">
                                        <h5 class="text-center mb-2 fw-bold">Sedang Diverifikasi</h5>
                                        <p class="text-center text-muted small mb-0">Estimasi waktu verifikasi: 1-2 hari kerja</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($isPaid || (!$isDpPaid && in_array($order->status, ['confirmed', 'in_production', 'completed'])))
                        {{-- PAID atau alur produksi (bukan hanya DP) --}}
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="alert alert-success border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white rounded-circle p-3 me-3 flex-shrink-0">
                                            <i class="bi bi-check-circle-fill text-success fs-2" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold">{{ $isPaid ? 'Pembayaran Berhasil' : 'Pesanan aktif' }}</h5>
                                            <p class="mb-0 small">{{ $isPaid ? 'Pesanan Anda telah lunas dibayar' : 'Pesanan Anda sedang diproses setelah konfirmasi pembayaran.' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->payment)
                                    @php
                                        $dpAmount = round($calculatedTotal * 50 / 100, 2);
                                        $remainingAmount = $calculatedTotal - $dpAmount;
                                    @endphp
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body p-4">
                                            <h6 class="text-muted mb-3 fw-bold">
                                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Detail Pembayaran
                                            </h6>
                                            <div class="payment-detail-row">
                                                <span class="text-muted">ID Transaksi</span>
                                                <strong
                                                    class="text-primary">{{ $order->payment->transaction_id ?? '-' }}</strong>
                                            </div>
                                            <div class="payment-detail-row">
                                                <span class="text-muted">Metode</span>
                                                <strong>
                                                    @if ($order->payment->payment_method === 'transfer')
                                                        <i class="bi bi-bank2 me-1" aria-hidden="true"></i>Transfer Bank
                                                    @elseif ($order->payment->payment_method === 'credit_card')
                                                        <i class="bi bi-credit-card me-1" aria-hidden="true"></i>Kartu
                                                        Kredit
                                                    @elseif ($order->payment->payment_method === 'cash')
                                                        <i class="bi bi-cash me-1" aria-hidden="true"></i>Tunai
                                                    @else
                                                        {{ ucfirst($order->payment->payment_method) }}
                                                    @endif
                                                </strong>
                                            </div>
                                            <div class="payment-detail-row">
                                                <span class="text-muted">Tanggal</span>
                                                <strong>{{ $order->payment->payment_date?->format('d M Y, H:i') ?? '-' }}</strong>
                                            </div>
                                            <div class="payment-detail-row border-bottom">
                                                <span class="text-muted">Total Pesanan</span>
                                                <strong class="text-dark">
                                                    <span class="price-convert" data-price="{{ $calculatedTotal }}" data-currency="IDR">
                                                        Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                                    </span>
                                                </strong>
                                            </div>
                                            @if ($isPaid)
                                                <div class="payment-detail-row pt-3">
                                                    <span class="text-muted small">DP Dibayar (50%)</span>
                                                    <strong class="text-success small">
                                                        <span class="price-convert" data-price="{{ $dpAmount }}" data-currency="IDR">
                                                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                                        </span>
                                                    </strong>
                                                </div>
                                                <div class="payment-detail-row border-0 pb-0">
                                                    <span class="text-muted small">Pelunasan Dibayar (50%)</span>
                                                    <strong class="text-success small">
                                                        <span class="price-convert" data-price="{{ $remainingAmount }}" data-currency="IDR">
                                                            Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                                                        </span>
                                                    </strong>
                                                </div>
                                            @else
                                                <div class="payment-detail-row border-0 pb-0">
                                                    <span class="text-muted">Total Dibayar</span>
                                                    <h5 class="mb-0 text-success fw-bold">
                                                        <span class="price-convert"
                                                            data-price="{{ $order->payment->amount_paid ?? $order->payment->amount ?? $calculatedTotal }}"
                                                            data-currency="IDR">
                                                            Rp
                                                            {{ number_format($order->payment->amount_paid ?? $order->payment->amount ?? $calculatedTotal, 0, ',', '.') }}
                                                        </span>
                                                    </h5>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if ($isPaid || $isDpPaid)
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="bi bi-file-earmark-text me-1"></i>Invoice &amp; print
                                        </a>
                                        <a href="{{ route('customer.orders.invoice.download', $order) }}" class="btn btn-outline-success btn-sm rounded-pill">
                                            <i class="bi bi-download me-1"></i>Unduh PDF
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-5">
                                @if ($isPaid)
                                    {{-- Show both DP and Full Payment proofs for PAID status --}}
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body p-4">
                                            <h6 class="text-muted mb-4 fw-bold">
                                                <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Transfer (DP 50%)
                                            </h6>
                                            @php
                                                $dpProofPath = null;
                                                if ($order->payment?->payment_proof_dp) {
                                                    $proof = $order->payment->payment_proof_dp;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $dpProofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $dpProofPath = asset('storage' . $proof);
                                                    } else {
                                                        $dpProofPath = asset('storage/' . $proof);
                                                    }
                                                }
                                            @endphp
                                            @if ($dpProofPath)
                                                <div class="payment-proof-wrapper" data-bs-toggle="modal"
                                                    data-bs-target="#paymentProofModal">
                                                    <img src="{{ $dpProofPath }}"
                                                        alt="Bukti Pembayaran DP" class="img-fluid rounded-3 shadow-sm w-100"
                                                        style="max-height:280px;object-fit:contain;">
                                                    <div class="payment-proof-overlay">
                                                        <i class="bi bi-zoom-in text-white fs-1" aria-hidden="true"></i>
                                                        <p class="text-white mt-2 small">Klik untuk perbesar</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 text-center d-flex gap-1 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                    </button>
                                                    <a href="{{ $dpProofPath }}"
                                                        download="Bukti_DP_{{ $order->order_number }}.jpg"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1" aria-hidden="true"></i>DL
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-image text-muted" style="font-size:2.5rem;"
                                                        aria-hidden="true"></i>
                                                    <p class="text-muted mt-2 small">Bukti DP tidak tersedia</p>
                                                </div>
                                            @endif
                                            
                                            <hr class="my-4">
                                            
                                            <h6 class="text-muted mb-4 fw-bold">
                                                <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Transfer Pelunasan (50%)
                                            </h6>
                                            @php
                                                $fullProofPath = null;
                                                if ($order->payment?->payment_proof_full) {
                                                    $proof = $order->payment->payment_proof_full;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $fullProofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $fullProofPath = asset('storage' . $proof);
                                                    } else {
                                                        $fullProofPath = asset('storage/' . $proof);
                                                    }
                                                }
                                            @endphp
                                            @if ($fullProofPath)
                                                <div class="payment-proof-wrapper" data-bs-toggle="modal"
                                                    data-bs-target="#paymentProofModal">
                                                    <img src="{{ $fullProofPath }}"
                                                        alt="Bukti Pembayaran Pelunasan" class="img-fluid rounded-3 shadow-sm w-100"
                                                        style="max-height:280px;object-fit:contain;">
                                                    <div class="payment-proof-overlay">
                                                        <i class="bi bi-zoom-in text-white fs-1" aria-hidden="true"></i>
                                                        <p class="text-white mt-2 small">Klik untuk perbesar</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 text-center d-flex gap-1 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                    </button>
                                                    <a href="{{ $fullProofPath }}"
                                                        download="Bukti_Pelunasan_{{ $order->order_number }}.jpg"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1" aria-hidden="true"></i>DL
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-image text-muted" style="font-size:2.5rem;"
                                                        aria-hidden="true"></i>
                                                    <p class="text-muted mt-2 small">Bukti pelunasan tidak tersedia</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Show single proof for other statuses --}}
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body p-4">
                                            <h6 class="text-muted mb-3 fw-bold">
                                                <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Transfer
                                            </h6>
                                            @if ($order->payment?->payment_proof)
                                                @php
                                                    $proof = $order->payment->payment_proof;
                                                    $proofPath = null;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $proofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $proofPath = asset('storage' . $proof);
                                                    } else {
                                                        $proofPath = asset('storage/' . $proof);
                                                    }
                                                @endphp
                                                <div class="payment-proof-wrapper" data-bs-toggle="modal"
                                                    data-bs-target="#paymentProofModal">
                                                    <img src="{{ $proofPath }}"
                                                        alt="Bukti Pembayaran" class="img-fluid rounded-3 shadow-sm w-100"
                                                        style="max-height:350px;object-fit:contain;">
                                                    <div class="payment-proof-overlay">
                                                        <i class="bi bi-zoom-in text-white fs-1" aria-hidden="true"></i>
                                                        <p class="text-white mt-2 small">Klik untuk perbesar</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-center d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                    </button>
                                                    <a href="{{ $proofPath }}"
                                                        download="Bukti_Pembayaran_{{ $order->order_number }}.jpg"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="bi bi-image text-muted" style="font-size:4rem;"
                                                        aria-hidden="true"></i>
                                                    <p class="text-muted mt-3 mb-0">Tidak ada bukti transfer yang diupload</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif ($order->payment?->payment_status === 'failed')
                        {{-- FAILED --}}
                        <div class="alert alert-danger border-0 shadow-sm">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-x-circle-fill fs-2 me-3 flex-shrink-0" aria-hidden="true"></i>
                                <div>
                                    <h5 class="alert-heading mb-2">Pembayaran Gagal</h5>
                                    <p class="mb-3">Terjadi kesalahan saat memproses pembayaran Anda.</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('customer.orders.payment', $order) }}" class="btn btn-danger">
                                            <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>Coba Lagi
                                        </a>
                                        <a href="https://wa.me/6285290505442?text=Halo,%20saya%20mengalami%20masalah%20pembayaran%20untuk%20pesanan%20{{ $order->order_number }}"
                                            target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger">
                                            <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Hubungi CS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($isDpPaid && $order->remainingPayableAmount() > 0.001)
                        {{-- DP PAID - AWAITING REMAINING PAYMENT --}}
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white rounded-circle p-3 me-3 flex-shrink-0">
                                            <i class="bi bi-piggy-bank text-info fs-2" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold">DP telah diverifikasi</h5>
                                            <p class="mb-0 small">Pembayaran DP Anda telah diverifikasi. Selesaikan sisa pembayaran untuk melanjutkan produksi.</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->payment)
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body p-4">
                                            <h6 class="text-muted mb-3 fw-bold">
                                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Detail Pembayaran DP
                                            </h6>
                                            {{-- Total Pesanan --}}
                                            <div class="payment-detail-row">
                                                <span class="text-muted">Total Pesanan</span>
                                                <strong class="text-dark">
                                                    <span class="price-convert" data-price="{{ $calculatedTotal }}" data-currency="IDR">
                                                        Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                                    </span>
                                                </strong>
                                            </div>
                                            {{-- DP Breakdown --}}
                                            <div class="payment-detail-row">
                                                <span class="text-muted">DP yang Dibayar (50%)</span>
                                                <strong class="text-success">
                                                    @php
                                                        $dpAmount = round($calculatedTotal * 50 / 100, 2);
                                                        $remainingPayment = $calculatedTotal - $dpAmount;
                                                    @endphp
                                                    <span class="price-convert" data-price="{{ $dpAmount }}" data-currency="IDR">
                                                        Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                                    </span>
                                                </strong>
                                            </div>
                                            {{-- Sisa Pembayaran --}}
                                            <div class="payment-detail-row border-top pt-3">
                                                <span class="text-muted fw-bold">Sisa Pembayaran (50%)</span>
                                                <h5 class="mb-0 text-primary fw-bold">
                                                    <span class="price-convert" data-price="{{ $remainingPayment }}" data-currency="IDR">
                                                        Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                                    </span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <a href="{{ route('customer.orders.payment', $order) }}" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold">
                                    <i class="bi bi-upload me-2" aria-hidden="true"></i>Unggah Bukti Pelunasan
                                </a>
                            </div>

                            <div class="col-lg-5">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-4">
                                        <h6 class="text-muted mb-3 fw-bold">
                                            <i class="bi bi-image me-2" aria-hidden="true"></i>Bukti Transfer DP
                                        </h6>
                                        @php
                                            $proofPath = null;
                                            // Check BOTH fields: payment_proof_dp (verified) AND payment_proof (pending verification)
                                            // For DP_PAID: file will be in payment_proof_dp
                                            // For PENDING (DP upload): file will be in payment_proof
                                            if ($order->payment?->payment_proof_dp) {
                                                $proof = $order->payment->payment_proof_dp;
                                                if (str_starts_with($proof, 'storage/')) {
                                                    $proofPath = asset($proof);
                                                } elseif (str_starts_with($proof, '/')) {
                                                    $proofPath = asset('storage' . $proof);
                                                } else {
                                                    $proofPath = asset('storage/' . $proof);
                                                }
                                            } elseif ($order->payment?->payment_proof) {
                                                // If payment_proof_dp is empty, check payment_proof
                                                // This handles: PENDING DP uploads waiting for admin verification
                                                $proof = $order->payment->payment_proof;
                                                if (str_starts_with($proof, 'storage/')) {
                                                    $proofPath = asset($proof);
                                                } elseif (str_starts_with($proof, '/')) {
                                                    $proofPath = asset('storage' . $proof);
                                                } else {
                                                    $proofPath = asset('storage/' . $proof);
                                                }
                                            }
                                        @endphp
                                        @if ($proofPath)
                                            <div class="payment-proof-wrapper" data-bs-toggle="modal"
                                                data-bs-target="#paymentProofModal">
                                                <img src="{{ $proofPath }}"
                                                    alt="Bukti Pembayaran DP" class="img-fluid rounded-3 shadow-sm w-100"
                                                    style="max-height:350px;object-fit:contain;">
                                                <div class="payment-proof-overlay">
                                                    <i class="bi bi-zoom-in text-white fs-1" aria-hidden="true"></i>
                                                    <p class="text-white mt-2 small">Klik untuk perbesar</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-center d-flex gap-2 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                </button>
                                                <a href="{{ $proofPath }}"
                                                    download="Bukti_DP_{{ $order->order_number }}.jpg"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="bi bi-image text-muted" style="font-size:3rem;" aria-hidden="true"></i>
                                                <p class="text-muted mt-3 small">Bukti transfer DP belum tersedia</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($order->payment?->payment_status === \App\Models\Payment::STATUS_FULL_PENDING)
                        {{-- FULL PENDING - MENUNGGU KONFIRMASI PELUNASAN --}}
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="alert alert-warning border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white rounded-circle p-3 me-3 flex-shrink-0">
                                            <i class="bi bi-hourglass-split text-warning fs-2" aria-hidden="true"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 fw-bold">Menunggu Konfirmasi Pelunasan</h5>
                                            <p class="mb-0 small">Bukti pelunasan Anda telah diterima dan sedang dalam proses verifikasi. Tim kami akan memverifikasi dalam 1–2 hari kerja. Pesanan produksi akan dimulai setelah verifikasi admin selesai.</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->payment)
                                    @php
                                        $dpAmount = round($calculatedTotal * 50 / 100, 2);
                                        $remainingPayment = $calculatedTotal - $dpAmount;
                                    @endphp
                                    
                                    {{-- Payment Summary --}}
                                    <div class="card border-0 bg-light mb-4">
                                        <div class="card-header bg-white border-bottom">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bi bi-receipt me-2 text-primary" aria-hidden="true"></i>Ringkasan Pembayaran
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            {{-- Total Amount --}}
                                            <div class="payment-detail-row mb-3 pb-3 border-bottom">
                                                <span class="text-muted fw-bold">Total Pesanan</span>
                                                <h5 class="mb-0 text-dark fw-bold">
                                                    <span class="price-convert" data-price="{{ $calculatedTotal }}" data-currency="IDR">
                                                        Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                                    </span>
                                                </h5>
                                            </div>

                                            {{-- DP Section --}}
                                            <div class="row g-3 mb-3 pb-3 border-bottom">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="text-muted small">DP YANG DIBAYAR (50%)</span>
                                                            <h6 class="mb-0 fw-bold mt-1">
                                                                <i class="bi bi-check-circle text-success me-2" aria-hidden="true"></i>Sudah Diverifikasi
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <h5 class="mb-0 text-success fw-bold">
                                                                <span class="price-convert" data-price="{{ $dpAmount }}" data-currency="IDR">
                                                                    Rp {{ number_format($dpAmount, 0, ',', '.') }}
                                                                </span>
                                                            </h5>
                                                            <small class="text-muted">✓ Terverifikasi</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Remaining Payment Section --}}
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="text-muted small">PELUNASAN (50%)</span>
                                                            <h6 class="mb-0 fw-bold mt-1">
                                                                <i class="bi bi-hourglass-split text-warning me-2" aria-hidden="true"></i>Menunggu Verifikasi
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <h5 class="mb-0 text-warning fw-bold">
                                                                <span class="price-convert" data-price="{{ $remainingPayment }}" data-currency="IDR">
                                                                    Rp {{ number_format($remainingPayment, 0, ',', '.') }}
                                                                </span>
                                                            </h5>
                                                            <small class="text-warning">⏳ Verifikasi Admin</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Info & Status --}}
                                    <div class="alert alert-info border-0 rounded-3 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle me-2 mt-1 flex-shrink-0" aria-hidden="true"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 fw-bold">Status Verifikasi</h6>
                                                <p class="mb-2 small">
                                                    Bukti pelunasan Anda telah kami terima. Admin akan mengecek kelengkapan dan kesesuaian jumlah transfer dengan jadwal yang telah ditentukan.
                                                </p>
                                                <p class="mb-0 small text-muted">
                                                    <i class="bi bi-exclamation-circle me-1"></i> <strong>Jangan upload bukti lagi</strong> — Bukti sudah dalam sistem dan sedang diproses.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border-warning border-2 rounded-3 mb-0">
                                        <h6 class="mb-2 fw-bold text-warning">⏱️ Timeline Verifikasi</h6>
                                        <ul class="mb-0 small">
                                            <li class="mb-1"><strong>Hari 0:</strong> Bukti diterima (hari ini)</li>
                                            <li class="mb-1"><strong>Hari 1-2:</strong> Admin memverifikasi bukti</li>
                                            <li class="mb-1"><strong>Hari 2:</strong> Status berubah ke LUNAS</li>
                                            <li class="mb-0"><strong>Hari 3+:</strong> Produksi dimulai</li>
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-5">
                                {{-- Proof Images Section --}}
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-white border-bottom">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-image me-2 text-primary" aria-hidden="true"></i>Bukti Transfer
                                        </h6>
                                    </div>
                                    <div class="card-body p-4">
                                        {{-- DP Proof --}}
                                        <div class="mb-4 pb-3 border-bottom">
                                            <small class="text-muted d-block mb-2 fw-bold">BUKTI TRANSFER DP (50%)</small>
                                            @php
                                                $dpProofPath = null;
                                                if ($order->payment?->payment_proof_dp) {
                                                    $proof = $order->payment->payment_proof_dp;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $dpProofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $dpProofPath = asset('storage' . $proof);
                                                    } else {
                                                        $dpProofPath = asset('storage/' . $proof);
                                                    }
                                                }
                                            @endphp
                                            @if ($dpProofPath)
                                                <div class="payment-proof-wrapper mb-2" data-bs-toggle="modal" data-bs-target="#dpProofModal">
                                                    <img src="{{ $dpProofPath }}" alt="Bukti Transfer DP" 
                                                        class="img-fluid rounded-2 w-100" style="max-height:180px;object-fit:cover;">
                                                    <div class="payment-proof-overlay rounded-2">
                                                        <i class="bi bi-zoom-in text-white fs-4" aria-hidden="true"></i>
                                                        <p class="text-white mt-1 small">Lihat</p>
                                                    </div>
                                                </div>
                                                <small class="text-success"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Terverifikasi</small>
                                            @else
                                                <div class="text-center py-3 bg-white rounded-2 border-2 border-dashed">
                                                    <i class="bi bi-image text-muted" style="font-size:2rem;" aria-hidden="true"></i>
                                                    <p class="text-muted mt-2 small mb-0">Bukti DP</p>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Pelunasan Proof --}}
                                        <div>
                                            <small class="text-muted d-block mb-2 fw-bold">BUKTI TRANSFER PELUNASAN (50%)</small>
                                            @php
                                                $fullProofPath = null;
                                                if ($order->payment?->payment_proof_full) {
                                                    $proof = $order->payment->payment_proof_full;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $fullProofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $fullProofPath = asset('storage' . $proof);
                                                    } else {
                                                        $fullProofPath = asset('storage/' . $proof);
                                                    }
                                                } elseif ($order->payment?->payment_proof) {
                                                    $proof = $order->payment->payment_proof;
                                                    if (str_starts_with($proof, 'storage/')) {
                                                        $fullProofPath = asset($proof);
                                                    } elseif (str_starts_with($proof, '/')) {
                                                        $fullProofPath = asset('storage' . $proof);
                                                    } else {
                                                        $fullProofPath = asset('storage/' . $proof);
                                                    }
                                                }
                                            @endphp
                                            @if ($fullProofPath)
                                                <div class="payment-proof-wrapper mb-2" data-bs-toggle="modal" data-bs-target="#pelunasanProofModal">
                                                    <img src="{{ $fullProofPath }}" alt="Bukti Transfer Pelunasan" 
                                                        class="img-fluid rounded-2 w-100" style="max-height:180px;object-fit:cover;">
                                                    <div class="payment-proof-overlay rounded-2">
                                                        <i class="bi bi-zoom-in text-white fs-4" aria-hidden="true"></i>
                                                        <p class="text-white mt-1 small">Lihat</p>
                                                    </div>
                                                </div>
                                                <small class="text-warning"><i class="bi bi-hourglass-split me-1" aria-hidden="true"></i>Menunggu Verifikasi</small>
                                            @else
                                                <div class="text-center py-3 bg-white rounded-2 border-2 border-dashed">
                                                    <i class="bi bi-image text-muted" style="font-size:2rem;" aria-hidden="true"></i>
                                                    <p class="text-muted mt-2 small mb-0">Bukti Pelunasan</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Box --}}
                                <div class="card border-0 bg-white shadow-sm">
                                    <div class="card-body p-4 text-center">
                                        <i class="bi bi-check-square text-success" style="font-size:2.5rem;" aria-hidden="true"></i>
                                        <h6 class="mt-3 fw-bold">Bukti Diterima</h6>
                                        <p class="text-muted small mb-3">Terima kasih telah mengunggah bukti pelunasan. Tim kami akan segera memverifikasi.</p>
                                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke Pesanan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal for DP Proof --}}
                        @if ($dpProofPath)
                            <div class="modal fade" id="dpProofModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header bg-success text-white border-0">
                                            <h6 class="modal-title fw-bold">Bukti Transfer DP</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px;">
                                            <img src="{{ $dpProofPath }}" alt="Bukti Transfer DP" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <a href="{{ $dpProofPath }}" download="Bukti_DP_{{ $order->order_number }}.jpg" class="btn btn-success btn-sm">
                                                <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                            </a>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Modal for Pelunasan Proof --}}
                        @if ($fullProofPath)
                            <div class="modal fade" id="pelunasanProofModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h6 class="modal-title fw-bold">Bukti Transfer Pelunasan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px;">
                                            <img src="{{ $fullProofPath }}" alt="Bukti Transfer Pelunasan" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <a href="{{ $fullProofPath }}" download="Bukti_Pelunasan_{{ $order->order_number }}.jpg" class="btn btn-warning btn-sm">
                                                <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                            </a>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- PENDING PAYMENT (NO PROOF YET) --}}
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="alert alert-warning border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle-fill fs-2 me-3 flex-shrink-0"
                                            aria-hidden="true"></i>
                                        <div>
                                            <h5 class="alert-heading mb-2">Menunggu Pembayaran</h5>
                                            <p class="mb-0">Pesanan ini belum dibayar. Segera lakukan pembayaran untuk
                                                memproses pesanan Anda.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-muted">Total Pembayaran</span>
                                            <h3 class="mb-0 text-success fw-bold">
                                                <span class="price-convert" data-price="{{ $calculatedTotal }}"
                                                    data-currency="IDR">
                                                    Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="alert alert-info py-2 px-3 mb-0 small">
                                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                            Pesanan akan diproses setelah pembayaran dikonfirmasi
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card border-success h-100">
                                    <div
                                        class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-credit-card-2-front text-success mb-3" style="font-size:4rem;"
                                            aria-hidden="true"></i>
                                        <h5 class="mb-3">Selesaikan Pembayaran</h5>
                                        <p class="text-muted mb-4">Klik tombol di bawah untuk melanjutkan ke halaman
                                            pembayaran</p>
                                        <a href="{{ route('customer.orders.payment', $order) }}"
                                            class="btn btn-success btn-lg w-100">
                                            <i class="bi bi-credit-card-2-front me-2" aria-hidden="true"></i>Bayar
                                            Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Proof Modal --}}
            @if ($order->payment?->payment_proof)
                @php
                    $modalProofPath = null;
                    $proof = $order->payment->payment_proof;
                    if (str_starts_with($proof, 'storage/')) {
                        $modalProofPath = asset($proof);
                    } elseif (str_starts_with($proof, '/')) {
                        $modalProofPath = asset('storage' . $proof);
                    } else {
                        $modalProofPath = asset('storage/' . $proof);
                    }
                @endphp
                <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 overflow-hidden">
                            <div class="modal-header border-0 text-white"
                                style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                <h5 class="modal-title text-white">
                                    <i class="bi bi-receipt me-2" aria-hidden="true"></i>Bukti Pembayaran -
                                    {{ $order->order_number }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-0 bg-dark">
                                <img src="{{ $modalProofPath }}"
                                    alt="Bukti Pembayaran" class="img-fluid w-100"
                                    style="max-height:80vh;object-fit:contain;">
                            </div>
                            <div class="modal-footer bg-light d-flex justify-content-between">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                    Upload: {{ $order->payment?->created_at?->format('d M Y, H:i') ?? '-' }}
                                </small>
                                <div>
                                    <a href="{{ $modalProofPath }}"
                                        download="Bukti_Pembayaran_{{ $order->order_number }}.jpg"
                                        class="btn btn-sm btn-success me-2">
                                        <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════════
             CUSTOMER SUPPORT CARD
        ═════════════════════════════════════════════ --}}
            <div class="card border-0 border-success shadow-sm rounded-4 mb-4" style="border-width:2px!important;">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Butuh Bantuan?</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">
                                <i class="bi bi-chat-dots me-2 text-success" aria-hidden="true"></i>Konsultasi & Negosiasi
                            </h6>
                            <p class="mb-2 text-muted">Ada pertanyaan tentang pesanan Anda? Ingin melakukan konsultasi
                                desain atau negosiasi harga?</p>
                            <p class="mb-0">
                                <i class="bi bi-telephone-fill me-2 text-primary" aria-hidden="true"></i>
                                <strong>Hubungi Kami:</strong>
                                <a href="tel:085290505442" class="text-decoration-none">085290505442</a>
                            </p>
                        </div>
                        <div class="col-md-4 text-center mt-3 mt-md-0">
                            <a href="https://wa.me/6285290505442?text=Halo,%20saya%20ingin%20berkonsultasi%20tentang%20pesanan%20{{ $order->order_number }}"
                                target="_blank" rel="noopener noreferrer" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Chat WhatsApp
                            </a>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-clock me-1" aria-hidden="true"></i>Jam Kerja: 08.00–17.00 WIB
                            </small>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="row text-center g-3">
                        <div class="col-md-4">
                            <div class="support-feature-box">
                                <i class="bi bi-headset fs-3 text-primary d-block mb-2" aria-hidden="true"></i>
                                <strong>Konsultasi Gratis</strong>
                                <p class="text-muted small mb-0">Tim ahli siap membantu</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="support-feature-box">
                                <i class="bi bi-calculator fs-3 text-success d-block mb-2" aria-hidden="true"></i>
                                <strong>Negosiasi Harga</strong>
                                <p class="text-muted small mb-0">Harga terbaik untuk Anda</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="support-feature-box">
                                <i class="bi bi-pencil-square fs-3 text-warning d-block mb-2" aria-hidden="true"></i>
                                <strong>Custom Design</strong>
                                <p class="text-muted small mb-0">Konsultasi desain khusus</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             PRODUCTION PROCESSES CARD
        ═════════════════════════════════════════════ --}}
            @if ($hasProcesses || in_array($order->status, ['confirmed', 'in_production']))
                <div class="card border-0 shadow-sm rounded-4 mb-4 prod-card">

                    <div class="prod-card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="prod-header-icon">
                                <i class="bi bi-gear-fill" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-white">Proses Produksi</h5>
                                <small class="text-white opacity-75">
                                    @if ($hasProcesses)
                                        {{ $order->productionProcesses->count() }} tahap terdaftar
                                    @else
                                        Menunggu tim produksi memulai
                                    @endif
                                </small>
                            </div>
                        </div>
                        @if ($hasProcesses)
                            <div class="prod-overall-progress">
                                <div class="prod-progress-bar" style="width:{{ $avgProgress }}%"></div>
                                <span class="prod-progress-label">{{ $avgProgress }}% selesai</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4">

                        @if ($hasProcesses)
                            {{-- Stage Stepper --}}
                            @php
                                $processedStages = $order->productionProcesses->keyBy('stage');
                                $activeStage =
                                    $order->productionProcesses->where('status', 'in_progress')->first()?->stage ??
                                    ($order->productionProcesses
                                        ->where('status', 'completed')
                                        ->sortByDesc('updated_at')
                                        ->first()?->stage ??
                                        null);
                            @endphp

                            <div class="stage-stepper mb-4">
                                @foreach ($stageOrder as $sIdx => $sKey)
                                    @php
                                        $sProcess = $processedStages->get($sKey);
                                        $sStatus = $sProcess?->status ?? 'not_started';
                                        $isActive = $sKey === $activeStage;
                                        $stepClass = match (true) {
                                            $sStatus === 'completed' => 'step-done',
                                            $sStatus === 'in_progress' => 'step-active',
                                            $sStatus === 'paused' => 'step-paused',
                                            $sStatus === 'issue' => 'step-issue',
                                            $sStatus === 'pending' => 'step-pending',
                                            default => 'step-empty',
                                        };
                                        $stepIcon = match (true) {
                                            $sStatus === 'completed' => 'bi-check-lg',
                                            $sStatus === 'paused' => 'bi-pause-fill',
                                            $sStatus === 'issue' => 'bi-exclamation-lg',
                                            default => $stageIcons[$sKey],
                                        };
                                    @endphp
                                    <div class="step-item {{ $stepClass }} {{ $isActive ? 'step-pulse' : '' }}">
                                        <div class="step-circle">
                                            <i class="bi {{ $stepIcon }}"></i>
                                        </div>
                                        <div class="step-label">{{ $stageLabels[$sKey] }}</div>
                                        @if ($sProcess)
                                            <div class="step-pct">{{ $sProcess->progress_percentage }}%</div>
                                        @endif
                                    </div>
                                    @if ($sIdx < count($stageOrder) - 1)
                                        <div
                                            class="step-connector {{ $sStatus === 'completed' ? 'step-connector-done' : '' }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Per-Process Cards --}}
                            @foreach ($order->productionProcesses->sortBy(fn($p) => array_search($p->stage, $stageOrder)) as $process)
                                @php
                                    $processLogs = $process->logs ?? collect();
                                    $hasLogs = $processLogs->isNotEmpty();
                                    $thisStageLabel = $stageLabels[$process->stage] ?? ucfirst($process->stage);
                                    $thisStageIcon = $stageIcons[$process->stage] ?? 'bi-gear';
                                    $productName =
                                        $process->orderDetail?->product?->name ??
                                        ($process->orderDetail?->product_name ??
                                            ($order->orderDetails->first()?->product?->name ??
                                                ($order->orderDetails->first()?->product_name ?? 'Item Pesanan')));

                                    $sc = match ($process->status) {
                                        'completed' => [
                                            'hex' => '#22c55e',
                                            'light' => '#dcfce7',
                                            'icon' => 'bi-check-circle-fill',
                                            'glow' => 'rgba(34,197,94,.22)',
                                        ],
                                        'in_progress' => [
                                            'hex' => '#6366f1',
                                            'light' => '#ede9fe',
                                            'icon' => 'bi-gear-wide-connected',
                                            'glow' => 'rgba(99,102,241,.22)',
                                        ],
                                        'pending' => [
                                            'hex' => '#f59e0b',
                                            'light' => '#fef3c7',
                                            'icon' => 'bi-clock-fill',
                                            'glow' => 'rgba(245,158,11,.18)',
                                        ],
                                        'paused' => [
                                            'hex' => '#6b7280',
                                            'light' => '#f3f4f6',
                                            'icon' => 'bi-pause-circle-fill',
                                            'glow' => 'rgba(107,114,128,.18)',
                                        ],
                                        'issue' => [
                                            'hex' => '#ef4444',
                                            'light' => '#fee2e2',
                                            'icon' => 'bi-exclamation-triangle-fill',
                                            'glow' => 'rgba(239,68,68,.18)',
                                        ],
                                        default => [
                                            'hex' => '#6b7280',
                                            'light' => '#f3f4f6',
                                            'icon' => 'bi-circle-fill',
                                            'glow' => 'rgba(107,114,128,.12)',
                                        ],
                                    };
                                @endphp

                                <div class="process-block {{ !$loop->last ? 'mb-4 pb-4 process-divider' : '' }}">

                                    <div class="process-hdr" style="border-left:4px solid {{ $sc['hex'] }};">
                                        <div class="row align-items-center g-3">
                                            <div class="col-auto">
                                                <div class="proc-status-icon"
                                                    style="background:{{ $sc['light'] }};color:{{ $sc['hex'] }};box-shadow:0 0 0 5px {{ $sc['glow'] }};">
                                                    <i class="bi {{ $sc['icon'] }}"></i>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h5 class="proc-product-name mb-1">{{ $productName }}</h5>
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <span class="proc-chip proc-chip-mono">
                                                        <i class="bi bi-upc-scan me-1"></i>{{ $process->production_code }}
                                                    </span>
                                                    <span class="proc-chip proc-chip-stage"
                                                        style="background:{{ $sc['light'] }};color:{{ $sc['hex'] }};border-color:{{ $sc['hex'] }}25;">
                                                        <i class="bi {{ $thisStageIcon }} me-1"></i>{{ $thisStageLabel }}
                                                    </span>
                                                    <span class="proc-chip proc-chip-status"
                                                        style="background:{{ $sc['hex'] }};color:#fff;border-color:{{ $sc['hex'] }};">
                                                        {{ $process->status_label }}
                                                    </span>
                                                    @if ($process->assignedTo)
                                                        <span class="proc-chip proc-chip-person">
                                                            <i
                                                                class="bi bi-person-fill me-1"></i>{{ $process->assignedTo->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-wrap gap-3 proc-meta">
                                                    @if ($process->started_at)
                                                        <span><i class="bi bi-calendar-check me-1"></i>Mulai:
                                                            {{ $process->started_at->format('d M Y, H:i') }}</span>
                                                    @endif
                                                    @if ($process->completed_at)
                                                        <span class="proc-meta-success"><i
                                                                class="bi bi-check-circle me-1"></i>Selesai:
                                                            {{ $process->completed_at->format('d M Y, H:i') }}</span>
                                                    @endif
                                                    @if ($order->expected_completion_date && !$process->completed_at)
                                                        <span><i class="bi bi-calendar-event me-1"></i>Estimasi order:
                                                            {{ $order->expected_completion_date->format('d M Y') }}</span>
                                                    @endif
                                                </div>
                                                @if ($process->notes)
                                                    <div class="proc-notes mt-2">
                                                        <i
                                                            class="bi bi-chat-left-text me-1"></i>{{ Str::limit($process->notes, 160) }}
                                                    </div>
                                                @endif

                                                {{-- Process Documentation (single image) --}}
                                                @if ($process->documentation)
                                                    <div class="mt-3">
                                                        <div class="doc-single-wrapper" data-bs-toggle="modal"
                                                            data-bs-target="#docProcessModal{{ $process->id }}">
                                                            <img src="{{ asset('storage/' . $process->documentation) }}"
                                                                alt="Dokumentasi Proses {{ $thisStageLabel }}"
                                                                loading="lazy"
                                                                onerror="this.closest('.doc-single-wrapper').classList.add('doc-img-error')">
                                                            <div class="doc-single-overlay">
                                                                <i class="bi bi-zoom-in text-white fs-2"
                                                                    aria-hidden="true"></i>
                                                                <p class="text-white small mt-1 mb-0">Klik untuk perbesar
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="doc-single-actions mt-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#docProcessModal{{ $process->id }}">
                                                                <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                            </button>
                                                            <a href="{{ asset('storage/' . $process->documentation) }}"
                                                                download="Dok_{{ $process->production_code }}.jpg"
                                                                class="btn btn-sm btn-outline-success">
                                                                <i class="bi bi-download me-1"
                                                                    aria-hidden="true"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                <div class="progress-circle-large"
                                                    style="--progress:{{ $process->progress_percentage }}">
                                                    <div class="progress-text-large">
                                                        <span
                                                            class="progress-number">{{ $process->progress_percentage }}</span>
                                                        <span class="progress-percent">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Production Logs Timeline --}}
                                    @if ($hasLogs)
                                        <div class="tl-wrap mt-4">
                                            <div class="tl-section-label">
                                                <span class="tl-section-text">
                                                    <i class="bi bi-journal-text me-2"></i>Riwayat Update
                                                </span>
                                                <span class="tl-count-badge">{{ $processLogs->count() }}</span>
                                            </div>

                                            <div class="tl-list mt-3">
                                                @foreach ($processLogs->sortByDesc('created_at') as $log)
                                                    @php
                                                        $lc = match ($log->action ?? '') {
                                                            'completed' => [
                                                                'hex' => '#22c55e',
                                                                'light' => '#dcfce7',
                                                                'icon' => 'bi-check-lg',
                                                            ],
                                                            'in_progress' => [
                                                                'hex' => '#6366f1',
                                                                'light' => '#ede9fe',
                                                                'icon' => 'bi-arrow-repeat',
                                                            ],
                                                            'started' => [
                                                                'hex' => '#06b6d4',
                                                                'light' => '#cffafe',
                                                                'icon' => 'bi-play-fill',
                                                            ],
                                                            'paused' => [
                                                                'hex' => '#f59e0b',
                                                                'light' => '#fef3c7',
                                                                'icon' => 'bi-pause-fill',
                                                            ],
                                                            'issue' => [
                                                                'hex' => '#ef4444',
                                                                'light' => '#fee2e2',
                                                                'icon' => 'bi-exclamation-triangle-fill',
                                                            ],
                                                            default => [
                                                                'hex' => '#6b7280',
                                                                'light' => '#f3f4f6',
                                                                'icon' => 'bi-circle-fill',
                                                            ],
                                                        };
                                                        $docImages = $log->documentation_images ?? [];
                                                        if (is_string($docImages)) {
                                                            $docImages = json_decode($docImages, true) ?? [];
                                                        }
                                                    @endphp

                                                    <div class="tl-item d-flex gap-3">
                                                        <div
                                                            class="tl-dot-col d-flex flex-column align-items-center flex-shrink-0">
                                                            <div class="tl-dot"
                                                                style="background:{{ $lc['light'] }};border:2.5px solid {{ $lc['hex'] }};color:{{ $lc['hex'] }};">
                                                                <i class="bi {{ $lc['icon'] }}"></i>
                                                            </div>
                                                            @if (!$loop->last)
                                                                <div class="tl-connector"></div>
                                                            @endif
                                                        </div>

                                                        <div class="flex-grow-1 pb-3">
                                                            <div class="tl-log-card">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                                                    <div>
                                                                        <h6 class="tl-log-title mb-1">
                                                                            {{ $stageLabels[$log->stage ?? ''] ?? ($log->stage_label ?? ucfirst($log->stage ?? 'Update')) }}
                                                                        </h6>
                                                                        <div class="d-flex gap-2 flex-wrap">
                                                                            <span class="tl-badge"
                                                                                style="background:{{ $lc['light'] }};color:{{ $lc['hex'] }};">
                                                                                {{ $log->action_label ?? ucfirst($log->action ?? '-') }}
                                                                            </span>
                                                                            <span class="tl-badge"
                                                                                style="background:#f0f4ff;color:#6366f1;">
                                                                                <i
                                                                                    class="bi bi-speedometer2 me-1"></i>{{ $log->progress_percentage }}%
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-end flex-shrink-0">
                                                                        <small
                                                                            class="tl-time-ago d-block">{{ $log->created_at->diffForHumans() }}</small>
                                                                        <small
                                                                            class="tl-time-abs">{{ $log->created_at->format('d M Y, H:i') }}</small>
                                                                    </div>
                                                                </div>

                                                                @if ($log->notes)
                                                                    <div class="tl-notes">
                                                                        <i
                                                                            class="bi bi-chat-left-quote-fill me-2 tl-notes-icon"></i>{{ $log->notes }}
                                                                    </div>
                                                                @endif

                                                                {{-- Log Documentation Images --}}
                                                                @if ($docImages && count($docImages) > 0)
                                                                    <div class="tl-docs mt-3">
                                                                        <div class="tl-docs-header">
                                                                            <i class="bi bi-images me-2"></i>Dokumentasi
                                                                            Produksi
                                                                            <span
                                                                                class="tl-docs-count">{{ count($docImages) }}
                                                                                Foto</span>
                                                                        </div>
                                                                        <div class="doc-img-grid">
                                                                            @foreach ($docImages as $imgIdx => $image)
                                                                                @php
                                                                                    $imgPath = is_string($image)
                                                                                        ? $image
                                                                                        : $image['path'] ?? '';
                                                                                    $imgUrl = asset(
                                                                                        'storage/' . $imgPath,
                                                                                    );
                                                                                    $modalDocId =
                                                                                        'docLogModal_' .
                                                                                        $process->id .
                                                                                        '_' .
                                                                                        $log->id .
                                                                                        '_' .
                                                                                        $imgIdx;
                                                                                @endphp
                                                                                <div class="doc-img-item">
                                                                                    <div class="doc-img-wrapper"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#{{ $modalDocId }}">
                                                                                        <img src="{{ $imgUrl }}"
                                                                                            alt="{{ $stageLabels[$log->stage ?? ''] ?? 'Update' }} Foto {{ $imgIdx + 1 }}"
                                                                                            loading="lazy"
                                                                                            onerror="this.closest('.doc-img-wrapper').classList.add('doc-img-error')">
                                                                                        <div class="doc-img-overlay">
                                                                                            <i class="bi bi-zoom-in text-white fs-2"
                                                                                                aria-hidden="true"></i>
                                                                                            <p
                                                                                                class="text-white small mt-1 mb-0">
                                                                                                Klik untuk perbesar</p>
                                                                                        </div>
                                                                                        <span class="doc-img-badge">Foto
                                                                                            {{ $imgIdx + 1 }}</span>
                                                                                    </div>
                                                                                    <div class="doc-img-actions">
                                                                                        <button type="button"
                                                                                            class="btn btn-sm btn-outline-primary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#{{ $modalDocId }}">
                                                                                            <i class="bi bi-eye me-1"
                                                                                                aria-hidden="true"></i>Lihat
                                                                                        </button>
                                                                                        <a href="{{ $imgUrl }}"
                                                                                            download="Dok_{{ $process->production_code }}_{{ $imgIdx + 1 }}.jpg"
                                                                                            class="btn btn-sm btn-outline-success">
                                                                                            <i class="bi bi-download me-1"
                                                                                                aria-hidden="true"></i>Download
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($log->user)
                                                                    <div class="tl-author mt-2 pt-2">
                                                                        <i
                                                                            class="bi bi-person-circle tl-author-icon me-2"></i>
                                                                        <span class="tl-author-text">
                                                                            Diperbarui oleh:
                                                                            <strong>{{ $log->user->name }}</strong>
                                                                            @if ($log->user->role)
                                                                                <span class="tl-badge ms-1"
                                                                                    style="background:#f0f4ff;color:#6366f1;">
                                                                                    {{ $log->user->role->name }}
                                                                                </span>
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                </div>{{-- /.process-block --}}
                            @endforeach
                        @else
                            <div class="tl-empty-global">
                                <div class="tl-empty-global-icon">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <p class="tl-empty-global-title">Produksi Belum Dimulai</p>
                                <p class="tl-empty-global-sub">Tim produksi akan segera memulai proses. Halaman ini akan
                                    diperbarui otomatis saat tahap produksi ditambahkan.</p>
                                <div class="stage-stepper stage-stepper-preview mt-4">
                                    @foreach ($stageOrder as $sIdx => $sKey)
                                        <div class="step-item step-empty">
                                            <div class="step-circle"><i class="bi {{ $stageIcons[$sKey] }}"></i></div>
                                            <div class="step-label">{{ $stageLabels[$sKey] }}</div>
                                        </div>
                                        @if ($sIdx < count($stageOrder) - 1)
                                            <div class="step-connector"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Process Documentation Modals --}}
                @if ($hasProcesses)
                    @foreach ($order->productionProcesses as $process)
                        @if ($process->documentation)
                            <div class="modal fade" id="docProcessModal{{ $process->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header border-0 text-white"
                                            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                            <h5 class="modal-title text-white">
                                                <i class="bi bi-camera-fill me-2" aria-hidden="true"></i>
                                                Dokumentasi
                                                {{ $stageLabels[$process->stage] ?? ucfirst($process->stage) }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark">
                                            <img src="{{ asset('storage/' . $process->documentation) }}"
                                                alt="Dokumentasi Proses" class="img-fluid w-100"
                                                style="max-height:80vh;object-fit:contain;">
                                        </div>
                                        <div class="modal-footer bg-light d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event me-1"
                                                    aria-hidden="true"></i>{{ $process->updated_at?->format('d M Y, H:i') }}
                                            </small>
                                            <div>
                                                <a href="{{ asset('storage/' . $process->documentation) }}"
                                                    download="Dok_{{ $process->production_code }}.jpg"
                                                    class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Log Image Modals --}}
                    @foreach ($order->productionProcesses as $process)
                        @foreach ($process->logs ?? [] as $log)
                            @php
                                $logDocImages = $log->documentation_images ?? [];
                                if (is_string($logDocImages)) {
                                    $logDocImages = json_decode($logDocImages, true) ?? [];
                                }
                                $logStageLabel = $stageLabels[$log->stage ?? ''] ?? ($log->stage_label ?? 'Update');
                            @endphp
                            @foreach ($logDocImages as $imgIdx => $image)
                                @php
                                    $imgPath = is_string($image) ? $image : $image['path'] ?? '';
                                    $imgUrl = asset('storage/' . $imgPath);
                                    $modalDocId = 'docLogModal_' . $process->id . '_' . $log->id . '_' . $imgIdx;
                                @endphp
                                <div class="modal fade" id="{{ $modalDocId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content rounded-4 overflow-hidden">
                                            <div class="modal-header border-0 text-white"
                                                style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                                <h5 class="modal-title text-white">
                                                    <i class="bi bi-camera-fill me-2" aria-hidden="true"></i>
                                                    {{ $logStageLabel }}
                                                    <span class="opacity-75 ms-2 small fw-normal">Foto
                                                        {{ $imgIdx + 1 }}</span>
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body p-0 bg-dark">
                                                <img src="{{ $imgUrl }}"
                                                    alt="{{ $logStageLabel }} Foto {{ $imgIdx + 1 }}"
                                                    class="img-fluid w-100" style="max-height:80vh;object-fit:contain;">
                                            </div>
                                            <div class="modal-footer bg-light d-flex justify-content-between">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"
                                                        aria-hidden="true"></i>{{ $log->created_at->format('d M Y, H:i') }}
                                                    @if ($log->user)
                                                        &nbsp;·&nbsp;<i class="bi bi-person me-1"
                                                            aria-hidden="true"></i>{{ $log->user->name }}
                                                    @endif
                                                </small>
                                                <div>
                                                    <a href="{{ $imgUrl }}"
                                                        download="Dok_{{ $process->production_code }}_{{ $imgIdx + 1 }}.jpg"
                                                        class="btn btn-sm btn-success me-2">
                                                        <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @endforeach
                @endif

            @endif{{-- /production --}}

            {{-- ════════════════════════════════════════
             SHIPPING MONITORING CARD
        ═════════════════════════════════════════════ --}}
            @if (in_array($order->status, ['in_production', 'completed']) || $order->shippingLogs()->exists())
                @php
                    $shippingLogs = $order->shippingLogs()->orderBy('created_at', 'desc')->get();
                    $hasShipping = $shippingLogs->count() > 0;
                    $currentStage = $shippingLogs->first();
                    $shippingProgress = match (true) {
                        $hasShipping && $currentStage?->stage === 'delivered' => 100,
                        $hasShipping && $currentStage?->stage === 'out_for_delivery' => 80,
                        $hasShipping && $currentStage?->stage === 'in_transit' => 60,
                        $hasShipping && $currentStage?->stage === 'handover' => 40,
                        $hasShipping && $currentStage?->stage === 'loading' => 20,
                        $hasShipping && $currentStage?->stage === 'issue' => 50,
                        default => 0,
                    };
                @endphp

                <div class="card border-0 shadow-sm rounded-4 mb-4 prod-card">
                    {{-- Header with gradient --}}
                    <div class="prod-card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
                        <div class="d-flex align-items-center gap-3 flex-grow-1" style="position: relative; z-index: 2;">
                            <div class="prod-header-icon" style="background: rgba(255, 255, 255, .2); border-color: rgba(255, 255, 255, .3);">
                                <i class="bi bi-truck fs-5"></i>
                            </div>
                            <div>
                                <h5 class="text-white fw-bold mb-0">Monitoring Pengiriman</h5>
                                <small class="text-white-50">Pantau perjalanan pesanan Anda hingga tiba</small>
                            </div>
                        </div>

                        @if ($hasShipping)
                            <div class="prod-overall-progress" style="position: relative; z-index: 2;">
                                <div class="prod-progress-bar" style="width: {{ $shippingProgress }}%;"></div>
                                <div class="prod-progress-label">
                                    <span>{{ $shippingProgress }}%</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        @if ($hasShipping)
                            {{-- Shipping Stages Timeline --}}
                            <div class="mb-5">
                                <h6 class="fw-bold mb-4 text-muted">
                                    <i class="bi bi-diagram-3 me-2"></i>Tahapan Pengiriman
                                </h6>

                                <div class="stage-stepper" style="overflow-x: auto; padding-bottom: 8px;">
                                    <div class="stage-stepper-preview d-flex gap-3">
                                        @php
                                            $stages = [
                                                'loading' => ['label' => 'Persiapan', 'icon' => 'bi-box-seam', 'color' => '#f59e0b'],
                                                'handover' => ['label' => 'Diserah ke Kurir', 'icon' => 'bi-truck', 'color' => '#3b82f6'],
                                                'in_transit' => ['label' => 'Dalam Perjalanan', 'icon' => 'bi-geo-alt', 'color' => '#8b5cf6'],
                                                'out_for_delivery' => ['label' => 'Tiba Area Tujuan', 'icon' => 'bi-signpost-2', 'color' => '#ec4899'],
                                                'delivered' => ['label' => 'Diterima', 'icon' => 'bi-house-check', 'color' => '#10b981'],
                                            ];
                                            $completedStages = [];
                                            $currentIdx = 0;

                                            foreach ($stages as $stage => $info) {
                                                $log = $shippingLogs->firstWhere('stage', $stage);
                                                if ($log) {
                                                    $completedStages[] = $stage;
                                                    $currentIdx = array_key_last($completedStages);
                                                }
                                            }
                                        @endphp

                                        @foreach ($stages as $stage => $info)
                                            @php
                                                $isCompleted = in_array($stage, $completedStages);
                                                $isActive = $currentStage->stage === $stage;
                                                $isPending = !$isCompleted && !$isActive;
                                            @endphp

                                            <div class="step-item {{ $isCompleted ? 'step-done' : ($isActive ? 'step-active' : 'step-pending') }}">
                                                <div class="step-circle {{ $isCompleted ? 'step-done' : ($isActive ? 'step-active' : 'step-pending') }}" 
                                                    style="border-color: {{ $info['color'] }}; {{ $isCompleted || $isActive ? 'background: ' . $info['color'] . '; color: white;' : '' }}">
                                                    <i class="bi {{ $info['icon'] }}"></i>
                                                </div>
                                                <div class="step-label">{{ $info['label'] }}</div>
                                                @if ($isCompleted || $isActive)
                                                    @php
                                                        $log = $shippingLogs->firstWhere('stage', $stage);
                                                    @endphp
                                                    <div class="step-pct text-muted small">{{ $log->created_at->format('d M') }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Current Status Card --}}
                            @if ($currentStage)
                                <div class="card border-start-4 rounded-3 mb-4" style="border-color: {{ match ($currentStage->stage) {
                                    'loading' => '#f59e0b',
                                    'handover' => '#3b82f6',
                                    'in_transit' => '#8b5cf6',
                                    'out_for_delivery' => '#ec4899',
                                    'delivered' => '#10b981',
                                    'issue' => '#ef4444',
                                    default => '#6b7280',
                                } }};">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="rounded-3 p-3" style="background: {{ match ($currentStage->stage) {
                                                    'loading' => '#fef3c7',
                                                    'handover' => '#dbeafe',
                                                    'in_transit' => '#ede9fe',
                                                    'out_for_delivery' => '#fce7f3',
                                                    'delivered' => '#d1fae5',
                                                    'issue' => '#fee2e2',
                                                    default => '#f3f4f6',
                                                } }};">
                                                    <i class="bi {{ match ($currentStage->stage) {
                                                        'loading' => 'bi-box-seam',
                                                        'handover' => 'bi-truck',
                                                        'in_transit' => 'bi-geo-alt',
                                                        'out_for_delivery' => 'bi-signpost-2',
                                                        'delivered' => 'bi-house-check',
                                                        'issue' => 'bi-exclamation-triangle',
                                                        default => 'bi-circle',
                                                    } }} fs-4" style="color: {{ match ($currentStage->stage) {
                                                        'loading' => '#f59e0b',
                                                        'handover' => '#3b82f6',
                                                        'in_transit' => '#8b5cf6',
                                                        'out_for_delivery' => '#ec4899',
                                                        'delivered' => '#10b981',
                                                        'issue' => '#ef4444',
                                                        default => '#6b7280',
                                                    } }};"></i>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h6 class="fw-bold mb-1">{{ $currentStage->stage_label }}</h6>
                                                <div class="text-muted small">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $currentStage->created_at->diffForHumans() }}
                                                    <br>
                                                    <span class="text-dark fw-600">{{ $currentStage->created_at->format('d M Y, H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($currentStage->notes)
                                            <div class="mt-3 pt-3 border-top">
                                                <p class="mb-0 small">
                                                    <strong class="text-dark">Catatan:</strong><br>
                                                    {{ $currentStage->notes }}
                                                </p>
                                            </div>
                                        @endif

                                        @if ($currentStage->courier_note)
                                            <div class="mt-3 alert alert-info mb-0 py-2">
                                                <small class="mb-0">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    <strong>Update Kurir:</strong> {{ $currentStage->courier_note }}
                                                </small>
                                            </div>
                                        @endif

                                        @if ($currentStage->tracking_note)
                                            <div class="mt-3 alert alert-warning mb-0 py-2">
                                                <small class="mb-0">
                                                    <i class="bi bi-geo me-1"></i>
                                                    <strong>Tracking:</strong> {{ $currentStage->tracking_note }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Shipping Timeline --}}
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-muted">
                                    <i class="bi bi-clock-history me-2"></i>Riwayat Pengiriman
                                </h6>

                                <div class="tl-list">
                                    @forelse ($shippingLogs as $i => $log)
                                        <div class="d-flex gap-0">
                                            {{-- Timeline Dot --}}
                                            <div class="tl-dot-col">
                                                <div class="tl-dot" style="background: {{ match ($log->stage) {
                                                    'loading' => '#f59e0b',
                                                    'handover' => '#3b82f6',
                                                    'in_transit' => '#8b5cf6',
                                                    'out_for_delivery' => '#ec4899',
                                                    'delivered' => '#10b981',
                                                    'issue' => '#ef4444',
                                                    default => '#6b7280',
                                                } }};"></div>
                                                @if (!$loop->last)
                                                    <div class="tl-connector"></div>
                                                @endif
                                            </div>

                                            {{-- Timeline Card --}}
                                            <div class="tl-log-card">
                                                <div class="d-flex flex-wrap gap-2 align-items-start justify-content-between mb-2">
                                                    <div>
                                                        <div class="tl-log-title">{{ $log->stage_label }}</div>
                                                        <div class="tl-time-ago">{{ $log->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <span class="badge" style="background: {{ match ($log->stage) {
                                                        'loading' => '#f59e0b',
                                                        'handover' => '#3b82f6',
                                                        'in_transit' => '#8b5cf6',
                                                        'out_for_delivery' => '#ec4899',
                                                        'delivered' => '#10b981',
                                                        'issue' => '#ef4444',
                                                        default => '#6b7280',
                                                    } }};">{{ ucfirst(str_replace('_', ' ', $log->stage)) }}</span>
                                                </div>

                                                <div class="tl-time-abs text-muted small">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $log->created_at->format('d M Y, H:i') }}
                                                </div>

                                                @if ($log->notes)
                                                    <div class="tl-notes mt-2">
                                                        <i class="tl-notes-icon bi bi-chat-left-text me-1"></i>
                                                        {{ $log->notes }}
                                                    </div>
                                                @endif

                                                @if ($log->courier_note)
                                                    <div class="alert alert-info small mb-0 mt-2 py-2">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        <strong>Kurir:</strong> {{ $log->courier_note }}
                                                    </div>
                                                @endif

                                                @if ($log->tracking_note)
                                                    <div class="alert alert-warning small mb-0 mt-2 py-2">
                                                        <i class="bi bi-geo me-1"></i>
                                                        <strong>Tracking:</strong> {{ $log->tracking_note }}
                                                    </div>
                                                @endif

                                                @if ($log->recordedBy)
                                                    <div class="tl-author mt-2">
                                                        <i class="tl-author-icon bi bi-person-circle me-1"></i>
                                                        <span class="tl-author-text">
                                                            Dicatat oleh <strong>{{ $log->recordedBy->name }}</strong>
                                                        </span>
                                                    </div>
                                                @endif

                                                {{-- Documentation Images --}}
                                                @if ($log->documentation)
                                                    @php
                                                        // Handle both old single string and new JSON array format
                                                        $docs = [];
                                                        $isJsonArray = false;
                                                        
                                                        try {
                                                            $decoded = json_decode($log->documentation, true);
                                                            if (is_array($decoded) && count($decoded) > 0) {
                                                                $docs = $decoded;
                                                                $isJsonArray = true;
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Not JSON, treat as single file
                                                        }
                                                        
                                                        if (!$isJsonArray) {
                                                            $docs = [$log->documentation];
                                                        }
                                                    @endphp
                                                    
                                                    @if (count($docs) > 0)
                                                        <div class="tl-docs mt-3">
                                                            <div class="tl-docs-header">
                                                                <i class="bi bi-images me-1"></i>
                                                                Dokumentasi
                                                                <span class="tl-docs-count">{{ count($docs) }}</span>
                                                            </div>
                                                            <div class="doc-img-grid">
                                                                @foreach ($docs as $idx => $doc)
                                                                    @php
                                                                        // Build proper URL for documentation file
                                                                        if (filter_var($doc, FILTER_VALIDATE_URL)) {
                                                                            $docUrl = $doc;
                                                                            $docExists = true;
                                                                        } else {
                                                                            $docUrl = asset('storage/' . $doc);
                                                                            $docExists = file_exists(public_path('storage/' . $doc));
                                                                        }
                                                                        $shippingDocModalId = 'shippingDocModal_' . $log->id . '_' . $idx;
                                                                    @endphp
                                                                    <div class="doc-img-item">
                                                                        <div class="doc-img-wrapper {{ !$docExists ? 'doc-img-error' : '' }}">
                                                                            @if ($docExists)
                                                                                <img src="{{ $docUrl }}" 
                                                                                    alt="Documentation {{ $idx + 1 }}" 
                                                                                    data-bs-toggle="modal" 
                                                                                    data-bs-target="#{{ $shippingDocModalId }}"
                                                                                    style="cursor: pointer;"
                                                                                    loading="lazy">
                                                                            @endif
                                                                            <div class="doc-img-overlay">
                                                                                <div class="doc-img-actions">
                                                                                    @if ($docExists)
                                                                                        <button type="button" class="btn btn-sm btn-white rounded-pill d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#{{ $shippingDocModalId }}">
                                                                                            <i class="bi bi-zoom-in me-1"></i>Lihat
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <span class="doc-img-badge">Foto {{ $idx + 1 }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada update pengiriman saat ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-truck fs-1 text-muted opacity-25"></i>
                                </div>
                                <h6 class="text-muted fw-bold mb-1">Belum Ada Proses Pengiriman</h6>
                                <p class="text-muted small mb-0">
                                    Pengiriman akan dimulai setelah produksi pesanan Anda selesai. Anda akan menerima notifikasi ketika tahapan pengiriman dimulai.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Shipping Documentation Modals --}}
            @if (in_array($order->status, ['in_production', 'completed']) || $order->shippingLogs()->exists())
                @php
                    $shippingLogs = $order->shippingLogs()->orderBy('created_at', 'desc')->get();
                @endphp
                @foreach ($shippingLogs as $log)
                    @if ($log->documentation)
                        @php
                            $docs = [];
                            $isJsonArray = false;
                            
                            try {
                                $decoded = json_decode($log->documentation, true);
                                if (is_array($decoded) && count($decoded) > 0) {
                                    $docs = $decoded;
                                    $isJsonArray = true;
                                }
                            } catch (\Exception $e) {}
                            
                            if (!$isJsonArray) {
                                $docs = [$log->documentation];
                            }
                        @endphp
                        @foreach ($docs as $imgIdx => $image)
                            @php
                                $imgPath = is_string($image) ? $image : $image['path'] ?? '';
                                $imgUrl = asset('storage/' . $imgPath);
                                $shippingDocModalId = 'shippingDocModal_' . $log->id . '_' . $imgIdx;
                            @endphp
                            <div class="modal fade" id="{{ $shippingDocModalId }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content rounded-4 overflow-hidden">
                                        <div class="modal-header border-0 text-white"
                                            style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
                                            <h5 class="modal-title text-white">
                                                <i class="bi bi-camera-fill me-2" aria-hidden="true"></i>
                                                {{ $log->stage_label }}
                                                <span class="opacity-75 ms-2 small fw-normal">Foto {{ $imgIdx + 1 }}</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0 bg-dark">
                                            <img src="{{ $imgUrl }}"
                                                alt="{{ $log->stage_label }} Foto {{ $imgIdx + 1 }}"
                                                class="img-fluid w-100" style="max-height:80vh;object-fit:contain;">
                                        </div>
                                        <div class="modal-footer bg-light d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event me-1"
                                                    aria-hidden="true"></i>{{ $log->created_at->format('d M Y, H:i') }}
                                                @if ($log->recordedBy)
                                                    &nbsp;·&nbsp;<i class="bi bi-person me-1"
                                                        aria-hidden="true"></i>{{ $log->recordedBy->name }}
                                                @endif
                                            </small>
                                            <div>
                                                <a href="{{ $imgUrl }}"
                                                    download="Pengiriman_{{ $order->order_number }}_{{ $imgIdx + 1 }}.jpg"
                                                    class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            @endif

            {{-- ════════════════════════════════════════
             CANCEL ORDER
        ═════════════════════════════════════════════ --}}
            @if ($order->status === 'pending')
                <div class="card border-danger shadow-sm rounded-4 mb-4">
                    <div class="card-body text-center p-4">
                        <h6 class="text-muted mb-3">Butuh membatalkan pesanan?</h6>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Batalkan Pesanan
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 overflow-hidden">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Batalkan Pesanan</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
                                    <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan.
                                </div>
                                <p>Apakah Anda yakin ingin membatalkan pesanan
                                    <strong>{{ $order->order_number }}</strong>?
                                </p>
                                <p class="text-muted small mb-0">
                                    Total pesanan: <strong>
                                        <span class="price-convert" data-price="{{ $calculatedTotal }}"
                                            data-currency="IDR">
                                            Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                                        </span>
                                    </strong>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1" aria-hidden="true"></i>Tidak, Kembali
                                </button>
                                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-check me-1" aria-hidden="true"></i>Ya, Batalkan Pesanan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>{{-- /.container --}}
    </section>

@endsection

@push('styles')
    <style>
        /* ════════════════════════════════════════════════
           HERO
        ═════════════════════════════════════════════════ */
        .hero-orders {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            padding-top: 8.5rem;
            padding-bottom: 9rem;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .hero-orders .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: rgba(255, 255, 255, .6);
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

        /* ════════════════════════════════════════════════
           UTILITIES
        ═════════════════════════════════════════════════ */
        .order-detail-section {
            padding-top: 2rem;
            padding-bottom: 5rem;
            min-height: 60vh;
        }

        .rounded-top-4 {
            border-radius: 1rem 1rem 0 0 !important;
        }

        .hover-opacity {
            transition: opacity .3s ease;
        }

        .hover-opacity:hover {
            opacity: .8;
        }

        .hover-lift {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
        }

        .hover-lift:active {
            transform: translateY(0);
        }

        /* ════════════════════════════════════════════════
           INFO BOX
        ═════════════════════════════════════════════════ */
        .info-box {
            padding: 1rem 1.25rem;
            background: #f8f9fa;
            border-radius: .75rem;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .info-box-title {
            font-size: .85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c757d;
            margin-bottom: .75rem;
        }

        /* ════════════════════════════════════════════════
           PRODUCT IMAGE
        ═════════════════════════════════════════════════ */
        .product-image-container {
            width: 200px;
            height: 200px;
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
            transition: all .3s ease;
        }

        .product-image-container:hover {
            box-shadow: 0 8px 20px rgba(102, 126, 234, .25);
            transform: scale(1.03);
        }

        .product-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .product-image-container:hover .product-thumbnail {
            transform: scale(1.1);
        }

        .product-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, .9), rgba(118, 75, 162, .9));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .3s ease;
            border-radius: 1rem;
        }

        .product-image-container:hover .product-image-overlay {
            opacity: 1;
        }

        .product-image-placeholder {
            width: 200px;
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 1rem;
            border: 2px dashed #dee2e6;
        }

        /* ════════════════════════════════════════════════
           ORDER ITEM CARD
        ═════════════════════════════════════════════════ */
        .order-item-card {
            border: 1px solid #e9ecef !important;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .order-item-card:hover {
            box-shadow: 0 8px 24px rgba(102, 126, 234, .12) !important;
            transform: translateY(-2px);
        }

        /* ════════════════════════════════════════════════
           ORDER TOTAL BAR
        ═════════════════════════════════════════════════ */
        .order-total-bar {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8f0ff 100%);
            border: 1px solid #e0e7ff;
        }

        /* ════════════════════════════════════════════════
           PAYMENT DETAIL ROWS
        ═════════════════════════════════════════════════ */
        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .6rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        /* ════════════════════════════════════════════════
           PAYMENT PROOF
        ═════════════════════════════════════════════════ */
        .payment-proof-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: .75rem;
            cursor: pointer;
            transition: all .3s ease;
        }

        .payment-proof-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, .2);
        }

        .payment-proof-overlay {
            position: absolute;
            inset: 0;
            background: rgba(102, 126, 234, .85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .3s ease;
            border-radius: .75rem;
        }

        .payment-proof-wrapper:hover .payment-proof-overlay {
            opacity: 1;
        }

        /* ════════════════════════════════════════════════
           SUPPORT FEATURE BOX
        ═════════════════════════════════════════════════ */
        .support-feature-box {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: .75rem;
            border: 1px solid #e9ecef;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .support-feature-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        /* ════════════════════════════════════════════════
           PROGRESS CIRCLE
        ═════════════════════════════════════════════════ */
        .progress-circle-large {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: conic-gradient(#28a745 calc(var(--progress)*1%), #e9ecef calc(var(--progress)*1%));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }

        .progress-circle-large::before {
            content: '';
            position: absolute;
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: white;
        }

        .progress-text-large {
            position: relative;
            z-index: 1;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .progress-number {
            font-size: 1.75rem;
            line-height: 1;
            color: #28a745;
        }

        .progress-percent {
            font-size: .875rem;
            color: #6c757d;
        }

        /* ════════════════════════════════════════════════
           PRODUCTION CARD HEADER
        ═════════════════════════════════════════════════ */
        .prod-card {
            border: 1px solid #ede9fe !important;
        }

        .prod-card-header {
            background: linear-gradient(135deg, #6366f1 0%, #7c3aed 50%, #764ba2 100%);
            border-radius: 1rem 1rem 0 0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .prod-card-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M20 20c0-5.5-4.5-10-10-10S0 14.5 0 20s4.5 10 10 10 10-4.5 10-10zm10 0c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10z'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .prod-header-icon {
            width: 46px;
            height: 46px;
            border-radius: .875rem;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.35rem;
            backdrop-filter: blur(4px);
        }

        .prod-overall-progress {
            position: relative;
            width: 160px;
            height: 32px;
            background: rgba(255, 255, 255, .18);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .25);
            backdrop-filter: blur(4px);
        }

        .prod-progress-bar {
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: rgba(255, 255, 255, .35);
            transition: width .8s ease;
        }

        .prod-progress-label {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: white;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .3px;
        }

        /* ════════════════════════════════════════════════
           PROCESS BLOCK
        ═════════════════════════════════════════════════ */
        .process-block {
            animation: fadeInUp .4s ease both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .process-hdr {
            background: #fff;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(99, 102, 241, .07);
            border: 1px solid #f0f0fc;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .process-hdr:hover {
            box-shadow: 0 6px 24px rgba(99, 102, 241, .13);
            transform: translateY(-2px);
        }

        .process-divider {
            border-bottom: 2px dashed #e5e7eb;
        }

        .proc-status-icon {
            width: 62px;
            height: 62px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            transition: transform .2s ease;
        }

        .process-hdr:hover .proc-status-icon {
            transform: rotate(-8deg) scale(1.05);
        }

        .proc-product-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e1b4b;
        }

        .proc-chip {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .77rem;
            font-weight: 600;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            color: #6b7280;
        }

        .proc-chip-mono {
            font-family: 'Courier New', monospace;
            font-size: .74rem;
            background: #f9fafb;
        }

        .proc-chip-status {
            font-weight: 700;
        }

        .proc-chip-person {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #bfdbfe;
        }

        .proc-meta {
            font-size: .81rem;
            color: #9ca3af;
        }

        .proc-meta-success {
            color: #16a34a;
            font-weight: 600;
        }

        .proc-notes {
            font-size: .82rem;
            color: #6b7280;
            padding: 6px 12px;
            background: #fafafa;
            border-left: 3px solid #e5e7eb;
            border-radius: 0 6px 6px 0;
        }

        /* ════════════════════════════════════════════════
           DOC SINGLE
        ═════════════════════════════════════════════════ */
        .doc-single-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: .75rem;
            cursor: pointer;
            transition: all .3s ease;
            width: 220px;
            max-width: 100%;
            background: #e5e7eb;
        }

        .doc-single-wrapper img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .doc-single-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, .2);
        }

        .doc-single-wrapper:hover img {
            transform: scale(1.06);
        }

        .doc-single-overlay {
            position: absolute;
            inset: 0;
            background: rgba(102, 126, 234, .85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .3s ease;
            border-radius: .75rem;
        }

        .doc-single-wrapper:hover .doc-single-overlay {
            opacity: 1;
        }

        .doc-single-actions {
            display: flex;
            gap: 6px;
        }

        /* ════════════════════════════════════════════════
           DOC IMAGE GRID
        ═════════════════════════════════════════════════ */
        .tl-docs {
            background: #f8f9ff;
            border: 1px solid #e8ebfc;
            border-radius: .875rem;
            padding: 14px;
        }

        .tl-docs-header {
            font-size: .78rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .tl-docs-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 8px;
            border-radius: 10px;
            background: #6366f1;
            color: white;
            font-size: .72rem;
            font-weight: 700;
            margin-left: .4rem;
        }

        .doc-img-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .doc-img-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .doc-img-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: .75rem;
            cursor: pointer;
            transition: all .3s ease;
            background: #e5e7eb;
            aspect-ratio: 4/3;
        }

        .doc-img-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, .2);
        }

        .doc-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .doc-img-wrapper:hover img {
            transform: scale(1.06);
        }

        .doc-img-overlay {
            position: absolute;
            inset: 0;
            background: rgba(102, 126, 234, .85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .3s ease;
            border-radius: .75rem;
        }

        .doc-img-wrapper:hover .doc-img-overlay {
            opacity: 1;
        }

        .doc-img-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            z-index: 2;
            background: rgba(255, 255, 255, .92);
            color: #374151;
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        }

        .doc-img-error img {
            display: none;
        }

        .doc-img-error {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80px;
            background: #f3f4f6;
        }

        .doc-img-error::after {
            content: 'Gagal memuat';
            font-size: .75rem;
            color: #9ca3af;
        }

        .doc-img-actions {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        /* ════════════════════════════════════════════════
           TIMELINE
        ═════════════════════════════════════════════════ */
        .tl-section-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem 0 .5rem;
            border-bottom: 2px solid #f3f4f6;
        }

        .tl-section-text {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9ca3af;
        }

        .tl-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 11px;
            background: #6366f1;
            color: white;
            font-size: .72rem;
            font-weight: 700;
        }

        .tl-list {
            padding-left: .25rem;
        }

        .tl-dot-col {
            width: 46px;
        }

        .tl-dot {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .875rem;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            transition: transform .2s ease;
        }

        .tl-item:hover .tl-dot {
            transform: scale(1.1);
        }

        .tl-connector {
            width: 2px;
            flex-grow: 1;
            min-height: 16px;
            margin: 4px 0;
            background: linear-gradient(to bottom, #c7d2fe, #e5e7eb, transparent);
            border-radius: 2px;
        }

        .tl-log-card {
            background: #fff;
            border: 1px solid #f0f0fc;
            border-radius: .875rem;
            padding: 1rem 1.2rem;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .tl-log-card:hover {
            box-shadow: 0 4px 16px rgba(99, 102, 241, .1);
            transform: translateY(-1px);
        }

        .tl-log-title {
            font-size: .875rem;
            font-weight: 700;
            color: #1e1b4b;
        }

        .tl-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            border-radius: 12px;
            font-size: .74rem;
            font-weight: 600;
        }

        .tl-time-ago {
            font-size: .73rem;
            color: #9ca3af;
        }

        .tl-time-abs {
            font-size: .75rem;
            color: #6b7280;
        }

        .tl-notes {
            background: #f5f5ff;
            border-left: 3px solid #6366f1;
            padding: 8px 12px;
            border-radius: 0 8px 8px 0;
            font-size: .82rem;
            color: #4b5563;
            margin-top: .5rem;
        }

        .tl-notes-icon {
            color: #6366f1;
        }

        .tl-author {
            display: flex;
            align-items: center;
            border-top: 1px solid #f5f5f5;
            padding-top: .5rem;
        }

        .tl-author-icon {
            color: #6366f1;
            font-size: 1rem;
        }

        .tl-author-text {
            font-size: .79rem;
            color: #9ca3af;
        }

        .tl-author-text strong {
            color: #4b5563;
        }

        .tl-empty-global {
            text-align: center;
            padding: 2.5rem 1rem 1.5rem;
        }

        .tl-empty-global-icon {
            width: 80px;
            height: 80px;
            background: #ede9fe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2.25rem;
            color: #6366f1;
            box-shadow: 0 0 0 8px rgba(99, 102, 241, .08);
        }

        .tl-empty-global-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3730a3;
            margin-bottom: .5rem;
        }

        .tl-empty-global-sub {
            font-size: .85rem;
            color: #9ca3af;
            max-width: 420px;
            margin: 0 auto;
        }

        /* ════════════════════════════════════════════════
           STAGE STEPPER
        ═════════════════════════════════════════════════ */
        .stage-stepper {
            display: flex;
            align-items: flex-start;
            overflow-x: auto;
            padding: 1rem .5rem 1.25rem;
            gap: 0;
            scrollbar-width: thin;
            scrollbar-color: #c7d2fe transparent;
        }

        .stage-stepper::-webkit-scrollbar {
            height: 4px;
        }

        .stage-stepper::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 2px;
        }

        .stage-stepper-preview {
            opacity: .55;
            pointer-events: none;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            min-width: 72px;
            text-align: center;
            position: relative;
        }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            border: 2.5px solid;
            margin-bottom: .5rem;
            transition: all .25s ease;
            position: relative;
            z-index: 1;
        }

        .step-label {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            line-height: 1.3;
        }

        .step-pct {
            font-size: .68rem;
            font-weight: 700;
            margin-top: .15rem;
            opacity: .75;
        }

        .step-done .step-circle {
            background: #dcfce7;
            border-color: #22c55e;
            color: #16a34a;
        }

        .step-done .step-label {
            color: #16a34a;
        }

        .step-done .step-pct {
            color: #16a34a;
        }

        .step-active .step-circle {
            background: #ede9fe;
            border-color: #6366f1;
            color: #4338ca;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, .18);
        }

        .step-active .step-label {
            color: #4338ca;
            font-weight: 700;
        }

        .step-active .step-pct {
            color: #6366f1;
        }

        .step-paused .step-circle {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #b45309;
        }

        .step-paused .step-label {
            color: #b45309;
        }

        .step-issue .step-circle {
            background: #fee2e2;
            border-color: #ef4444;
            color: #dc2626;
        }

        .step-issue .step-label {
            color: #dc2626;
        }

        .step-pending .step-circle {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #9ca3af;
        }

        .step-pending .step-label {
            color: #6b7280;
        }

        .step-empty .step-circle {
            background: #f9fafb;
            border-color: #e5e7eb;
            color: #d1d5db;
            border-style: dashed;
        }

        .step-empty .step-label {
            color: #d1d5db;
        }

        .step-pulse .step-circle::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, .4);
            animation: stepPulse 2s infinite;
        }

        @keyframes stepPulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .step-connector {
            flex: 1;
            min-width: 20px;
            height: 2.5px;
            margin-top: 25px;
            background: #e5e7eb;
            border-radius: 2px;
            transition: background .3s ease;
            flex-shrink: 0;
        }

        .step-connector-done {
            background: linear-gradient(90deg, #22c55e, #86efac);
        }

        /* ════════════════════════════════════════════════
           RESPONSIVE
        ═════════════════════════════════════════════════ */
        @media (max-width: 991px) {

            .product-image-container,
            .product-image-placeholder {
                width: 150px !important;
                height: 150px !important;
            }

            .prod-overall-progress {
                width: 120px;
            }
        }

        @media (max-width: 768px) {
            .hero-orders {
                padding-top: 6.5rem;
                padding-bottom: 5rem;
            }

            .wave-bottom svg {
                height: 40px;
            }

            .order-detail-section {
                padding-top: 1rem;
            }

            .progress-circle-large {
                width: 70px;
                height: 70px;
            }

            .progress-circle-large::before {
                width: 58px;
                height: 58px;
            }

            .progress-number {
                font-size: 1.25rem;
            }

            .proc-status-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }

            .prod-card-header {
                padding: 1rem;
            }

            .prod-overall-progress {
                display: none;
            }

            .tl-dot-col {
                width: 38px;
            }

            .tl-dot {
                width: 38px;
                height: 38px;
            }

            .doc-img-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }

            .doc-single-wrapper {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .hero-orders h1 {
                font-size: 1.75rem;
            }

            .product-image-container,
            .product-image-placeholder {
                width: 100% !important;
                height: 220px !important;
            }

            .process-hdr {
                padding: 1rem;
            }

            .proc-product-name {
                font-size: .95rem;
            }

            .doc-img-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            function cleanupModal() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            }

            document.addEventListener('DOMContentLoaded', function() {

                // Clean backdrop on modal close
                document.querySelectorAll('.modal').forEach(function(modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', cleanupModal);
                });

                // Stagger process-block fade-in animations
                document.querySelectorAll('.process-block').forEach(function(el, i) {
                    el.style.animationDelay = (i * 0.1) + 's';
                });

                // Cancel order – SweetAlert confirmation
                document.querySelectorAll('form[action*="cancel"]').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Batalkan Pesanan?',
                            text: 'Anda yakin ingin membatalkan pesanan ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Batalkan',
                            cancelButtonText: 'Tidak',
                        }).then(result => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Membatalkan...',
                                    allowOutsideClick: false,
                                    didOpen: () => Swal.showLoading(),
                                });
                                form.submit();
                            }
                        });
                    });
                });
            });
        }());
    </script>
@endpush
