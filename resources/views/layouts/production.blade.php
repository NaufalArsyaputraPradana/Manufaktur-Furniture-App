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
        :root {
            --prod-primary: #2d6a4f;
            --prod-secondary: #1b4332;
            --prod-accent: #52b788;
            --light-bg: #f0faf4;
            --sidebar-width: 250px;
            --header-height: 70px;
            --transition-speed: 0.3s;
            --box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
        }

        body {
            font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-bg);
            color: #3a3a3a;
            overflow-x: hidden;
            margin: 0;
        }

        a { text-decoration: none; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--prod-primary) 0%, var(--prod-secondary) 100%);
            color: #fff;
            z-index: 1040;
            transition: transform var(--transition-speed) ease-in-out;
            overflow-y: auto;
            box-shadow: var(--box-shadow);
            display: flex;
            flex-direction: column;
        }

        /* ===== MAIN WRAPPER ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left var(--transition-speed) ease-in-out;
        }

        /* ===== HEADER ===== */
        .main-header {
            position: sticky;
            top: 0;
            height: var(--header-height);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            z-index: 1030;
        }

        /* ===== CONTENT AREA ===== */
        .main-content {
            flex: 1;
            padding: 2rem;
        }

        /* ===== BACKDROP (MOBILE) ===== */
        .sidebar-backdrop {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed);
        }
        .sidebar-backdrop.show { opacity: 1; visibility: visible; }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* ===== SIDEBAR NAV ===== */
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: rgba(255,255,255,0.82);
            font-size: 0.93rem;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .sidebar-nav-link:hover {
            background: rgba(255,255,255,0.14);
            color: #fff;
        }
        .sidebar-nav-link.active {
            background: rgba(255,255,255,0.25);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .sidebar-nav-link i { font-size: 1.1rem; margin-right: 0.75rem; flex-shrink: 0; }

        .sidebar-section-label {
            font-size: 0.72rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            font-weight: 700;
            padding: 0.25rem 1rem;
            margin-top: 1.25rem;
            margin-bottom: 0.4rem;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
        }
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
        <div class="flex-grow-1 px-3 py-3 d-flex flex-column overflow-auto">
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
            <div class="flex-grow-1"></div>

            <!-- User Info & Logout -->
            <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                <div class="d-flex align-items-center gap-2 px-1 mb-3">
                    <div class="rounded-circle bg-white bg-opacity-25 text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
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
        document.addEventListener('DOMContentLoaded', function () {
            'use strict';

            // --- Sidebar Toggle ---
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                sidebar?.classList.toggle('show');
                backdrop?.classList.toggle('show');
            }
            function closeSidebar() {
                sidebar?.classList.remove('show');
                backdrop?.classList.remove('show');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar?.classList.contains('show')) closeSidebar();
            });

            // --- Real-time Clock ---
            const clockEl = document.getElementById('headerClock');
            if (clockEl) {
                const tick = () => clockEl.textContent = new Date().toLocaleTimeString('id-ID');
                tick();
                setInterval(tick, 1000);
            }

            // --- SweetAlert2 Toast ---
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
            @endif
            @if (session('error'))
                Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
            @endif
            @if (session('warning'))
                Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
            @endif
            @if (session('info'))
                Toast.fire({ icon: 'info', title: "{{ session('info') }}" });
            @endif

            // --- Confirmation Dialogs ---
            document.body.addEventListener('click', function (e) {
                const triggerConfirmation = (selector, config) => {
                    const el = e.target.closest(selector);
                    if (el) {
                        e.preventDefault();
                        const form = el.closest('form');
                        if (form) {
                            Swal.fire({
                                ...config,
                                showCancelButton: true,
                                cancelButtonColor: '#858796',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) form.submit();
                            });
                        }
                    }
                };

                triggerConfirmation('.delete-confirm', {
                    title: 'Apakah Anda yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus!'
                });

                triggerConfirmation('.delete-confirm-logout', {
                    title: 'Keluar dari akun?',
                    text: 'Anda akan logout dari panel produksi.',
                    icon: 'question',
                    confirmButtonColor: '#2d6a4f',
                    confirmButtonText: 'Ya, Keluar'
                });
            });

            // --- Auto Hide Bootstrap Alerts ---
            document.querySelectorAll('.alert-auto').forEach(alertEl => {
                setTimeout(() => {
                    bootstrap.Alert.getOrCreateInstance(alertEl)?.close();
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
