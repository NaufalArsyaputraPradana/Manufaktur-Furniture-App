{{-- Page Loader Component --}}
<div id="pageLoader" class="page-loader" style="display: none;">
    <div class="loader-container">
        <div class="loader-content">
            {{-- Animated Logo/Spinner --}}
            <div class="loader-spinner">
                <svg class="spinner-svg" viewBox="0 0 50 50">
                    <circle class="spinner-circle" cx="25" cy="25" r="20" fill="none" stroke-width="2"></circle>
                </svg>
                <div class="spinner-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

            {{-- Loading Text --}}
            <div class="loader-text">
                <h4 class="loader-title">Memproses...</h4>
                <p class="loader-subtitle">Mohon tunggu sebentar</p>
            </div>

            {{-- Progress Bar --}}
            <div class="loader-progress">
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar progress-bar-animated" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* ============================================
           PAGE LOADER STYLES
           ============================================ */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.95) 0%, rgba(35, 74, 190, 0.95) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .page-loader.hide {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                pointer-events: none;
            }
        }

        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loader-content {
            text-align: center;
            color: white;
        }

        /* ============================================
           SPINNER STYLES
           ============================================ */
        .loader-spinner {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
        }

        .spinner-svg {
            width: 100%;
            height: 100%;
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-circle {
            stroke: rgba(255, 255, 255, 0.8);
            stroke-dasharray: 125.6;
            stroke-dashoffset: 0;
            animation: dash 1.5s ease-in-out infinite;
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 125.6;
                stroke-dashoffset: 0;
            }
            50% {
                stroke-dasharray: 31.4;
                stroke-dashoffset: -94.2;
            }
            100% {
                stroke-dasharray: 125.6;
                stroke-dashoffset: -125.6;
            }
        }

        {{-- Bouncing Dots Animation --}}
        .spinner-dots {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 8px;
        }

        .spinner-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .spinner-dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .spinner-dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        .spinner-dots span:nth-child(3) {
            animation-delay: 0;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ============================================
           LOADER TEXT
           ============================================ */
        .loader-text {
            margin-bottom: 2rem;
        }

        .loader-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: white;
            letter-spacing: 0.5px;
        }

        .loader-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0;
            letter-spacing: 0.3px;
        }

        /* ============================================
           PROGRESS BAR
           ============================================ */
        .loader-progress {
            width: 120px;
            margin-top: 1.5rem;
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .progress-bar {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 1) 50%, rgba(255, 255, 255, 0.8) 100%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 576px) {
            .loader-spinner {
                width: 60px;
                height: 60px;
                margin-bottom: 1.5rem;
            }

            .loader-title {
                font-size: 1.25rem;
            }

            .loader-subtitle {
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            const pageLoader = document.getElementById('pageLoader');

            // ============================================
            // Show Loader on Page Transition
            // ============================================
            window.showPageLoader = function(message = null, subtitle = null) {
                if (!pageLoader) return;
                
                // Update text if provided
                if (message) {
                    const titleEl = pageLoader.querySelector('.loader-title');
                    if (titleEl) titleEl.textContent = message;
                }
                if (subtitle) {
                    const subtitleEl = pageLoader.querySelector('.loader-subtitle');
                    if (subtitleEl) subtitleEl.textContent = subtitle;
                }

                pageLoader.style.display = 'flex';
                pageLoader.classList.remove('hide');
            };

            // ============================================
            // Hide Loader
            // ============================================
            window.hidePageLoader = function(delay = 300) {
                if (!pageLoader) return;
                
                setTimeout(() => {
                    pageLoader.classList.add('hide');
                    setTimeout(() => {
                        pageLoader.style.display = 'none';
                    }, 500);
                }, delay);
            };

            // ============================================
            // Auto Show on Link Click
            // ============================================
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                // Skip if link has data-no-loader attribute
                if (link.hasAttribute('data-no-loader')) return;

                // Skip if link has no href or is just an anchor
                if (!link.href || link.href.includes('#')) return;

                // Skip if it's an external link
                if (link.target === '_blank' || link.rel.includes('external')) return;

                // Skip if it's a download link
                if (link.hasAttribute('download')) return;

                // Skip if it's not the same origin
                if (!link.href.includes(window.location.origin)) return;

                // Show loader for internal navigation
                showPageLoader();
            });

            // ============================================
            // Auto Show on Form Submit
            // ============================================
            document.addEventListener('submit', function(e) {
                const form = e.target;

                // Skip if form has data-no-loader attribute
                if (form.hasAttribute('data-no-loader')) return;

                // Skip if form method is GET (usually just filters)
                if (form.method.toUpperCase() === 'GET') return;

                showPageLoader('Memproses...', 'Mohon tunggu sebentar');
            });

            // ============================================
            // Hide Loader When Page Fully Loaded
            // ============================================
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hidePageLoader);
            } else {
                hidePageLoader();
            }

            // Fallback: Hide after 5 seconds if stuck
            setTimeout(() => {
                if (pageLoader && pageLoader.style.display !== 'none') {
                    hidePageLoader(0);
                }
            }, 5000);

        })();
    </script>
@endpush
