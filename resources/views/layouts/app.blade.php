<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-current-lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="UD Bisa Furniture - Produsen furniture berkualitas tinggi dengan desain modern dan fungsional untuk rumah dan kantor Anda.">
    <meta name="keywords" content="furniture, mebel, kursi, meja, lemari, sofa, furniture custom, furniture Jepara">
    <meta name="author" content="UD Bisa Furniture">
    <meta name="theme-color" content="#667eea">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Home') - UD Bisa Furniture">
    <meta property="og:description"
        content="Produsen furniture berkualitas tinggi dengan desain modern dan fungsional.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <title>@yield('title', 'Home') - {{ config('app.name', 'UD Bisa Furniture') }}</title>

    {{-- Favicon with protocol-relative URL to handle both HTTP and HTTPS --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico', config('app.asset_url')) }}">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @include('layouts.customer.partials.app-styles')
    </style>
    @stack('styles')
</head>

<body>

    <x-layouts.customer-navbar />

    <main id="main-content">
        @yield('content')
    </main>

    @include('layouts.customer.footer')

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/6285290505442?text=Halo%20Bisa%20Furniture%2C%20saya%20ingin%20bertanya"
        class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Hubungi kami via WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Translate Initialization -->
    <script type="text/javascript">
        @include('layouts.customer.partials.app-scripts')
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

    @stack('scripts')
</body>

</html>
