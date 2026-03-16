<!-- BRAND LOGO -->
<div class="d-flex align-items-center justify-content-center py-4 mb-3 border-bottom border-white border-opacity-10">
    <a href="{{ route('home') ?? url('/') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
        <i class="bi bi-building-fill fs-4"></i>
        <span class="fs-5 fw-bold tracking-wide">FMS ADMIN</span>
    </a>
</div>

<!-- NAVIGATION MENU -->
<div class="d-flex flex-column justify-content-between flex-grow-1 px-3 pb-4">

    <nav class="nav flex-column gap-1">

        {{-- DASHBOARD --}}
        <a href="{{ route('admin.dashboard') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-speedometer2 me-3 fs-5"></i>
            <span>Dashboard</span>
        </a>

        {{-- SECTION: MASTER DATA --}}
        <div class="text-uppercase text-white-50 fw-bold mt-4 mb-2 ps-3"
            style="font-size: 0.75rem; letter-spacing: 1px;">
            Master Data
        </div>

        <a href="{{ route('admin.orders.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-cart-check me-3 fs-5"></i>
            <span>Pesanan</span>
        </a>

        <a href="{{ route('admin.custom-orders.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.custom-orders.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-pencil-square me-3 fs-5"></i>
            <span>Custom Order</span>
        </a>

        <a href="{{ route('admin.products.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.products.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-box-seam me-3 fs-5"></i>
            <span>Produk</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-tags me-3 fs-5"></i>
            <span>Kategori</span>
        </a>

        {{-- SECTION: ADMINISTRATION --}}
        <div class="text-uppercase text-white-50 fw-bold mt-4 mb-2 ps-3"
            style="font-size: 0.75rem; letter-spacing: 1px;">
            Administrasi
        </div>

        <a href="{{ route('admin.payments.pending') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-credit-card-2-front me-3 fs-5"></i>
            <span>Pembayaran</span>
        </a>

        <a href="{{ route('admin.reports.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-file-earmark-bar-graph me-3 fs-5"></i>
            <span>Laporan</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-people me-3 fs-5"></i>
            <span>Pengguna</span>
        </a>

        <a href="{{ route('admin.settings.index') }}"
            class="nav-link text-white d-flex align-items-center p-3 rounded transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : 'opacity-75 hover-opacity-100' }}">
            <i class="bi bi-gear me-3 fs-5"></i>
            <span>Pengaturan</span>
        </a>

    </nav>

    <!-- LOGOUT SECTION (Di bagian bawah sidebar) -->
    <div class="mt-5 pt-3 border-top border-white border-opacity-10">
        <form action="{{ route('logout') }}" method="POST" class="d-grid">
            @csrf
            <button type="submit"
                class="btn btn-danger d-flex align-items-center justify-content-center delete-confirm-logout">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </button>
        </form>
    </div>

</div>

{{-- Tambahan CSS Khusus untuk Sidebar Partial ini --}}
@push('styles')
    <style>
        .hover-opacity-100:hover {
            opacity: 1 !important;
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
@endpush
