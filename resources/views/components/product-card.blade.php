@props([
    'product',
    'showCategory' => true,
    'showDimensions' => true,
    'showCart' => false,
])

@php
    // Determine product image
    $productImage = null;
    if (is_array($product->images ?? null) && count($product->images) > 0) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($product->images[0])) {
            $productImage = asset('storage/' . $product->images[0]);
        }
    } elseif (!empty($product->image)) {
        $productImage = asset('storage/' . $product->image);
    }

    // Get category name
    $categoryName = $product->category?->name ?? 'Furniture';

    // Determine price display
    $displayPrice = $product->base_price ?? $product->price ?? null;
    $formattedPrice = $displayPrice ? 'Rp ' . number_format($displayPrice, 0, ',', '.') : 'Hubungi untuk harga';
@endphp

<article class="product-card card h-100 border-0 shadow-sm rounded-4 hover-lift transition-all
    {{ !$product->is_active ? 'opacity-75' : '' }}" role="region" aria-label="{{ $product->name }}">

    {{-- Product Image Container --}}
    <div class="product-image-wrapper position-relative overflow-hidden bg-light" style="height: 240px;">
        @if ($productImage)
            <img src="{{ $productImage }}" 
                class="card-img-top product-image w-100 h-100" 
                alt="{{ $product->name }}"
                loading="lazy"
                style="object-fit: cover;"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="card-img-top d-none align-items-center justify-content-center text-white"
                style="height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="text-center">
                    <i class="bi bi-image display-3 mb-2 opacity-50" aria-hidden="true"></i>
                    <p class="small mb-0 opacity-75 px-3">{{ Str::limit($product->name, 30) }}</p>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center justify-content-center text-white h-100"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="text-center">
                    <i class="bi bi-image display-3 mb-2 opacity-50" aria-hidden="true"></i>
                    <p class="small mb-0 opacity-75 px-3">{{ Str::limit($product->name, 30) }}</p>
                </div>
            </div>
        @endif

        {{-- Availability Status Badge --}}
        <span class="badge position-absolute top-0 end-0 m-3 shadow-sm 
            {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}"
            aria-label="{{ $product->is_active ? 'Produk tersedia' : 'Produk kosong' }}">
            <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"
                aria-hidden="true"></i>
            {{ $product->is_active ? 'Tersedia' : 'Kosong' }}
        </span>

        {{-- Category Badge --}}
        @if ($showCategory && $product->category)
            <div class="position-absolute bottom-0 start-0 w-100 p-3"
                style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                <span class="badge bg-white text-dark shadow-sm">
                    <i class="bi bi-tag-fill me-1" aria-hidden="true"></i>
                    {{ $categoryName }}
                </span>
            </div>
        @endif
    </div>

    {{-- Product Details --}}
    <div class="card-body d-flex flex-column p-4">
        {{-- Product Name --}}
        <h3 class="h5 card-title mb-2 fw-bold text-dark" style="min-height: 1.5em;">
            {{ $product->name }}
        </h3>

        {{-- Description --}}
        <p class="card-text text-muted small mb-3 grow lh-base">
            {{ Str::limit($product->description, 80) }}
        </p>

        {{-- Dimensions --}}
        @if ($showDimensions && $product->dimensions)
            <div class="d-flex align-items-center mb-3 text-muted small bg-light p-2 rounded-3 border">
                <i class="bi bi-rulers me-2 text-primary shrink-0" aria-hidden="true"></i>
                <span>{{ $product->dimensions }}</span>
            </div>
        @endif

        {{-- Pricing Section --}}
        <div class="mt-auto border-top pt-3">
            <div class="price-box mb-3 d-flex justify-content-between align-items-end">
                @if ($product->base_price !== null)
                    <small class="text-muted fw-bold text-uppercase">Mulai Dari</small>
                    <div class="text-end">
                        <span class="h5 text-primary mb-0 fw-bold"
                            data-price="{{ $product->base_price }}" data-currency="IDR">
                            {{ $formattedPrice }}
                        </span>
                    </div>
                @else
                    <small class="text-muted fw-bold text-uppercase">Harga</small>
                    <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20menanyakan%20harga%20produk%20{{ urlencode($product->name) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="btn btn-success btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> Tanya Harga
                    </a>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="d-grid gap-2">
                @auth
                    @if (auth()->user()?->role?->name === 'customer' && $product->is_active && $product->base_price !== null)
                        <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_name" value="{{ $product->name }}">
                            <input type="hidden" name="price" value="{{ $product->base_price }}">
                            <input type="hidden" name="image" value="{{ $product->images[0] ?? '' }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="btn btn-primary w-100 rounded-3 shadow-sm fw-bold"
                                aria-label="Tambah {{ $product->name }} ke keranjang">
                                <i class="bi bi-cart-plus-fill me-2" aria-hidden="true"></i> Tambah ke Keranjang
                            </button>
                        </form>
                    @endif
                @endauth

                <a href="{{ route('products.show', $product->slug ?? $product->id) }}"
                    class="btn btn-outline-primary w-100 rounded-3 fw-medium transition-all hover-lift"
                    aria-label="Lihat detail {{ $product->name }}">
                    <i class="bi bi-eye-fill me-2" aria-hidden="true"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>
</article>

