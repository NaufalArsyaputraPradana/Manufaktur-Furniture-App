<!-- Alert Component - Konsisten untuk semua halaman -->

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show slide-in shadow-sm rounded-modern border-0" role="alert">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bi bi-check-circle-fill fs-4"></i>
        </div>
        <div class="grow">
            <strong class="d-block mb-1">Berhasil!</strong>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show slide-in shadow-sm rounded-modern border-0" role="alert">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        </div>
        <div class="grow">
            <strong class="d-block mb-1">Error!</strong>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show slide-in shadow-sm rounded-modern border-0" role="alert">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bi bi-exclamation-circle-fill fs-4"></i>
        </div>
        <div class="grow">
            <strong class="d-block mb-1">Peringatan!</strong>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show slide-in shadow-sm rounded-modern border-0" role="alert">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bi bi-info-circle-fill fs-4"></i>
        </div>
        <div class="grow">
            <strong class="d-block mb-1">Informasi</strong>
            <span>{{ session('info') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show slide-in shadow-sm rounded-modern border-0" role="alert">
    <div class="d-flex align-items-start">
        <div class="me-3">
            <i class="bi bi-exclamation-octagon-fill fs-4"></i>
        </div>
        <div class="grow">
            <strong class="d-block mb-2">Terdapat {{ $errors->count() }} kesalahan:</strong>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif
