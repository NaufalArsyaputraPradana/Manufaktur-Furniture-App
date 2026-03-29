@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Tambah Produk Baru</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan!</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            <div class="row g-4">
                <!-- Left: Main Info -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Utama</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <x-form-input 
                                        name="name" 
                                        label="Nama Produk"
                                        type="text"
                                        placeholder="Contoh: Kursi Jati Minimalis"
                                        :value="old('name')"
                                        :errors="$errors"
                                        required
                                        autofocus />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <x-form-input 
                                        name="sku" 
                                        label="SKU (Kode Unik)"
                                        type="text"
                                        placeholder="Contoh: KJM-001"
                                        :value="old('sku')"
                                        :errors="$errors"
                                        required />
                                </div>
                            </div>

                            <x-form-input 
                                name="category_id" 
                                label="Kategori"
                                type="select"
                                :options="collect(['' => '-- Pilih Kategori --'])->union($categories->pluck('name', 'id'))"
                                :value="old('category_id')"
                                :errors="$errors"
                                required />

                            <x-form-input 
                                name="description" 
                                label="Deskripsi"
                                type="textarea"
                                rows="5"
                                placeholder="Jelaskan detail produk..."
                                :value="old('description')"
                                :errors="$errors" />

                            <h6 class="fw-bold mt-4 mb-3 text-success"><i class="bi bi-rulers me-2"></i>Spesifikasi</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <x-form-input 
                                        name="dimensions" 
                                        label="Dimensi (PxLxT)"
                                        type="text"
                                        placeholder="e.g. 120x60x75 cm"
                                        :value="old('dimensions')"
                                        :errors="$errors" />
                                </div>
                                <div class="col-md-4 mb-3">
                                    <x-form-input 
                                        name="wood_type" 
                                        label="Jenis Kayu"
                                        type="text"
                                        placeholder="e.g. Jati Grade A"
                                        :value="old('wood_type')"
                                        :errors="$errors" />
                                </div>
                                <div class="col-md-4 mb-3">
                                    <x-form-input 
                                        name="finishing_type" 
                                        label="Finishing"
                                        type="text"
                                        placeholder="e.g. Melamine Natural"
                                        :value="old('finishing_type')"
                                        :errors="$errors" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Price & Media -->
                <div class="col-lg-4">
                    <!-- Pricing -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-cash-stack me-2"></i>Harga & Produksi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="base_price" class="form-label fw-bold">Harga Dasar (Rp) <span class="text-muted fw-normal small">(opsional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                        id="base_price" name="base_price" value="{{ old('base_price') }}"
                                        min="0" placeholder="Kosongkan jika belum ditentukan">
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
                                <label for="estimated_production_days" class="form-label fw-bold">Estimasi Produksi</label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control @error('estimated_production_days') is-invalid @enderror"
                                        id="estimated_production_days" name="estimated_production_days"
                                        value="{{ old('estimated_production_days', 7) }}" min="1" required>
                                    <span class="input-group-text bg-light">Hari</span>
                                </div>
                                @error('estimated_production_days')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-images me-2"></i>Gambar Produk</h6>
                        </div>
                        <div class="card-body p-4">
                            <x-file-input
                                name="images[]"
                                id="imagesInput"
                                label="Upload Gambar"
                                multiple
                                accept="image/png, image/jpeg, image/jpg, image/webp"
                                maxSize="2"
                                helpText="Bisa pilih banyak gambar sekaligus (Max 2MB/file)."
                                preview
                                :error="$errors->has('images.*') ? $errors->first('images.*') : null"
                            />
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Aktifkan Produk</label>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_customizable"
                                    id="is_customizable" value="1" {{ old('is_customizable') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_customizable">Bisa Custom?</label>
                                <small class="d-block text-muted">Izinkan pembeli meminta perubahan ukuran/warna.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Simpan Produk
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
                        img.style.width = '70px';
                        img.style.height = '70px';
                        img.style.objectFit = 'cover';
                        previewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
        document.getElementById('productForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
