@props([
    'detail',
    'product' => null,
])

@php
    // Get product reference
    $prod = $product ?? $detail->product;
    
    // Determine thumbnail source
    $thumbSrc = null;

    // 1. Check custom design image
    if ($detail->is_custom && !empty($detail->custom_specifications)) {
        $specs = is_string($detail->custom_specifications)
            ? json_decode($detail->custom_specifications, true)
            : $detail->custom_specifications;

        if (!empty($specs['design_image']) && 
            \Illuminate\Support\Facades\Storage::disk('public')->exists($specs['design_image'])) {
            $thumbSrc = asset('storage/' . $specs['design_image']);
        }
    }

    // 2. Fallback to product image
    if (!$thumbSrc && $prod) {
        if (!empty($prod->images) && is_array($prod->images)) {
            $thumbSrc = asset('storage/' . $prod->images[0]);
        } elseif (!empty($prod->image)) {
            $thumbSrc = asset('storage/' . $prod->image);
        }
    }

    // Format unit price
    $unitPrice = $detail->unit_price ?? $prod->base_price ?? $prod->price ?? 0;
    $subtotal = $detail->subtotal ?? ($unitPrice * $detail->quantity);
    $formattedUnitPrice = 'Rp ' . number_format($unitPrice, 0, ',', '.');
    $formattedSubtotal = 'Rp ' . number_format($subtotal, 0, ',', '.');
@endphp

<div class="card border border-light rounded-3 mb-3 order-item-card bg-light bg-opacity-50">
    <div class="card-body p-3">
        <div class="row g-3 align-items-start">
            
            {{-- Thumbnail --}}
            <div class="col-auto">
                @if ($thumbSrc)
                    <img src="{{ $thumbSrc }}"
                        alt="{{ $prod->name ?? 'Produk' }}"
                        class="rounded-2"
                        loading="lazy"
                        style="width: 80px; height: 80px; object-fit: cover;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="d-none rounded-2 align-items-center justify-content-center text-white"
                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-image" aria-hidden="true"></i>
                    </div>
                @else
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-white"
                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-image" aria-hidden="true"></i>
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
