@extends('layouts.app')

@section('title', 'Hubungi Kami')

@section('content')
    <!-- Hero Section -->
    <section class="hero position-relative contact-hero" aria-label="Contact hero section">
        <!-- Background Pattern -->
        <div class="hero-pattern"></div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center text-white">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb justify-content-center bg-transparent m-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}"
                                    class="text-white text-decoration-none opacity-75 hover-opacity-100 transition-all">Home</a>
                            </li>
                            <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Hubungi Kami</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold mb-3 fade-in text-white">Hubungi Kami</h1>
                    <p class="lead mb-0 fade-in-up" style="font-size: 1.15rem; opacity: 0.95;">
                        Punya pertanyaan atau butuh konsultasi? Tim kami siap membantu Anda.
                    </p>
                </div>
            </div>
        </div>

        <!-- Wave Shape Bottom -->
        <div class="position-absolute bottom-0 start-0 w-100" style="overflow: hidden; line-height: 0; z-index: 1;"
            aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" focusable="false"
                style="height: 80px; width: 100%; display: block;">
                <path fill="#ffffff" fill-opacity="1"
                    d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                </path>
            </svg>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="py-5 bg-white" aria-label="Contact information and form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row g-4">

                        <!-- Contact Info -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100 rounded-modern bg-light">
                                <div class="card-body p-4 p-lg-5">
                                    <h2 class="h4 card-title mb-4 fw-bold">Informasi Kontak</h2>

                                    <!-- Address -->
                                    <div class="mb-4 contact-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 shadow-sm">
                                                    <i class="bi bi-geo-alt-fill text-primary fs-5" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h3 class="h6 fw-bold mb-2">Alamat Workshop</h3>
                                                <p class="text-muted mb-0 small lh-base">
                                                    Kecapi RT 28/RW 05<br>
                                                    Kec. Tahunan<br>
                                                    Kabupaten Jepara, Jawa Tengah
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- WhatsApp -->
                                    <div class="mb-4 contact-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-3 shadow-sm">
                                                    <i class="bi bi-whatsapp text-success fs-5" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h3 class="h6 fw-bold mb-2">WhatsApp</h3>
                                                <p class="mb-0 small">
                                                    <a href="https://wa.me/6285290505442"
                                                        class="text-muted text-decoration-none hover-link fw-medium"
                                                        target="_blank" rel="noopener noreferrer">
                                                        0852-9050-5442
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Working Hours -->
                                    <div class="contact-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 shadow-sm">
                                                    <i class="bi bi-clock-fill text-warning fs-5" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h3 class="h6 fw-bold mb-2">Jam Operasional</h3>
                                                <p class="text-muted mb-0 small lh-base">
                                                    Senin - Jumat: 08:00 - 17:00<br>
                                                    Sabtu: 08:00 - 14:00<br>
                                                    Minggu: Libur
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 opacity-10">

                                    <!-- Social Media -->
                                    <div>
                                        <h3 class="h6 fw-bold mb-3">Ikuti Media Sosial Kami</h3>
                                        <div class="d-flex gap-2">
                                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer"
                                                class="btn btn-primary btn-sm rounded-circle hover-scale shadow-sm d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;" aria-label="Kunjungi Facebook kami">
                                                <i class="bi bi-facebook" aria-hidden="true"></i>
                                            </a>
                                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                                                class="btn btn-danger btn-sm rounded-circle hover-scale shadow-sm d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; background: #E1306C; border-color: #E1306C;"
                                                aria-label="Kunjungi Instagram kami">
                                                <i class="bi bi-instagram" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100 rounded-modern">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3 mb-3 shadow-sm">
                                            <i class="bi bi-send-fill text-primary fs-4" aria-hidden="true"></i>
                                        </div>
                                        <div class="mb-3">
                                            <h2 class="h4 mb-1 fw-bold">Kirim Pesan Cepat</h2>
                                            <p class="text-muted small mb-0">Pesan Anda akan langsung diteruskan ke WhatsApp
                                                Admin kami.</p>
                                        </div>
                                    </div>

                                    <form action="{{ route('contact.submit') }}" method="POST" id="contactForm"
                                        aria-label="Formulir Kontak">
                                        @csrf
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <x-form-input 
                                                    name="name" 
                                                    label="Nama Lengkap"
                                                    type="text"
                                                    placeholder="Contoh: Budi Santoso"
                                                    :value="old('name')"
                                                    :errors="$errors"
                                                    required />
                                            </div>

                                            <div class="col-md-6">
                                                <x-form-input 
                                                    name="email" 
                                                    label="Email"
                                                    type="email"
                                                    placeholder="nama@email.com"
                                                    :value="old('email')"
                                                    :errors="$errors"
                                                    required />
                                            </div>

                                            <div class="col-12">
                                                <x-form-input 
                                                    name="subject" 
                                                    label="Subjek"
                                                    type="text"
                                                    placeholder="Contoh: Tanya Custom Lemari"
                                                    :value="old('subject')"
                                                    :errors="$errors"
                                                    required />
                                            </div>

                                            <div class="col-12">
                                                <x-form-input 
                                                    name="message" 
                                                    label="Detail Pesan"
                                                    type="textarea"
                                                    placeholder="Jelaskan kebutuhan atau pertanyaan Anda di sini..."
                                                    :value="old('message')"
                                                    :errors="$errors"
                                                    rows="5"
                                                    required />
                                                <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Akan dikirim ke WhatsApp.</small>
                                            </div>

                                            <div class="col-12 mt-4 pt-2">
                                                <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                                                    <button type="reset"
                                                        class="btn btn-light border btn-lg px-4 shadow-sm fw-medium">
                                                        <i class="bi bi-arrow-counterclockwise me-1"
                                                            aria-hidden="true"></i> Reset Form
                                                    </button>
                                                    <button type="submit"
                                                        class="btn btn-primary btn-lg px-5 shadow-sm hover-scale fw-bold">
                                                        <i class="bi bi-send-fill me-2" aria-hidden="true"></i> Kirim ke
                                                        WhatsApp
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-5 bg-light" aria-label="Location map">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-4">
                        <h2 class="h3 fw-bold mb-2">
                            <i class="bi bi-geo-alt-fill text-primary me-2" aria-hidden="true"></i> Lokasi Workshop Kami
                        </h2>
                        <p class="text-muted">Kecapi RT 28/RW 05, Kec. Tahunan, Kabupaten Jepara, Jawa Tengah</p>
                    </div>

                    <div class="map-container mb-4 shadow-sm rounded-modern overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.3878862773668!2d110.708962!3d-6.5986221!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e711f81d4b5df01%3A0xbe12ccf8423e4c24!2sBisa%20Furniture!5e0!3m2!1sid!2sid!4v1770945772956!5m2!1sid!2sid"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" title="Lokasi UD Bisa Furniture Jepara"></iframe>
                    </div>

                    <div class="text-center">
                        <a href="https://www.google.com/maps/place/Bisa+Furniture/@-6.5986221,110.708962,17z"
                            target="_blank" rel="noopener noreferrer"
                            class="btn btn-outline-primary btn-lg rounded-modern px-5 fw-bold shadow-sm hover-scale">
                            <i class="bi bi-map me-2" aria-hidden="true"></i> Buka Petunjuk Arah di Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* ============================================
                   HERO SECTION
                ============================================ */
        .contact-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 9rem 0 7rem 0;
            /* Jarak aman untuk fixed navbar */
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 1;
            z-index: 1;
        }

        /* ============================================
                   ANIMATIONS
                ============================================ */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
                   UTILITIES
                ============================================ */
        .rounded-modern {
            border-radius: 1rem !important;
        }

        .hover-opacity-100 {
            opacity: 1 !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .contact-item {
            transition: transform 0.3s ease;
        }

        .contact-item:hover {
            transform: translateX(8px);
        }

        .hover-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .hover-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1.5px;
            bottom: -2px;
            left: 0;
            background-color: var(--bs-primary);
            transition: width 0.3s ease;
        }

        .hover-link:hover {
            color: var(--bs-primary) !important;
        }

        .hover-link:hover::after {
            width: 100%;
        }

        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }

        /* ============================================
                   FORM STYLES
                ============================================ */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
            background-color: #fff;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #667eea;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        #charCount {
            font-weight: 700;
            color: var(--bs-primary);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter for message textarea
            const messageTextarea = document.getElementById('message');
            const charCountSpan = document.getElementById('charCount');

            if (messageTextarea && charCountSpan) {
                const updateCharCount = () => {
                    const currentLength = messageTextarea.value.length;
                    charCountSpan.textContent = currentLength;

                    if (currentLength > 1900) {
                        charCountSpan.style.color = 'var(--bs-danger)';
                    } else if (currentLength > 1500) {
                        charCountSpan.style.color = 'var(--bs-warning)';
                    } else {
                        charCountSpan.style.color = 'var(--bs-primary)';
                    }
                };

                // Initial count & event listener
                updateCharCount();
                messageTextarea.addEventListener('input', updateCharCount);
            }

            // Form validation before sending to WhatsApp
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    const name = document.getElementById('name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const subject = document.getElementById('subject').value.trim();
                    const message = messageTextarea.value.trim();

                    if (!name || !email || !subject || !message) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: 'Mohon isi semua bidang yang diwajibkan.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#667eea'
                        });
                        return false;
                    }

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Email Tidak Valid',
                            text: 'Mohon periksa kembali format alamat email Anda.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#e74a3b'
                        });
                        return false;
                    }

                    // Tampilkan SweetAlert Loading (karena form akan me-redirect)
                    Swal.fire({
                        title: 'Mengarahkan ke WhatsApp...',
                        html: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            }
        });
    </script>
@endpush
