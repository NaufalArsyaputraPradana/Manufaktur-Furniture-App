{{-- Simple Page Loader Component with Inline Styles --}}
<div id="pageLoader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); display: flex !important; align-items: center; justify-content: center; z-index: 999999 !important; overflow: hidden; opacity: 1; visibility: visible; transition: opacity 0.6s ease-out, visibility 0.6s ease-out; pointer-events: auto;">
    {{-- Background Shapes --}}
    <div style="position: absolute; width: 100%; height: 100%; overflow: hidden;">
        <div style="position: absolute; width: 300px; height: 300px; background: white; top: -50px; left: -50px; border-radius: 50%; opacity: 0.1; animation: loaderFloat 6s ease-in-out infinite;"></div>
        <div style="position: absolute; width: 200px; height: 200px; background: white; bottom: 50px; right: 20px; border-radius: 50%; opacity: 0.1; animation: loaderFloat 6s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; width: 150px; height: 150px; background: white; top: 50%; right: 10%; border-radius: 50%; opacity: 0.1; animation: loaderFloat 6s ease-in-out infinite 2s;"></div>
    </div>

    {{-- Loader Content --}}
    <div style="position: relative; z-index: 10; text-align: center; color: white; display: flex; flex-direction: column; align-items: center; gap: 2rem; animation: loaderSlideUpIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);">
        {{-- Spinner --}}
        <div id="loaderSpinner" style="position: relative; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
            <canvas id="spinnerCanvas" width="120" height="120" style="filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.4));"></canvas>
        </div>

        {{-- Text --}}
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <h4 style="font-size: 1.75rem; font-weight: 700; margin: 0; color: white; letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); animation: loaderFadeInDown 0.8s ease 0.2s both;">Memuat Halaman</h4>
            <p style="font-size: 1rem; color: rgba(255, 255, 255, 0.9); margin: 0; letter-spacing: 0.2px; animation: loaderFadeInUp 0.8s ease 0.4s both;">Mohon tunggu sebentar...</p>
            <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.5rem;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: rgba(255, 255, 255, 0.8); animation: loaderBounce 1.4s ease-in-out infinite 0s;"></span>
                <span style="width: 6px; height: 6px; border-radius: 50%; background: rgba(255, 255, 255, 0.8); animation: loaderBounce 1.4s ease-in-out infinite 0.2s;"></span>
                <span style="width: 6px; height: 6px; border-radius: 50%; background: rgba(255, 255, 255, 0.8); animation: loaderBounce 1.4s ease-in-out infinite 0.4s;"></span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div style="width: 180px; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
            <div style="width: 100%; height: 6px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0, 0, 0, 0.2) inset;">
                <div class="loader-progress-bar" style="height: 100%; background: linear-gradient(90deg, rgba(255, 255, 255, 0.8) 0%, white 50%, rgba(255, 255, 255, 0.8) 100%); background-size: 200% 100%; width: 30%; border-radius: 10px; animation: loaderProgress 2s ease infinite, loaderShimmer 1.5s ease infinite; box-shadow: 0 0 10px rgba(255, 255, 255, 0.6); transition: width 0.3s ease;"></div>
            </div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9); font-weight: 600; font-variant-numeric: tabular-nums;">
                <span id="loadingPercent" style="display: inline-block; min-width: 24px; text-align: right;">0</span>%
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Loader Animations */
        @keyframes loaderFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }

        @keyframes loaderSlideUpIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loaderFadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loaderFadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loaderBounce {
            0%, 100% {
                transform: translateY(0);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-8px);
                opacity: 1;
            }
        }

        @keyframes loaderProgress {
            0% { width: 10%; }
            50% { width: 70%; }
            100% { width: 90%; }
        }

        @keyframes loaderShimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Hide state */
        #pageLoader.hide {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        body.loader-active {
            overflow: hidden !important;
        }

        #spinnerCanvas {
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.3));
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Modern Canvas Spinner Animation
        (function() {
            const canvas = document.getElementById('spinnerCanvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            let animationId;
            let time = 0;

            function drawModernSpinner() {
                const centerX = canvas.width / 2;
                const centerY = canvas.height / 2;

                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.save();

                // Outer rotating circle
                ctx.translate(centerX, centerY);
                ctx.rotate(time * 0.05);

                // Main rotating arc with glow
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 4;
                ctx.lineCap = 'round';
                ctx.shadowColor = 'rgba(255, 255, 255, 0.6)';
                ctx.shadowBlur = 8;
                ctx.beginPath();
                ctx.arc(0, 0, 40, 0, Math.PI * 1.5);
                ctx.stroke();

                ctx.restore();
                ctx.save();

                // Counter-rotating circle (opposite direction)
                ctx.translate(centerX, centerY);
                ctx.rotate(-time * 0.03);

                ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
                ctx.lineWidth = 2;
                ctx.shadowColor = 'rgba(255, 255, 255, 0.3)';
                ctx.shadowBlur = 4;
                ctx.beginPath();
                ctx.arc(0, 0, 50, Math.PI, Math.PI * 1.8);
                ctx.stroke();

                ctx.restore();
                ctx.save();

                // Orbiting dots around the spinner
                ctx.translate(centerX, centerY);
                const dotsCount = 3;
                for (let i = 0; i < dotsCount; i++) {
                    const angle = (time * 0.08) + (i * (Math.PI * 2 / dotsCount));
                    const x = Math.cos(angle) * 55;
                    const y = Math.sin(angle) * 55;

                    // Gradient for dots
                    const dotGradient = ctx.createRadialGradient(x, y, 0, x, y, 4);
                    dotGradient.addColorStop(0, 'rgba(255, 255, 255, 1)');
                    dotGradient.addColorStop(1, 'rgba(255, 255, 255, 0.3)');

                    ctx.fillStyle = dotGradient;
                    ctx.shadowColor = 'rgba(255, 255, 255, 0.8)';
                    ctx.shadowBlur = 6;
                    ctx.beginPath();
                    ctx.arc(x, y, 4, 0, Math.PI * 2);
                    ctx.fill();
                }

                ctx.restore();
                ctx.save();

                // Center pulsing circle
                ctx.translate(centerX, centerY);
                const pulse = Math.sin(time * 0.04) * 2 + 8;
                const centerGradient = ctx.createRadialGradient(0, 0, 0, 0, 0, pulse);
                centerGradient.addColorStop(0, 'rgba(255, 255, 255, 0.8)');
                centerGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

                ctx.fillStyle = centerGradient;
                ctx.beginPath();
                ctx.arc(0, 0, pulse, 0, Math.PI * 2);
                ctx.fill();

                ctx.restore();

                // Update time
                time++;

                // Continue animation
                animationId = requestAnimationFrame(drawModernSpinner);
            }

            // Start animation when page loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', drawModernSpinner);
            } else {
                drawModernSpinner();
            }

            // Store animation ID for cleanup
            window.spinnerAnimationId = animationId;
        })();
    </script>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            const pageLoader = document.getElementById('pageLoader');
            let progressValue = 0;
            let progressInterval = null;

            console.log('[PageLoader] Initialized');

            // Show Loader
            window.showPageLoader = function(message = null, subtitle = null) {
                if (!pageLoader) return console.error('[PageLoader] Element not found');

                if (message) {
                    const titleEl = pageLoader.querySelector('h4');
                    if (titleEl) titleEl.textContent = message;
                }
                if (subtitle) {
                    const subtitleEl = pageLoader.querySelector('p');
                    if (subtitleEl) subtitleEl.textContent = subtitle;
                }

                pageLoader.style.display = 'flex';
                pageLoader.classList.remove('hide');
                document.body.style.overflow = 'hidden';

                progressValue = 40; // Start dari 40% agar match dengan bar visual
                updateProgress(progressValue);
                startProgress();

                console.log('[PageLoader] Show called');
            };

            // Hide Loader
            window.hidePageLoader = function(delay = 600) {
                if (!pageLoader) return console.error('[PageLoader] Element not found');

                setTimeout(() => {
                    clearInterval(progressInterval);
                    updateProgress(100);

                    setTimeout(() => {
                        pageLoader.classList.add('hide');
                        document.body.style.overflow = 'auto';

                        setTimeout(() => {
                            pageLoader.style.display = 'none';
                            progressValue = 0;
                        }, 600);
                    }, 200);
                }, delay);

                console.log('[PageLoader] Hide called');
            };

            // Progress Management
            function startProgress() {
                clearInterval(progressInterval);
                // Update progress lebih sering dengan increment yang lebih kecil
                progressInterval = setInterval(() => {
                    if (progressValue < 90) {
                        // Increment random antara 1-5%
                        const increment = Math.random() * 5 + 1;
                        progressValue += increment;
                        
                        if (progressValue > 90) progressValue = 90;
                        updateProgress(progressValue);
                    }
                }, 200); // Update setiap 200ms untuk animasi lebih smooth
            }

            function updateProgress(value) {
                const progressBar = pageLoader?.querySelector('.loader-progress-bar');
                const progressText = pageLoader?.querySelector('#loadingPercent');

                if (progressBar) {
                    progressBar.style.width = value + '%';
                }
                if (progressText) {
                    progressText.textContent = Math.floor(value);
                }
            }

            // Auto-trigger on Link Click
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                if (link.hasAttribute('data-no-loader')) return;
                if (!link.href || link.href.includes('#')) return;
                if (link.target === '_blank') return;
                if (link.hasAttribute('download')) return;
                if (!link.href.includes(window.location.origin)) return;

                console.log('[PageLoader] Link click - showing loader');
                showPageLoader();
            });

            // Auto-trigger on Form Submit
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.hasAttribute('data-no-loader')) return;
                if (form.method.toUpperCase() === 'GET') return;

                console.log('[PageLoader] Form submit - showing loader');
                showPageLoader('Memproses...', 'Mohon tunggu sebentar');
            });

            // Auto-hide When Page Loads
            if (document.readyState === 'complete') {
                console.log('[PageLoader] Page already loaded - hiding immediately');
                hidePageLoader(300);
            } else {
                window.addEventListener('load', function() {
                    console.log('[PageLoader] Window load event - hiding loader');
                    hidePageLoader(300);
                });
            }

            // Fallback timeout
            setTimeout(() => {
                if (pageLoader && pageLoader.style.display !== 'none' && !pageLoader.classList.contains('hide')) {
                    console.log('[PageLoader] Fallback timeout (3s) - forcing hide');
                    hidePageLoader(0);
                }
            }, 3000);

        })();
    </script>
@endpush
