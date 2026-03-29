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

    <style>
        @include('layouts.admin.partials.layout-styles')
    </style>

    @stack('styles')
</head>

<body>
    {{-- Page Loader --}}
    @include('components.page-loader')

    <!-- Sidebar Partial -->
    <div id="sidebar" class="sidebar">
        <x-layouts.admin-sidebar />
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

    {{-- Loader Utilities --}}
    @include('components.loader-utilities')

    <script>
        @include('layouts.admin.partials.layout-scripts')
    </script>

    @stack('scripts')
</body>

</html>
