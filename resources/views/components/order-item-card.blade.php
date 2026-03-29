@props([
    'detail',
    'product' => null,
])

@php
    // Get product reference
    $prod = $product ?? $detail->product;
    
    // Image handling - robust pattern matching show.blade.php exactly
    $customImagePath = null;
    $productImagePath = null;

    // 1. Check custom design image first
    if ($detail->is_custom && $detail->custom_specifications) {
        $detailSpecs = is_string($detail->custom_specifications)
            ? json_decode($detail->custom_specifications, true)
            : $detail->custom_specifications;
        // Get design_image from specs
        if ($detailSpecs && is_array($detailSpecs)) {
            $customImagePath = $detailSpecs['design_image'] ?? null;
        }
    }

    // 2. Fallback to product image (only if no custom image AND product exists)
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

    // Format unit price
    $unitPrice = $detail->unit_price ?? $prod?->base_price ?? $prod?->price ?? 0;
    $subtotal = $detail->subtotal ?? ($unitPrice * $detail->quantity);
    $formattedUnitPrice = 'Rp ' . number_format($unitPrice, 0, ',', '.');
    $formattedSubtotal = 'Rp ' . number_format($subtotal, 0, ',', '.');
@endphp

<div class="card border border-light rounded-3 mb-3 order-item-card bg-light bg-opacity-50">
    <div class="card-body p-3">
        <div class="row g-3 align-items-start">
            
            {{-- Product Image --}}
            <div class="col-auto">
                @if ($customImagePath)
                    <div class="product-image-container" style="width: 90px; height: 90px; border-radius: .75rem;">
                        <img src="{{ asset('storage/' . $customImagePath) }}"
                            alt="Custom Design - {{ $detail->product_name }}"
                            class="product-thumbnail"
                            loading="lazy"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: .75rem;"
                            onerror="this.parentElement.classList.add('img-error')">
                        <span class="position-absolute top-0 start-0 badge bg-warning text-dark m-2"
                            style="font-size:.7rem; z-index:2;">
                            <i class="bi bi-pencil-square me-1"></i>Custom
                        </span>
                        <div class="product-image-overlay"
                            style="position: absolute; inset: 0; background: rgba(102, 126, 234, .85); border-radius: .75rem; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s ease; flex-direction: column;">
                            <i class="bi bi-zoom-in fs-5 text-white mb-2" aria-hidden="true"></i>
                            <p class="text-white small mb-0 fw-bold">Perbesar</p>
                        </div>
                    </div>
                @elseif ($productImagePath)
                    <div class="product-image-container" style="width: 90px; height: 90px; border-radius: .75rem; cursor: pointer;">
                        <img src="{{ asset('storage/' . $productImagePath) }}"
                            alt="{{ $prod?->name ?? 'Produk' }}"
                            class="product-thumbnail"
                            loading="lazy"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: .75rem;"
                            onerror="this.parentElement.classList.add('img-error')">
                        <div class="product-image-overlay"
                            style="position: absolute; inset: 0; background: rgba(102, 126, 234, .85); border-radius: .75rem; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s ease; flex-direction: column;">
                            <i class="bi bi-zoom-in fs-5 text-white mb-2" aria-hidden="true"></i>
                            <p class="text-white small mb-0 fw-bold">Perbesar</p>
                        </div>
                    </div>
                @else
                    <div class="product-image-placeholder" style="width: 90px; height: 90px; border-radius: .75rem; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i class="bi bi-image text-muted" style="font-size: 1.5rem;" aria-hidden="true"></i>
                        <small class="text-muted mt-1">No Image</small>
                    </div>
                @endif
            </div>

            {{-- Product Details --}}
            <div class="col">
                <div class="row g-2">
                    {{-- Product Name --}}
                    <div class="col-12">
                        <h5 class="mb-1 fw-bold text-dark">
                            {{ $prod?->name ?? $detail->product_name ?? 'Produk' }}
                        </h5>
                        @if (!$detail->is_custom && $prod && $prod->sku)
                            <small class="text-muted">SKU: {{ $prod->sku }}</small>
                        @endif
                    </div>

                    {{-- Custom Specifications (if any) --}}
                    @if ($detail->is_custom && !empty($detail->custom_specifications))
                        @php
                            $specs = is_string($detail->custom_specifications)
                                ? json_decode($detail->custom_specifications, true)
                                : $detail->custom_specifications;
                        @endphp
                        <div class="col-12">
                            <div class="bg-white p-2 rounded-2 border border-light small">
                                <strong class="text-primary d-block mb-1">
                                    <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>
                                    Kustomisasi:
                                </strong>
                                <ul class="mb-0 ps-3 text-muted">
                                    @if (!empty($specs['dimensions']))
                                        <li>Dimensi: {{ $specs['dimensions'] }}</li>
                                    @endif
                                    @if (!empty($specs['material']))
                                        <li>Material: {{ $specs['material'] }}</li>
                                    @endif
                                    @if (!empty($specs['color']))
                                        <li>Warna: {{ $specs['color'] }}</li>
                                    @endif
                                    @if (!empty($specs['notes']))
                                        <li>Catatan: {{ $specs['notes'] }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Quantity & Pricing --}}
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <span class="badge bg-light text-dark border border-light">
                                <i class="bi bi-box-seam me-1" aria-hidden="true"></i>
                                Jumlah: <strong>{{ $detail->quantity }}</strong>
                            </span>

                            <span class="text-muted small">
                                @ {{ $formattedUnitPrice }}
                            </span>

                            <span class="fw-bold text-primary ms-auto">
                                {{ $formattedSubtotal }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
