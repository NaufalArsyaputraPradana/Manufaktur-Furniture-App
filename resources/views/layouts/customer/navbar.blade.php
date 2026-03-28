@php
    $cartCount = count((array) session('cart', []));
    $user = auth()->user();
    $roleName = $user?->role?->name;
@endphp

<nav class="navbar-modern" role="navigation" aria-label="Navigasi Utama">
    <div class="container-fluid px-3 px-lg-5">
        <div class="navbar-content">

            {{-- Brand Logo --}}
            <a class="brand-logo" href="{{ route('home') }}" aria-label="UD Bisa Furniture - Beranda">
                <div class="brand-icon" aria-hidden="true">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="brand-text">
                    <span class="brand-name">UD Bisa</span>
                    <span class="brand-sub">Furniture</span>
                </div>
            </a>

            {{-- Desktop Menu --}}
            <div class="nav-menu d-none d-lg-flex" role="menubar">
                <a href="{{ route('home') }}" class="nav-item {{ Request::is('/') ? 'active' : '' }}" role="menuitem">
                    <i class="bi bi-house-door"></i> <span>Home</span>
                </a>
                <a href="{{ route('products.index') }}" class="nav-item {{ Request::is('products*') ? 'active' : '' }}"
                    role="menuitem">
                    <i class="bi bi-grid"></i> <span>Katalog</span>
                </a>
                <a href="{{ route('about') }}" class="nav-item {{ Request::is('about') ? 'active' : '' }}"
                    role="menuitem">
                    <i class="bi bi-info-circle"></i> <span>Tentang</span>
                </a>
                <a href="{{ route('contact') }}" class="nav-item {{ Request::is('contact') ? 'active' : '' }}"
                    role="menuitem">
                    <i class="bi bi-telephone"></i> <span>Kontak</span>
                </a>

                @auth
                    @if ($roleName === 'customer' || $roleName === 'admin' || $roleName === 'production_staff')
                        <a href="{{ route('customer.orders.index') }}"
                            class="nav-item {{ Request::is('customer/orders*') ? 'active' : '' }}" role="menuitem">
                            <i class="bi bi-box-seam"></i> <span>Pesanan</span>
                        </a>
                        <a href="{{ route('customer.cart.index') }}"
                            class="nav-item {{ Request::is('customer/cart*') ? 'active' : '' }}" role="menuitem">
                            <div class="position-relative">
                                <i class="bi bi-cart3"></i>
                                @if ($cartCount > 0)
                                    <span class="cart-badge">{{ $cartCount }}</span>
                                @endif
                            </div>
                            <span>Keranjang</span>
                        </a>
                    @endif

                    @if ($roleName === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-item {{ Request::is('admin') || Request::is('admin/*') ? 'active' : '' }}"
                            role="menuitem">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard Admin</span>
                        </a>
                    @elseif ($roleName === 'production_staff')
                        <a href="{{ route('production.dashboard') }}"
                            class="nav-item {{ Request::is('production') || Request::is('production/*') ? 'active' : '' }}"
                            role="menuitem">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard Produksi</span>
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Desktop Actions --}}
            <div class="nav-actions d-none d-lg-flex">
                <div class="d-flex align-items-center gap-2 border-end pe-3 me-2">

                    {{--
                        SATU instance Google Translate untuk desktop & mobile.
                        Saat offcanvas mobile dibuka  → widget dipindah ke #translate-slot-mobile.
                        Saat offcanvas mobile ditutup → widget dikembalikan ke sini.
                        Widget selalu "visible" saat dirender sehingga dropdown bahasa bisa diklik.
                    --}}
                    <div id="translate-wrapper-desktop" class="tool-box" title="Pilih Bahasa">
                        <i class="bi bi-translate text-primary"></i>
                        <span class="tool-label">Translate</span>
                        <div id="google_translate_element" class="gt-overlay"></div>
                    </div>

                    <div class="tool-box currency-tool" title="Pilih Mata Uang">
                        <i class="bi bi-cash-coin text-primary"></i>
                        <select class="currency-selector" aria-label="Ubah Mata Uang"></select>
                    </div>
                </div>

                @auth
                    <div class="dropdown">
                        <button class="user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ Str::limit($user->name, 12) }}</span>
                                <span class="user-role">{{ ucfirst(str_replace('_', ' ', $roleName)) }}</span>
                            </div>
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                            <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                                <div class="fw-bold text-dark small">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $user->email }}</div>
                            </li>
                            @if ($roleName === 'customer')
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile.index') }}">
                                        <i class="bi bi-person me-2"></i> Profil Saya
                                    </a>
                                </li>
                            @elseif ($roleName === 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                    </a>
                                </li>
                            @elseif ($roleName === 'production_staff')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('production.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i>
                                        <span>Dashboard Produksi</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('staff.production.schedules.index') }}">
                                        <i class="bi bi-calendar3 me-2 text-success"></i>
                                        <span>Jadwal Produksi</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('staff.production.todos.index') }}">
                                        <i class="bi bi-list-check me-2 text-warning"></i>
                                        <span>Catatan Produksi</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn-nav btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn-nav btn-register">Daftar</a>
                    </div>
                @endauth
            </div>

            {{-- Mobile Toggle --}}
            <button class="mobile-toggle d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mobileMenu" aria-label="Menu Mobile">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

{{-- Mobile Menu (Offcanvas) --}}
<div class="offcanvas offcanvas-end" id="mobileMenu" tabindex="-1" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header border-bottom">
        <div class="brand-logo">
            <div class="brand-icon"><i class="bi bi-box-seam"></i></div>
            <div class="brand-text">
                <span class="brand-name" id="mobileMenuLabel">UD Bisa</span>
                <span class="brand-sub">Furniture</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        {{-- Mobile Tools --}}
        <div class="mobile-tools mb-4 p-3 bg-light rounded-3">
            <span class="tools-label d-block mb-2">Pengaturan Tampilan</span>
            <div class="d-flex flex-column gap-2">

                {{--
                    Slot penerima #google_translate_element saat mobile offcanvas dibuka.
                    Widget sudah ter-render sempurna saat dipindah → dropdown langsung bisa diklik.
                --}}
                <div id="translate-slot-mobile" class="tool-box w-100">
                    <i class="bi bi-translate text-primary"></i>
                    <span class="tool-label">Translate</span>
                    {{-- #google_translate_element di-append ke sini oleh JS --}}
                </div>

                <div class="tool-box currency-tool w-100">
                    <i class="bi bi-cash-coin text-primary"></i>
                    <select class="currency-selector" aria-label="Ubah Mata Uang"></select>
                </div>
            </div>
        </div>

        {{-- Mobile Nav --}}
        <nav class="mobile-nav">
            <a href="{{ route('home') }}" class="mobile-nav-item {{ Request::is('/') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
            <a href="{{ route('products.index') }}"
                class="mobile-nav-item {{ Request::is('products*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Katalog
            </a>
            <a href="{{ route('about') }}" class="mobile-nav-item {{ Request::is('about') ? 'active' : '' }}">
                <i class="bi bi-info-circle"></i> Tentang Kami
            </a>
            <a href="{{ route('contact') }}" class="mobile-nav-item {{ Request::is('contact') ? 'active' : '' }}">
                <i class="bi bi-telephone"></i> Kontak
            </a>

            @auth
                <div class="mobile-divider"></div>
                @if ($roleName === 'customer')
                    <a href="{{ route('customer.orders.index') }}"
                        class="mobile-nav-item {{ Request::is('customer/orders*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Pesanan Saya
                    </a>
                    <a href="{{ route('customer.cart.index') }}"
                        class="mobile-nav-item {{ Request::is('customer/cart*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Keranjang ({{ $cartCount }})
                    </a>
                    <a href="{{ route('customer.profile.index') }}" class="mobile-nav-item">
                        <i class="bi bi-person-circle"></i> Profil
                    </a>
                @elseif ($roleName === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item">
                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                    </a>
                @elseif ($roleName === 'production_staff')
                    <a href="{{ route('production.dashboard') }}"
                        class="mobile-nav-item {{ Request::is('production') || Request::is('production/*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard Produksi
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="mobile-nav-item text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            @else
                <div class="mobile-divider"></div>
                <a href="{{ route('login') }}" class="mobile-nav-item fw-bold">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="{{ route('register') }}" class="mobile-nav-item fw-bold text-primary">
                    <i class="bi bi-person-plus"></i> Daftar Akun
                </a>
            @endauth
        </nav>
    </div>
</div>

<style>
    /* ============================================
       VARIABLES
    ============================================ */
    :root {
        --nav-height: 75px;
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* ============================================
       NAVBAR CORE
    ============================================ */
    .navbar-modern {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1020;
        transition: box-shadow 0.3s ease;
    }

    .navbar-content {
        height: var(--nav-height);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ============================================
       BRAND
    ============================================ */
    .brand-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .brand-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-gradient);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .brand-name {
        font-weight: 800;
        font-size: 1.3rem;
        color: #2d3748;
        display: block;
        line-height: 1;
    }

    .brand-sub {
        font-size: 0.8rem;
        color: #718096;
        font-weight: 500;
    }

    /* ============================================
       DESKTOP NAV ITEMS
    ============================================ */
    .nav-item {
        color: #4a5568;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 8px 16px;
        border-radius: 8px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-item:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.08);
    }

    .nav-item.active {
        color: #667eea;
        background: rgba(102, 126, 234, 0.12);
    }

    .cart-badge {
        position: absolute;
        top: -8px;
        right: -10px;
        background: #f5576c;
        color: #fff;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 10px;
        border: 2px solid #fff;
    }

    /* ============================================
       DESKTOP ACTIONS
    ============================================ */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* ============================================
       TOOL BOX
       ─────────────────────────────────────────
       FIX KRITIS #1: position: relative
         Tanpa ini, .gt-overlay (position:absolute) tidak ter-anchor
         ke dalam .tool-box, melainkan ke ancestor lain.
         Hasilnya overlay tidak menutupi area yang benar → klik meleset.

       FIX KRITIS #2: overflow: hidden
         Mencegah .gt-overlay dan widget Google bocor keluar batas
         tool-box dan mengganggu/memblokir elemen lain di sekitarnya.
    ============================================ */
    .tool-box {
        position: relative;
        /* FIX #1 */
        overflow: hidden;
        /* FIX #2 */
        background: #f8f9fc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 4px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        height: 38px;
        min-width: 130px;
        transition: border-color 0.3s, background 0.3s;
    }

    .tool-box:hover {
        border-color: #667eea;
        background: #fff;
    }

    /* Ikon dan label tidak boleh menghalangi klik ke overlay */
    .tool-box>i,
    .tool-label {
        pointer-events: none;
        flex-shrink: 0;
        z-index: 1;
    }

    .tool-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a5568;
        white-space: nowrap;
    }

    /* ─────────────────────────────────────────
       .gt-overlay — lapisan klik transparan
       ─────────────────────────────────────────
       FIX KRITIS #3: z-index: 20
         Lebih tinggi dari semua child (.tool-label, ikon, dsb.)
         sehingga event klik selalu sampai ke widget Google di dalamnya.
         z-index:5 dari versi lama terlalu rendah.

       FIX KRITIS #4: overflow: hidden
         Clip semua konten (termasuk widget Google yang ukurannya
         kadang overflow) agar tidak keluar batas overlay.
    ============================================ */
    .gt-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: 20;
        /* FIX #3 */
        overflow: hidden;
        /* FIX #4 */
        cursor: pointer;
    }

    /* ─────────────────────────────────────────
       FIX KRITIS #5: Paksa widget Google memenuhi 100% overlay
       ─────────────────────────────────────────
       Widget yang diinjeksi Google (goog-te-gadget, goog-te-combo)
       defaultnya berukuran kecil. Tanpa ini, area yang bisa diklik
       hanya sebagian kecil dari tool-box → terasa "tidak bisa diklik".
    ============================================ */
    .gt-overlay>*,
    .gt-overlay .goog-te-gadget,
    .gt-overlay .goog-te-gadget-simple,
    .gt-overlay .goog-te-combo,
    .gt-overlay select {
        width: 100% !important;
        height: 100% !important;
        min-height: 100% !important;
        box-sizing: border-box !important;
        cursor: pointer !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
    }

    /* ============================================
       CURRENCY SELECT — normal flow, tanpa overlay
    ============================================ */
    .currency-selector {
        border: none;
        background: transparent;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a5568;
        outline: none;
        cursor: pointer;
        flex: 1;
        min-width: 0;
        /* Hapus appearance bawaan browser lalu tambah panah custom */
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23718096' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.25rem center;
        background-size: 12px;
        padding-right: 1.25rem;
    }

    .currency-selector:focus {
        outline: none;
        box-shadow: none;
    }

    /* ============================================
       USER BUTTON
    ============================================ */
    .user-btn {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    .user-btn:hover {
        opacity: 0.9;
    }

    .user-avatar {
        width: 28px;
        height: 28px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .user-name {
        font-weight: 700;
        font-size: 0.875rem;
    }

    .user-role {
        font-size: 0.7rem;
        opacity: 0.85;
    }

    /* ============================================
       AUTH BUTTONS
    ============================================ */
    .btn-nav {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-login {
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-login:hover {
        background: #667eea;
        color: #fff;
    }

    .btn-register {
        background: var(--primary-gradient);
        color: #fff;
    }

    .btn-register:hover {
        opacity: 0.9;
        color: #fff;
    }

    /* ============================================
       MOBILE TOGGLE
    ============================================ */
    .mobile-toggle {
        width: 40px;
        height: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        border: none;
        background: #f8f9fc;
        border-radius: 8px;
        cursor: pointer;
    }

    .mobile-toggle span {
        display: block;
        width: 22px;
        height: 2px;
        background: #4a5568;
        border-radius: 2px;
    }

    /* ============================================
       OFFCANVAS MOBILE
    ============================================ */
    .offcanvas {
        width: 300px !important;
    }

    /* Tool-box di mobile: tinggi auto agar widget Google bisa lebih tinggi */
    .mobile-tools .tool-box {
        height: auto;
        min-height: 44px;
        border-radius: 10px;
        padding: 8px 12px;
    }

    .tools-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ============================================
       MOBILE NAV
    ============================================ */
    .mobile-nav {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .mobile-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        text-decoration: none;
        color: #4a5568;
        font-weight: 600;
        border-radius: 10px;
        transition: 0.2s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .mobile-nav-item:hover {
        background: rgba(102, 126, 234, 0.08);
        color: #667eea;
    }

    .mobile-nav-item.active {
        background: var(--primary-gradient);
        color: #fff;
    }

    .mobile-divider {
        height: 1px;
        background: #edf2f7;
        margin: 8px 0;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 991.98px) {
        .brand-icon {
            width: 36px;
            height: 36px;
            font-size: 1.2rem;
        }

        .brand-name {
            font-size: 1.1rem;
        }
    }
</style>

<script>
    @include('layouts.customer.partials.navbar-scripts')
</script>
