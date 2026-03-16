<footer class="footer-section bg-dark text-white mt-auto" role="contentinfo" aria-label="Situs Footer">

    {{-- Main Footer Content --}}
    <div class="container py-4 py-md-5">
        <div class="row g-4">

            {{-- Company Info --}}
            <div class="col-lg-3 col-md-6">
                <div class="footer-brand mb-3">
                    <div class="footer-logo-icon" aria-hidden="true">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">UD Bisa Furniture</h5>
                        <small class="text-white-50">Quality First, Always</small>
                    </div>
                </div>
                <p class="text-white-50 mb-4 small lh-base">
                    Produsen furniture berkualitas tinggi dengan desain modern dan fungsional. Berlokasi di Jepara,
                    pusat furniture terbaik Indonesia dengan standar ekspor.
                </p>
                <div class="social-links">
                    <h6 class="fw-bold mb-3 small text-uppercase ls-1">Ikuti Kami</h6>
                    <div class="d-flex gap-2">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer"
                            class="footer-social-btn" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                            class="footer-social-btn" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://wa.me/6285290505442" target="_blank" rel="noopener noreferrer"
                            class="footer-social-btn" aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="fw-bold mb-3 small text-uppercase ls-1">Tautan Cepat</h6>
                <ul class="list-unstyled footer-links mb-0">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-white-50 text-decoration-none small">
                            <i class="bi bi-chevron-right me-1 small"></i> Beranda
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('products.index') }}" class="text-white-50 text-decoration-none small">
                            <i class="bi bi-chevron-right me-1 small"></i> Katalog
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('about') }}" class="text-white-50 text-decoration-none small">
                            <i class="bi bi-chevron-right me-1 small"></i> Tentang Kami
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('contact') }}" class="text-white-50 text-decoration-none small">
                            <i class="bi bi-chevron-right me-1 small"></i> Kontak
                        </a>
                    </li>
                    @auth
                        @if (in_array(auth()->user()?->role?->name, ['customer', 'admin']))
                            <li class="mb-2">
                                <a href="{{ route('customer.orders.index') }}"
                                    class="text-white-50 text-decoration-none small">
                                    <i class="bi bi-chevron-right me-1 small"></i> Pesanan Saya
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-3 col-md-6 col-6">
                <h6 class="fw-bold mb-3 small text-uppercase ls-1">Hubungi Kami</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-primary me-2 mt-1"></i>
                        <div>
                            <div class="text-white fw-semibold small">Alamat Workshop</div>
                            <address class="text-white-50 mb-0 small">
                                Kecapi RT 28/RW 05, Kec. Tahunan<br>
                                Kabupaten Jepara, Jawa Tengah
                            </address>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-whatsapp text-primary me-2 mt-1"></i>
                        <div>
                            <div class="text-white fw-semibold small">WhatsApp Admin</div>
                            <a href="https://wa.me/6285290505442" target="_blank" rel="noopener noreferrer"
                                class="text-white-50 text-decoration-none small">
                                0852-9050-5442
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Business Hours --}}
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3 small text-uppercase ls-1">Jam Operasional</h6>
                <div class="op-hours-container mb-4">
                    @php
                        $schedules = [
                            ['days' => 'Senin - Jumat', 'hours' => '08:00 - 17:00', 'class' => 'bg-success'],
                            ['days' => 'Sabtu', 'hours' => '08:00 - 14:00', 'class' => 'bg-warning text-dark'],
                            ['days' => 'Minggu', 'hours' => 'TUTUP', 'class' => 'bg-danger'],
                        ];
                    @endphp
                    @foreach ($schedules as $s)
                        <div
                            class="d-flex justify-content-between align-items-center op-hour-row px-3 py-2 rounded-3 mb-2">
                            <span class="small fw-medium">{{ $s['days'] }}</span>
                            <span class="badge {{ $s['class'] }}">{{ $s['hours'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- CTA Card --}}
                <div class="p-3 rounded-4 footer-cta-card">
                    <h6 class="fw-bold mb-1 text-white small">
                        <i class="bi bi-chat-dots-fill me-1"></i> Konsultasi Gratis?
                    </h6>
                    <p class="text-white-50 mb-3 extra-small">
                        Diskusikan kebutuhan furniture custom Anda dengan ahlinya sekarang.
                    </p>
                    <a href="https://wa.me/6285290505442?text=Halo%20UD%20Bisa%20Furniture%2C%20saya%20ingin%20konsultasi"
                        target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm w-100 fw-bold py-2">
                        <i class="bi bi-whatsapp me-2"></i> Kirim Pesan
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="footer-bottom py-3">
        <div class="container">
            <div class="row align-items-center g-2">
                <div class="col-md-6 text-center text-md-start">
                    <small class="text-white-50">
                        &copy; {{ date('Y') }} <strong class="text-white">UD Bisa Furniture</strong>. Hak Cipta
                        Dilindungi.
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-white-50">
                        Handcrafted with <i class="bi bi-heart-fill text-danger mx-1"></i> in Jepara
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Back to Top Button --}}
    <button id="backToTop" class="back-to-top-btn shadow-lg" aria-label="Kembali ke atas">
        <i class="bi bi-arrow-up"></i>
    </button>

</footer>

<style>
    /* UTILITIES */
    .ls-1 {
        letter-spacing: 1px;
    }

    .extra-small {
        font-size: 0.75rem;
    }

    /* BRAND LOGO */
    .footer-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .footer-logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* LINKS HOVER */
    .footer-links a {
        transition: all 0.3s ease;
    }

    .footer-links a:hover {
        color: #fff !important;
        transform: translateX(5px);
    }

    /* SOCIAL BUTTONS */
    .footer-social-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.3s;
    }

    .footer-social-btn:hover {
        background: #667eea;
        border-color: #667eea;
        transform: translateY(-3px);
        color: #fff;
    }

    /* OPERATIONAL HOURS */
    .op-hour-row {
        background: rgba(255, 255, 255, 0.05);
    }

    .op-hour-row .badge {
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* CTA CARD */
    .footer-cta-card {
        background: rgba(102, 126, 234, 0.1);
        border: 1px solid rgba(102, 126, 234, 0.2);
    }

    /* BOTTOM BAR */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* BACK TO TOP */
    .back-to-top-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        border-radius: 50%;
        display: none;
        /* Inisial hidden */
        align-items: center;
        justify-content: center;
        z-index: 1030;
        transition: 0.3s;
        font-size: 1.2rem;
    }

    .back-to-top-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    @media (max-width: 768px) {
        .back-to-top-btn {
            width: 40px;
            height: 40px;
            bottom: 20px;
            right: 20px;
        }
    }
</style>

<script>
    (function() {
        'use strict';
        const backToTop = document.getElementById('backToTop');

        if (backToTop) {
            window.addEventListener('scroll', function() {
                // Tampilkan tombol saat scroll > 400px
                if (window.pageYOffset > 400) {
                    backToTop.style.display = 'flex';
                } else {
                    backToTop.style.display = 'none';
                }
            }, {
                passive: true
            });

            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    })();
</script>
