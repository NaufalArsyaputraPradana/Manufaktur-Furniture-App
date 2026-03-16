@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Edit Produk</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($product->name, 20) }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Batal
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan!</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            id="editForm">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left: Info -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Informasi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">Nama Produk <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="form-label fw-bold">SKU <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                        id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="5">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <h6 class="fw-bold mt-4 mb-3 text-success"><i class="bi bi-rulers me-2"></i>Spesifikasi</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="dimensions"
                                        class="form-label small text-uppercase fw-bold text-muted">Dimensi</label>
                                    <input type="text" class="form-control @error('dimensions') is-invalid @enderror"
                                        id="dimensions" name="dimensions"
                                        value="{{ old('dimensions', $product->dimensions) }}">
                                    @error('dimensions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="wood_type" class="form-label small text-uppercase fw-bold text-muted">Jenis
                                        Kayu</label>
                                    <input type="text" class="form-control @error('wood_type') is-invalid @enderror"
                                        id="wood_type" name="wood_type"
                                        value="{{ old('wood_type', $product->wood_type) }}">
                                    @error('wood_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="finishing_type"
                                        class="form-label small text-uppercase fw-bold text-muted">Finishing</label>
                                    <input type="text" class="form-control @error('finishing_type') is-invalid @enderror"
                                        id="finishing_type" name="finishing_type"
                                        value="{{ old('finishing_type', $product->finishing_type) }}">
                                    @error('finishing_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Media & Settings -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-cash-stack me-2"></i>Harga & Produksi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="base_price" class="form-label fw-bold">Harga Dasar <span class="text-muted fw-normal small">(opsional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                        id="base_price" name="base_price"
                                        value="{{ old('base_price', $product->base_price) }}" min="0"
                                        placeholder="Kosongkan jika belum ditentukan">
                                </div>
                                @error('base_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="bi bi-whatsapp text-success me-1"></i>
                                    Kosongkan jika harga belum ditentukan — pelanggan akan diarahkan ke WhatsApp untuk menanyakan harga.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="estimated_production_days" class="form-label fw-bold">Waktu Produksi <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control @error('estimated_production_days') is-invalid @enderror"
                                        id="estimated_production_days" name="estimated_production_days"
                                        value="{{ old('estimated_production_days', $product->estimated_production_days) }}"
                                        min="1" required>
                                    <span class="input-group-text bg-light">Hari</span>
                                </div>
                                @error('estimated_production_days')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-images me-2"></i>Gambar Produk</h6>
                        </div>
                        <div class="card-body p-4">
                            @if (!empty($product->images))
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">Gambar Saat Ini:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($product->images as $img)
                                            <img src="{{ asset('storage/' . $img) }}" class="rounded border"
                                                style="width: 60px; height: 60px; object-fit: cover; cursor:pointer;"
                                                onclick="showImageModal('{{ asset('storage/' . $img) }}', '{{ $product->name }}')">
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="imagesInput" class="form-label fw-bold">Ganti Gambar</label>
                                <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                    name="images[]" id="imagesInput" multiple
                                    accept="image/png, image/jpeg, image/jpg, image/webp">
                                <small class="text-danger d-block mt-1 small"><i class="bi bi-exclamation-circle"></i>
                                    Mengupload gambar baru akan mengganti semua gambar lama.</small>
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active"
                                    id="is_active" value="1"
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Aktifkan Produk</label>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="is_customizable"
                                    id="is_customizable" value="1"
                                    {{ old('is_customizable', $product->is_customizable) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_customizable">Bisa Custom?</label>
                                <small class="d-block text-muted ms-5">Izinkan pembeli meminta perubahan
                                    ukuran/warna.</small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg shadow-sm text-white"
                                    id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Update Produk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('imagesInput').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = '';
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'rounded border';
                        img.style.width = '60px';
                        img.style.height = '60px';
                        img.style.objectFit = 'cover';
                        previewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
        document.getElementById('editForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
