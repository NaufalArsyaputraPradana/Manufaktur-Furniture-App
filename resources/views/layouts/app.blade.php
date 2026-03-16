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

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        /* ============================================
           CSS VARIABLES & BASE
        ============================================ */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --bg-light: #f8f9fa;
            --border-color: #e2e8f0;
            --transition: 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            top: 0 !important;
            /* Fix Google Translate shift */
        }

        main {
            flex: 1;
        }

        /* ============================================
           UTILITY CLASSES
        ============================================ */
        .rounded-modern {
            border-radius: 12px !important;
        }

        .text-gradient {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* ============================================
           GOOGLE TRANSLATE UI CLEANUP
        ============================================ */
        .goog-logo-link,
        .goog-te-gadget span {
            display: none !important;
        }

        .goog-te-gadget {
            color: transparent !important;
            font-size: 0 !important;
        }

        .skiptranslate,
        .goog-te-banner-frame {
            display: none !important;
        }

        body {
            top: 0 !important;
            position: relative !important;
        }

        .translated-ltr,
        .translated-rtl {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .translated-ltr .navbar-modern,
        .translated-rtl .navbar-modern {
            top: 0 !important;
            margin-top: 0 !important;
        }

        /* ============================================
           WHATSAPP FLOAT BUTTON
        ============================================ */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #25D366;
            color: #fff;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1040;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.1) rotate(5deg);
            background: #128C7E;
            color: #fff;
        }

        @media (max-width: 576px) {
            .whatsapp-float {
                bottom: 20px;
                right: 20px;
                width: 48px;
                height: 48px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    @include('layouts.customer.navbar')

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
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

    <!-- Currency Converter Engine -->
    <script>
        const CurrencyConverter = {
            rateCache: {},
            availableCurrencies: [{
                    code: 'IDR',
                    label: '🇮🇩 IDR',
                    locale: 'id-ID'
                },
                {
                    code: 'USD',
                    label: '🇺🇸 USD',
                    locale: 'en-US'
                },
                {
                    code: 'EUR',
                    label: '🇪🇺 EUR',
                    locale: 'de-DE'
                },
                {
                    code: 'GBP',
                    label: '🇬🇧 GBP',
                    locale: 'en-GB'
                },
                {
                    code: 'SAR',
                    label: '🇸🇦 SAR',
                    locale: 'ar-SA'
                },
                {
                    code: 'AED',
                    label: '🇦🇪 AED',
                    locale: 'ar-AE'
                },
                {
                    code: 'MYR',
                    label: '🇲🇾 MYR',
                    locale: 'ms-MY'
                },
                {
                    code: 'SGD',
                    label: '🇸🇬 SGD',
                    locale: 'en-SG'
                },
                {
                    code: 'AUD',
                    label: '🇦🇺 AUD',
                    locale: 'en-AU'
                },
                {
                    code: 'JPY',
                    label: '🇯🇵 JPY',
                    locale: 'ja-JP'
                },
                {
                    code: 'CNY',
                    label: '🇨🇳 CNY',
                    locale: 'zh-CN'
                },
                {
                    code: 'KRW',
                    label: '🇰🇷 KRW',
                    locale: 'ko-KR'
                },
                {
                    code: 'TRY',
                    label: '🇹🇷 TRY',
                    locale: 'tr-TR'
                }
            ],

            formatCurrency: function(amount, currencyCode) {
                const config = this.availableCurrencies.find(c => c.code === currencyCode) || {
                    locale: 'id-ID'
                };
                try {
                    return new Intl.NumberFormat(config.locale, {
                        style: 'currency',
                        currency: currencyCode,
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                } catch (e) {
                    return `${currencyCode} ${amount.toLocaleString()}`;
                }
            },

            getExchangeRate: async function(from, to) {
                const cacheKey = `${from}_${to}`;
                if (this.rateCache[cacheKey]) return this.rateCache[cacheKey];

                try {
                    const res = await fetch(`https://open.er-api.com/v6/latest/${from}`);
                    const data = await res.json();
                    if (data.result === 'success' && data.rates[to]) {
                        this.rateCache[cacheKey] = data.rates[to];
                        return data.rates[to];
                    }
                    throw new Error('Rate not found');
                } catch (err) {
                    console.error('Currency API Error:', err);
                    return null;
                }
            },

            updatePrices: async function(targetCurrency) {
                const priceElements = document.querySelectorAll('[data-price]');
                if (priceElements.length === 0) return;

                // Loading State
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Swal.fire({
                    title: 'Mengonversi Harga...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    for (const el of priceElements) {
                        const basePrice = parseFloat(el.getAttribute('data-price'));
                        const baseCurrency = el.getAttribute('data-currency') || 'IDR';

                        if (targetCurrency === baseCurrency) {
                            el.textContent = this.formatCurrency(basePrice, baseCurrency);
                        } else {
                            const rate = await this.getExchangeRate(baseCurrency, targetCurrency);
                            if (rate) {
                                el.textContent = this.formatCurrency(basePrice * rate, targetCurrency);
                            }
                        }
                    }
                    localStorage.setItem('selectedCurrency', targetCurrency);
                    Swal.close();
                    toast.fire({
                        icon: 'success',
                        title: `Mata uang diubah ke ${targetCurrency}`
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Koneksi API bermasalah.'
                    });
                }
            },

            init: function() {
                const selectors = document.querySelectorAll('.currency-selector');
                const savedCurrency = localStorage.getItem('selectedCurrency') || 'IDR';

                selectors.forEach(select => {
                    select.innerHTML = this.availableCurrencies
                        .map(c =>
                            `<option value="${c.code}" ${c.code === savedCurrency ? 'selected' : ''}>${c.label}</option>`
                        )
                        .join('');

                    select.addEventListener('change', (e) => {
                        const val = e.target.value;
                        selectors.forEach(s => s.value = val); // Sync multiple selectors
                        this.updatePrices(val);
                    });
                });

                // Trigger initial conversion if not IDR
                if (savedCurrency !== 'IDR') {
                    setTimeout(() => this.updatePrices(savedCurrency), 500);
                } else {
                    // Just format IDR
                    document.querySelectorAll('[data-price]').forEach(el => {
                        el.textContent = this.formatCurrency(el.getAttribute('data-price'), 'IDR');
                    });
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            CurrencyConverter.init();

            // Auto-hide alerts
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
