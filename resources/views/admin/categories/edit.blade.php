@extends('layouts.admin')

@section('title', 'Edit Kategori')

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
                <i class="bi bi-pencil-square" style="font-size: 8rem; color: white;"></i>
            </div>
            <div class="position-relative" style="z-index: 3;">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-3 p-3 shadow-sm me-3">
                        <i class="bi bi-pencil text-warning fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 text-white fw-bold text-shadow">Edit Kategori</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                                        class="text-white text-decoration-none opacity-75">Kategori</a></li>
                                <li class="breadcrumb-item text-white active" aria-current="page">
                                    {{ Str::limit($category->name, 20) }}</li>
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

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data"
            id="editForm">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left: Main Info -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Utama</h6>
                        </div>
                        <div class="card-body p-4">
                            <x-form-input 
                                name="name" 
                                label="Nama Kategori"
                                type="text"
                                :value="old('name', $category->name)"
                                :errors="$errors"
                                required />

                            <x-form-input 
                                name="parent_id" 
                                label="Induk Kategori"
                                type="select"
                                :options="collect(['' => '-- Kategori Utama (Root) --'])->union($parents->pluck('name', 'id'))"
                                :value="old('parent_id', $category->parent_id)"
                                :errors="$errors"
                                help="Ubah jika ingin memindahkan kategori ini ke bawah kategori lain." />

                            <x-form-input 
                                name="description" 
                                label="Deskripsi"
                                type="textarea"
                                rows="4"
                                :value="old('description', $category->description)"
                                :errors="$errors" />
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
                            <x-file-input
                                name="image"
                                id="image"
                                label="Gambar Kategori"
                                accept="image/png, image/jpeg, image/jpg, image/webp"
                                maxSize="2"
                                helpText="Format: JPG, PNG, WEBP. Maks: 2MB."
                                preview
                                :previewUrl="$category->image ? asset('storage/' . $category->image) : null"
                                :previewAlt="$category->name"
                                :error="$errors->has('image') ? $errors->first('image') : null"
                            />
                        </div>
                    </div>

                    <!-- Status & Actions -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-gear me-2"></i>Pengaturan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="is_active"
                                    name="is_active" value="1"
                                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Aktifkan Kategori</label>
                                <small class="text-muted d-block ms-5">Kontrol visibilitas kategori di aplikasi.</small>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg shadow-sm text-white"
                                    id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Perbarui Kategori
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 mt-4">
                        <div class="card-body bg-light">
                            <small class="text-muted d-block mb-1">Dibuat:
                                {{ $category->created_at->format('d M Y H:i') }}</small>
                            <small class="text-muted d-block">Terakhir Update:
                                {{ $category->updated_at->format('d M Y H:i') }}</small>
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
                    if (placeholder) placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
        document.getElementById('editForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
