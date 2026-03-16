@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@push('styles')
    <style>
        :root {
            --admin-primary: #4e73df;
            --admin-secondary: #224abe;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .border-dashed {
            border-style: dashed !important;
            border-width: 2px !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="position-relative overflow-hidden mb-4 rounded-3 shadow-sm"
            style="background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); padding: 2rem;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.15); z-index: 1;"></div>
            <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 2;">
                <i class="bi bi-folder-plus" style="font-size: 8rem; color: white;"></i>
            </div>
            <div class="position-relative" style="z-index: 3;">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-3 p-3 shadow-sm me-3">
                        <i class="bi bi-plus-circle text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 text-white fw-bold text-shadow">Tambah Kategori</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                                        class="text-white text-decoration-none opacity-75">Kategori</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">Tambah Baru</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Terjadi Kesalahan!</h6>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
            @csrf
            <div class="row g-4">
                <!-- Left: Main Info -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Utama</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama Kategori <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}"
                                    placeholder="Contoh: Elektronik, Pakaian, Makanan" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="parent_id" class="form-label fw-bold">Induk Kategori <small
                                        class="text-muted fw-normal">(Opsional)</small></label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id"
                                    name="parent_id">
                                    <option value="">-- Jadikan Kategori Utama (Root) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Pilih jika kategori ini adalah sub-kategori dari kategori lain.</div>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4" placeholder="Jelaskan secara singkat tentang kategori ini...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Settings & Image -->
                <div class="col-lg-4">
                    <!-- Image Upload -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-image me-2"></i>Gambar Kategori</h6>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="image-preview-wrapper bg-light rounded border border-dashed d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width: 100%; height: 200px; overflow: hidden; position: relative;">
                                    <img id="previewImage" src="#" alt="Preview"
                                        class="d-none w-100 h-100 object-fit-cover">
                                    <div id="placeholderImage" class="text-muted text-center p-3">
                                        <i class="bi bi-cloud-upload fs-1 d-block mb-2"></i>
                                        <span class="small">Klik untuk upload gambar</span>
                                    </div>
                                    <input type="file"
                                        class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer @error('image') is-invalid @enderror"
                                        id="image" name="image" accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="previewFile(this)">
                                </div>
                                <small class="text-muted d-block">Format: JPG, PNG, WEBP. Maks: 2MB.</small>
                                @error('image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status & Actions -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-gear me-2"></i>Pengaturan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold ms-2" for="is_active">Aktifkan Kategori</label>
                                <small class="text-muted d-block ms-5">Jika non-aktif, kategori tidak akan muncul di menu
                                    pelanggan.</small>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Simpan Kategori
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
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
        function previewFile(input) {
            const file = input.files[0];
            const preview = document.getElementById('previewImage');
            const placeholder = document.getElementById('placeholderImage');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
        document.getElementById('createForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
