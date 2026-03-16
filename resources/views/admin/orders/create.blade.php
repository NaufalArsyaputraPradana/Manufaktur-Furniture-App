@extends('layouts.admin')

@section('title', 'Buat Pesanan Baru')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Buat Pesanan Baru</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Pesanan</a></li>
                        <li class="breadcrumb-item active">Buat Baru</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan!</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" id="orderForm">
            @csrf
            <div class="row g-4">
                <!-- Left: Order Info -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Info Pelanggan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="user_id" class="form-label fw-bold">Pelanggan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id"
                                    name="user_id" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="order_date" class="form-label fw-bold">Tanggal Order <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                    id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}"
                                    required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="estimated_delivery_date" class="form-label fw-bold">Estimasi Selesai</label>
                                <input type="date"
                                    class="form-control @error('estimated_delivery_date') is-invalid @enderror"
                                    id="estimated_delivery_date" name="estimated_delivery_date"
                                    value="{{ old('estimated_delivery_date') }}">
                                @error('estimated_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label fw-bold">Alamat Pengiriman <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                    name="shipping_address" rows="3" required placeholder="Alamat lengkap...">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-0">
                                <label for="notes" class="form-label fw-bold">Catatan Internal <small
                                        class="text-muted fw-normal">(Opsional)</small></label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"
                                    placeholder="Catatan tambahan untuk pesanan ini...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sticky -->
                    <div class="card shadow-sm border-0 rounded-3 bg-light sticky-top" style="top: 1rem; z-index: 100;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Ringkasan Biaya</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold">Total</span>
                                <span class="fs-4 fw-bold text-primary" id="summaryTotal">Rp 0</span>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-bold shadow-sm" id="submitBtn">
                                <i class="bi bi-save me-2"></i>Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Items -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-cart me-2"></i>Item Pesanan</h6>
                            <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="addItem()">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Item
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div id="itemsContainer">
                                <!-- Items will be injected here via JS -->
                            </div>

                            <div id="emptyState" class="text-center py-5 text-muted">
                                <i class="bi bi-basket fs-1 mb-2 d-block"></i>
                                Belum ada item ditambahkan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template Item (Hidden) -->
    <template id="itemTemplate">
        <div class="item-row card border mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-secondary">Item #<span class="item-index">1</span></h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-bs-toggle="tooltip"
                        title="Hapus Item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="product_id_INDEX" class="form-label small fw-bold text-uppercase">Produk</label>
                        <select class="form-select product-select" id="product_id_INDEX"
                            name="products[INDEX][product_id]" onchange="onProductChange(this)">
                            <option value="">-- Produk Custom --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->base_price }}"
                                    data-name="{{ $product->name }}">
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden field to mark if custom (if product_id empty = custom) -->
                        <input type="hidden" name="products[INDEX][is_custom]" class="is-custom-input" value="1">
                    </div>
                    <div class="col-md-6">
                        <label for="product_name_INDEX" class="form-label small fw-bold text-uppercase">Nama Item <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control item-name" id="product_name_INDEX"
                            name="products[INDEX][product_name]" placeholder="Nama item..." required>
                    </div>
                    <div class="col-md-4">
                        <label for="unit_price_INDEX" class="form-label small fw-bold text-uppercase">Harga Satuan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control item-price" id="unit_price_INDEX"
                                name="products[INDEX][unit_price]" min="0" value="0" required
                                oninput="calculateTotal()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="quantity_INDEX" class="form-label small fw-bold text-uppercase">Jumlah <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control item-qty" id="quantity_INDEX"
                            name="products[INDEX][quantity]" min="1" value="1" required
                            oninput="calculateTotal()">
                    </div>
                    <div class="col-md-5">
                        <label for="subtotal_INDEX" class="form-label small fw-bold text-uppercase">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="text" class="form-control bg-light fw-bold item-subtotal" id="subtotal_INDEX"
                                readonly value="0">
                        </div>
                    </div>

                    <!-- Custom Fields Toggle -->
                    <div class="col-12">
                        <a class="text-decoration-none small fw-bold text-primary" data-bs-toggle="collapse"
                            href="#customFieldsINDEX" aria-controls="customFieldsINDEX" role="button">
                            <i class="bi bi-gear-fill me-1"></i>Opsi Custom / Desain Spesifik
                        </a>
                        <div class="collapse mt-2" id="customFieldsINDEX">
                            <div class="card card-body bg-light border-0">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="custom_desc_INDEX" class="form-label small">Deskripsi
                                            Spesifikasi</label>
                                        <textarea class="form-control" id="custom_desc_INDEX" name="products[INDEX][customizations][description]"
                                            rows="2" placeholder="Warna, bahan, ukuran khusus..."></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="custom_image_INDEX" class="form-label small">Upload Gambar Desain
                                            (Opsional)</label>
                                        <input type="file" class="form-control" id="custom_image_INDEX"
                                            name="products[INDEX][customizations][design_image]"
                                            accept="image/png, image/jpeg, image/jpg, image/webp">
                                        <small class="text-muted">Format: JPG, PNG, WEBP. Max 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('styles')
    <style>
        .card {
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let itemCounter = 0;

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const template = document.getElementById('itemTemplate');
            const clone = template.content.cloneNode(true);
            const emptyState = document.getElementById('emptyState');

            // Replace placeholders (INDEX) with unique counter
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace(/INDEX/g, itemCounter);
            });
            clone.querySelectorAll('[id*="INDEX"]').forEach(el => {
                el.id = el.id.replace(/INDEX/g, itemCounter);
            });
            clone.querySelectorAll('[for*="INDEX"]').forEach(el => {
                if (el.htmlFor) el.htmlFor = el.htmlFor.replace(/INDEX/g, itemCounter);
            });
            clone.querySelectorAll('[href*="INDEX"]').forEach(el => {
                el.href = el.href.replace(/INDEX/g, itemCounter);
            });
            clone.querySelectorAll('[aria-controls*="INDEX"]').forEach(el => {
                el.setAttribute('aria-controls', el.getAttribute('aria-controls').replace(/INDEX/g, itemCounter));
            });

            clone.querySelector('.item-index').textContent = itemCounter + 1;
            clone.querySelector('.remove-item').onclick = function() {
                this.closest('.item-row').remove();
                calculateTotal();
                if (container.children.length === 0) emptyState.style.display = 'block';
            };

            container.appendChild(clone);
            emptyState.style.display = 'none';

            // Auto calculate for new row
            calculateTotal();

            itemCounter++;
        }

        function onProductChange(select) {
            const row = select.closest('.item-row');
            const selected = select.options[select.selectedIndex];
            const nameInput = row.querySelector('.item-name');
            const priceInput = row.querySelector('.item-price');
            const customInput = row.querySelector('.is-custom-input');

            if (select.value) {
                // Produk Katalog
                nameInput.value = selected.dataset.name;
                nameInput.readOnly = true; // Kunci nama jika dari katalog
                priceInput.value = selected.dataset.price;
                customInput.value = "0"; // Bukan custom murni
            } else {
                // Produk Custom
                nameInput.value = "";
                nameInput.readOnly = false;
                priceInput.value = 0;
                customInput.value = "1"; // Custom murni
            }
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const qty = parseInt(row.querySelector('.item-qty').value) || 0;
                const subtotal = price * qty;

                row.querySelector('.item-subtotal').value = new Intl.NumberFormat('id-ID').format(subtotal);
                total += subtotal;
            });

            // Tanpa Pajak, Total = Subtotal
            document.getElementById('summaryTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        // Initialize first item on load
        document.addEventListener('DOMContentLoaded', () => {
            addItem();
        });

        // Form Submit Handler
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                // Pastikan SweetAlert tersedia, jika tidak gunakan alert standar
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Tambahkan minimal satu item pesanan!', 'error');
                } else {
                    alert('Tambahkan minimal satu item pesanan!');
                }
                return;
            }

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });
    </script>
@endpush
