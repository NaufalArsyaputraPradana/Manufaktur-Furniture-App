<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'FMS') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom Internal CSS -->
    <style>
        :root {
            /* Colors */
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-bg: #f8f9fc;
            --dark-text: #5a5c69;

            /* Dimensions */
            --sidebar-width: 250px;
            --header-height: 70px;

            /* Effects */
            --transition-speed: 0.3s;
            --box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
        }

        /* ===== GLOBAL SETTINGS ===== */
        body {
            font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            overflow-x: hidden;
            margin: 0;
        }

        a {
            text-decoration: none;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            color: #fff;
            z-index: 1040;
            transition: transform var(--transition-speed) ease-in-out;
            overflow-y: auto;
            box-shadow: var(--box-shadow);
        }

        /* ===== MAIN WRAPPER ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left var(--transition-speed) ease-in-out;
        }

        /* ===== HEADER / NAVBAR ===== */
        .main-header {
            position: sticky;
            top: 0;
            height: var(--header-height);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed);
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar Partial -->
    <div id="sidebar" class="sidebar">
        @include('layouts.admin.sidebar')
    </div>

    <!-- Backdrop untuk Mobile -->
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar Partial -->
        <header class="main-header">
            @include('layouts.admin.navbar')
        </header>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer Partial -->
        @include('layouts.admin.footer')
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="imageModalLabel">Preview Gambar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid"
                        style="max-height:80vh; width:auto; margin:0 auto;">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // --- 1. Sidebar Logic ---
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const closeBtn = document.getElementById('sidebarClose');

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
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

            // Tutup sidebar saat tombol ESC ditekan
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
                    closeSidebar();
                }
            });

            // --- 2. Auto Hide Alert Bootstrap ---
            const alerts = document.querySelectorAll('.alert-auto');
            alerts.forEach(alertEl => {
                setTimeout(() => {
                    const alertInstance = bootstrap.Alert.getOrCreateInstance(alertEl);
                    if (alertInstance) alertInstance.close();
                }, 5000);
            });

            // --- 3. SweetAlert2 Toast Configuration ---
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            // --- 4. Global Flash Messages (Laravel Session) ---
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if (session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{{ session('warning') }}"
                });
            @endif

            // --- 5. Global Action Confirmations ---
            document.body.addEventListener('click', function(e) {

                // Helper function untuk menampilkan konfirmasi SweetAlert
                const triggerConfirmation = (element, config) => {
                    if (e.target.closest(element)) {
                        e.preventDefault();
                        const form = e.target.closest(element).closest('form');

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

                // Trigger untuk Delete Data
                triggerConfirmation('.delete-confirm', {
                    title: 'Apakah Anda yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus!'
                });

                // Trigger untuk Logout
                triggerConfirmation('.delete-confirm-logout', {
                    title: 'Keluar dari akun?',
                    text: 'Anda akan logout dari panel admin.',
                    icon: 'question',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ya, Keluar'
                });
            });
        });

        // Fungsi global untuk membuka modal gambar
        function showImageModal(src, title = 'Preview Gambar') {
            const modalEl = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');
            modalImage.src = src;
            modalTitle.textContent = title;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    </script>

    @stack('scripts')
</body>

</html>
