<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Produksi') - {{ config('app.name', 'FMS') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        @include('layouts.production.partials.layout-styles')
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- Brand -->
        <div class="d-flex align-items-center gap-2 px-4 py-4 border-bottom border-white border-opacity-10">
            <div class="bg-white bg-opacity-20 p-2 rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                <i class="bi bi-gear-wide-connected text-white fs-5"></i>
            </div>
            <div>
                <span class="text-white fw-bold fs-6 d-block lh-1">FMS Produksi</span>
                <span class="text-white-50" style="font-size:0.72rem;">Panel Staf</span>
            </div>
        </div>

        <!-- Nav -->
        <div class="grow px-3 py-3 d-flex flex-column overflow-auto">
            <nav class="d-flex flex-column gap-1">

                <a href="{{ route('production.dashboard') }}"
                   class="sidebar-nav-link {{ request()->routeIs('production.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section-label">Produksi</div>

                <a href="{{ route('production.monitoring.orders') }}"
                   class="sidebar-nav-link {{ request()->routeIs('production.monitoring.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i> Daftar Order
                </a>

                <a href="{{ route('production.shipping.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('production.shipping.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Monitoring Pengiriman
                </a>

                <a href="{{ route('staff.production.schedules.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('staff.production.schedules.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i> Jadwal Produksi
                </a>

                <a href="{{ route('staff.production.todos.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('staff.production.todos.*') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i> To Do List
                </a>

            </nav>

            <!-- Spacer -->
            <div class="grow"></div>

            <!-- User Info & Logout -->
            <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                <div class="d-flex align-items-center gap-2 px-1 mb-3">
                    <div class="rounded-circle bg-white bg-opacity-25 text-white d-flex align-items-center justify-content-center fw-bold shrink-0"
                         style="width:36px;height:36px;">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white small fw-bold text-truncate">{{ auth()->user()?->name ?? 'Staff' }}</div>
                        <div class="text-white-50" style="font-size:0.72rem;">Staf Produksi</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 delete-confirm-logout">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Backdrop (Mobile) -->
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <header class="main-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none border-0 shadow-sm" id="sidebarToggle" type="button" aria-label="Toggle Sidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">
                    @yield('title', 'Dashboard Produksi')
                </h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Clock -->
                <span class="d-none d-md-inline text-muted small">
                    <i class="bi bi-clock me-1"></i><span id="headerClock"></span>
                </span>
                <!-- Back to Customer Site -->
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-1" title="Kembali ke situs utama">
                    <i class="bi bi-house-door"></i> <span>Situs Utama</span>
                </a>
                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2"
                       id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                             style="width:38px;height:38px;background:var(--prod-primary);">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block lh-1">
                            <span class="d-block fw-bold small text-dark">{{ auth()->user()?->name ?? 'Staff' }}</span>
                            <span class="d-block text-secondary" style="font-size:0.72rem;">Staf Produksi</span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('production.dashboard') }}">
                                <i class="bi bi-speedometer2 fs-5"></i> Dashboard
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger delete-confirm-logout">
                                    <i class="bi bi-box-arrow-right fs-5"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div class="text-muted small">
                    &copy; {{ now()->year }} <strong>FMS</strong> &mdash; Furniture Manufacturing System
                </div>
                <div class="text-muted small">
                    <i class="bi bi-gear-fill text-success me-1"></i> Panel Produksi
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @include('layouts.production.partials.layout-scripts')
    </script>

    @stack('scripts')
</body>

</html>
