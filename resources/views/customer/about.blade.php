@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
    <!-- Hero Section -->
    <section class="hero position-relative about-hero" aria-label="About hero section">

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
                            <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Tentang Kami</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold mb-3 fade-in text-white">Tentang Kami</h1>
                    <p class="lead mb-0 fade-in-up" style="font-size: 1.15rem; opacity: 0.95;">
                        Produsen furniture berkualitas tinggi dengan pengalaman lebih dari 10 tahun mewujudkan ruang impian.
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

    <!-- Company Image Section -->
    <section class="py-5 bg-white" aria-label="Company overview">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="position-relative rounded-modern overflow-hidden shadow-xl mb-5">
                        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1200&h=500&fit=crop"
                            class="img-fluid w-100"
                            alt="Workshop manufaktur furniture UD Bisa dengan peralatan modern dan tim profesional"
                            loading="lazy" style="height: 400px; object-fit: cover;"
                            onerror="this.src='https://via.placeholder.com/1200x400/667eea/ffffff?text=Workshop+Furniture+Manufacturing'">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white"
                            style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <h2 class="h4 mb-1 fw-bold">Workshop Modern Kami</h2>
                            <p class="mb-0 small opacity-75">Fasilitas produksi terintegrasi dengan teknologi terkini di
                                Jepara</p>
                        </div>
                    </div>
                </div>

                <!-- Company Story -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-6">
                        <article class="h-100">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3 shadow-sm">
                                    <i class="bi bi-bookmark-heart-fill fs-4 text-primary" aria-hidden="true"></i>
                                </div>
                                <h2 class="h3 mb-0 fw-bold">Cerita Kami</h2>
                            </div>
                            <p class="text-muted mb-3 lh-lg">
                                Kami adalah perusahaan manufaktur furniture yang berdedikasi untuk menghasilkan produk
                                berkualitas tinggi dengan desain modern dan fungsional. Sejak didirikan pada tahun 2014,
                                kami telah melayani ribuan pelanggan di seluruh Indonesia, mengubah kayu mentah menjadi
                                karya seni fungsional.
                            </p>
                            <p class="text-muted mb-0 lh-lg">
                                Dengan tim <em>craftsman</em> berpengalaman dan teknologi produksi modern, kami mampu
                                menghasilkan
                                furniture custom sesuai kebutuhan dan selera Anda. Setiap produk dibuat dengan perhatian
                                terhadap detail yang tinggi dan mengikuti standar kualitas internasional.
                            </p>
                        </article>
                    </div>
                    <div class="col-lg-6">
                        <article class="h-100 p-4 bg-light rounded-modern border border-light">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3 shadow-sm">
                                    <i class="bi bi-bullseye fs-4 text-success" aria-hidden="true"></i>
                                </div>
                                <h2 class="h3 mb-0 fw-bold">Visi & Misi</h2>
                            </div>
                            <div class="mb-4">
                                <h3 class="h5 fw-bold mb-2 text-dark">Visi</h3>
                                <p class="text-muted mb-0 lh-base">
                                    Menjadi produsen furniture terpercaya dengan standar kualitas internasional dan menjadi
                                    pilihan utama keluarga Indonesia.
                                </p>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold mb-2 text-dark">Misi</h3>
                                <ul class="text-muted mb-0 lh-base ps-3">
                                    <li class="mb-2">Menghasilkan produk berkualitas tinggi dengan material terbaik yang
                                        teruji.</li>
                                    <li class="mb-2">Memberikan layanan prima dan kepuasan maksimal kepada setiap
                                        pelanggan.</li>
                                    <li class="mb-2">Berinovasi tiada henti dalam desain dan teknologi produksi.</li>
                                    <li>Menggunakan material ramah lingkungan dan mendukung ekonomi berkelanjutan.</li>
                                </ul>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-light" aria-label="Company values">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold mb-2">Nilai-Nilai Kami</h2>
                        <p class="text-muted mb-0">Prinsip dasar yang menjadi fondasi dalam setiap karya yang kami ciptakan
                        </p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <article class="card h-100 border-0 shadow-sm hover-lift rounded-modern text-center">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-flex mb-4"
                                        aria-hidden="true">
                                        <i class="bi bi-star-fill fs-1 text-warning"></i>
                                    </div>
                                    <h3 class="h4 mb-3 fw-bold">Kualitas</h3>
                                    <p class="text-muted mb-0 small lh-base">
                                        Kami berkomitmen menghasilkan produk dengan standar kualitas tertinggi menggunakan
                                        material terbaik dan proses produksi yang sangat teliti.
                                    </p>
                                </div>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="card h-100 border-0 shadow-sm hover-lift rounded-modern text-center">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-flex mb-4"
                                        aria-hidden="true">
                                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                                    </div>
                                    <h3 class="h4 mb-3 fw-bold">Kepuasan Pelanggan</h3>
                                    <p class="text-muted mb-0 small lh-base">
                                        Kepuasan Anda adalah prioritas utama kami. Kami memastikan setiap produk dan
                                        layanan yang diberikan melampaui ekspektasi.
                                    </p>
                                </div>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="card h-100 border-0 shadow-sm hover-lift rounded-modern text-center">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-flex mb-4"
                                        aria-hidden="true">
                                        <i class="bi bi-lightbulb-fill fs-1 text-success"></i>
                                    </div>
                                    <h3 class="h4 mb-3 fw-bold">Inovasi</h3>
                                    <p class="text-muted mb-0 small lh-base">
                                        Kami terus berinovasi dalam segi desain maupun teknologi produksi untuk memberikan
                                        solusi furnitur modern yang selalu up-to-date.
                                    </p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-white" aria-label="Company statistics">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Disini juga saya samakan background gradasi ungunya agar konsisten -->
                    <div class="position-relative rounded-modern overflow-hidden shadow-lg stats-banner">
                        <div class="p-5 text-white position-relative" style="z-index: 2;">
                            <div class="text-center mb-5">
                                <h2 class="h3 fw-bold mb-2">Pencapaian Kami</h2>
                                <p class="mb-0 opacity-75">Dipercaya oleh ribuan pelanggan di seluruh Nusantara</p>
                            </div>
                            <div class="row text-center g-4">
                                <div class="col-6 col-md-3">
                                    <div class="stat-item">
                                        <div class="display-4 fw-bold mb-2 text-warning" data-count="10">0</div>
                                        <p class="mb-0 small fw-medium">Tahun Pengalaman</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-item">
                                        <div class="display-4 fw-bold mb-2 text-warning" data-count="5000">0</div>
                                        <p class="mb-0 small fw-medium">Pelanggan Puas</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-item">
                                        <div class="display-4 fw-bold mb-2 text-warning" data-count="50">0</div>
                                        <p class="mb-0 small fw-medium">Craftsman Ahli</p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-item">
                                        <div class="display-4 fw-bold mb-2 text-warning" data-count="100">0</div>
                                        <p class="mb-0 small fw-medium">Desain Eksklusif</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Decorative circles -->
                        <div class="position-absolute rounded-circle bg-white"
                            style="width: 250px; height: 250px; opacity: 0.05; top: -80px; right: -50px; z-index: 1;"
                            aria-hidden="true"></div>
                        <div class="position-absolute rounded-circle bg-white"
                            style="width: 150px; height: 150px; opacity: 0.05; bottom: -30px; left: -30px; z-index: 1;"
                            aria-hidden="true"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-5 py-lg-6 bg-light" aria-label="Call to action">
        <div class="container mb-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center bg-white p-5 rounded-modern shadow-sm border border-light">
                        <h2 class="h3 fw-bold mb-3">Siap Mewujudkan Furniture Impian Anda?</h2>
                        <p class="text-muted mb-4 fs-5">
                            Konsultasikan kebutuhan furniture Anda dengan tim kami. Kami siap membantu mewujudkan desain
                            impian Anda dengan kualitas ekspor terbaik.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('products.index') }}"
                                class="btn btn-primary btn-lg px-4 px-md-5 py-3 rounded-modern shadow-sm fw-bold"
                                aria-label="Lihat katalog produk furniture">
                                <i class="bi bi-shop me-2" aria-hidden="true"></i> Lihat Katalog
                            </a>
                            <a href="{{ route('contact') }}"
                                class="btn btn-outline-primary btn-lg px-4 px-md-5 py-3 rounded-modern fw-bold"
                                aria-label="Hubungi kami untuk konsultasi">
                                <i class="bi bi-envelope me-2" aria-hidden="true"></i> Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* ============================================
                   HERO & SHAPES
                ============================================ */
        .about-hero {
            /* MENGUBAH GRADASI MENJADI UNGU AGAR KONSISTEN DENGAN HOME/CONTACT */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 9rem 0 7rem 0;
            /* Memberi jarak aman untuk fixed navbar */
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        /* ============================================
                   UTILITIES & CARDS
                ============================================ */
        .rounded-modern {
            border-radius: 1rem !important;
        }

        .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .hover-opacity-100 {
            opacity: 1 !important;
        }

        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        /* MENYAMAKAN WARNA STATS BANNER DENGAN TEMA UNGU JUGA */
        .stats-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-item {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .stat-item.visible {
            opacity: 1;
            transform: translateY(0);
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

        /* Image Shimmer Loading Fallback */
        img[loading="lazy"] {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* ============================================
                   RESPONSIVE
                ============================================ */
        @media (max-width: 768px) {
            .about-hero {
                padding: 7rem 0 4rem 0;
            }

            .display-4 {
                font-size: 2.25rem;
            }

            .stat-item .display-4 {
                font-size: 2.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /**
             * Optimized Counter Animation using requestAnimationFrame
             */
            function animateCounter(element, target, duration = 2000) {
                let startTimestamp = null;
                const formatNumber = (num) => target > 1000 ? Math.floor(num).toLocaleString('id-ID') + '+' : Math
                    .floor(num) + '+';

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    // Ease-out function for smoother stopping
                    const easeOutProgress = 1 - Math.pow(1 - progress, 3);
                    const currentVal = easeOutProgress * target;

                    element.textContent = formatNumber(currentVal);

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        element.textContent = formatNumber(target); // Ensure exactly target at the end
                    }
                };

                window.requestAnimationFrame(step);
            }

            // Intersection Observer for triggering stats animation when visible
            const statsObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statItem = entry.target;
                        statItem.classList.add('visible');

                        const counter = statItem.querySelector('[data-count]');
                        if (counter && !counter.classList.contains('animated')) {
                            counter.classList.add('animated');
                            const targetVal = parseInt(counter.getAttribute('data-count'), 10);
                            animateCounter(counter, targetVal);
                        }
                        // Stop observing once animated
                        observer.unobserve(statItem);
                    }
                });
            }, {
                threshold: 0.4
            });

            document.querySelectorAll('.stat-item').forEach(item => {
                statsObserver.observe(item);
            });
        });
    </script>
@endpush
