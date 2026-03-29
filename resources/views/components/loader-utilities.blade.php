{{-- 
    Utility Helper for Loader Integration
    Usage: @include('components.loader-utilities')
--}}

@push('scripts')
    <script>
        // ============================================
        // Loader Utility Functions
        // ============================================

        /**
         * Show loader dengan custom message
         * @param {string} title - Judul loader
         * @param {string} subtitle - Subtitle loader
         */
        window.showLoader = function(title = 'Memproses...', subtitle = 'Mohon tunggu sebentar') {
            if (typeof showPageLoader === 'function') {
                showPageLoader(title, subtitle);
            }
        };

        /**
         * Hide loader dengan optional delay
         * @param {number} delay - Delay dalam milliseconds
         */
        window.hideLoader = function(delay = 300) {
            if (typeof hidePageLoader === 'function') {
                hidePageLoader(delay);
            }
        };

        /**
         * Redirect dengan loader
         * @param {string} url - URL destination
         * @param {string} message - Loading message
         */
        window.redirectWithLoader = function(url, message = 'Memproses...') {
            showLoader(message);
            setTimeout(() => {
                window.location.href = url;
            }, 300);
        };

        /**
         * Submit form dengan loader
         * @param {string} formId - ID form
         * @param {string} message - Loading message
         */
        window.submitFormWithLoader = function(formId, message = 'Memproses...') {
            const form = document.getElementById(formId);
            if (!form) {
                console.warn(`Form dengan ID "${formId}" tidak ditemukan`);
                return;
            }
            
            showLoader(message);
            form.submit();
        };

        /**
         * Fetch dengan loader
         * @param {string} url - URL untuk di-fetch
         * @param {object} options - Fetch options
         * @returns {Promise}
         */
        window.fetchWithLoader = function(url, options = {}) {
            const message = options.message || 'Memproses...';
            const subtitle = options.subtitle || 'Mohon tunggu sebentar';
            const hideDelay = options.hideDelay || 300;

            showLoader(message, subtitle);

            return fetch(url, {
                ...options,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                    ...options.headers
                }
            }).finally(() => {
                hideLoader(hideDelay);
            });
        };

        /**
         * AJAX dengan loader (jQuery compatible)
         * @param {object} settings - jQuery AJAX settings
         */
        window.ajaxWithLoader = function(settings = {}) {
            if (typeof jQuery === 'undefined' || typeof $.ajax === 'undefined') {
                console.warn('jQuery tidak ditemukan. Gunakan fetchWithLoader sebagai alternatif.');
                return;
            }

            const message = settings.loadMessage || 'Memproses...';
            const subtitle = settings.loadSubtitle || 'Mohon tunggu sebentar';

            showLoader(message, subtitle);

            return $.ajax({
                ...settings,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    ...settings.headers
                }
            }).always(() => {
                hideLoader();
            });
        };

        /**
         * Animate scroll dengan loader
         * @param {string} selector - Target selector to scroll to
         */
        window.scrollWithLoader = function(selector, callback) {
            showLoader('Memproses...', 'Sedang scroll ke bagian');
            
            setTimeout(() => {
                const element = document.querySelector(selector);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                    hideLoader(500);
                    if (typeof callback === 'function') {
                        callback();
                    }
                } else {
                    hideLoader();
                    console.warn(`Elemen dengan selector "${selector}" tidak ditemukan`);
                }
            }, 300);
        };

        /**
         * Delay execution dengan loader
         * @param {function} callback - Fungsi yang akan dijalankan
         * @param {number} duration - Durasi loader (ms)
         * @param {string} message - Loading message
         */
        window.executeWithLoader = function(callback, duration = 1000, message = 'Memproses...') {
            showLoader(message);
            
            setTimeout(() => {
                if (typeof callback === 'function') {
                    callback();
                }
                hideLoader();
            }, duration);
        };

        // ============================================
        // Form Auto-Handler
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-bind forms dengan class 'form-with-loader'
            const loaderForms = document.querySelectorAll('form.form-with-loader');
            
            loaderForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const message = form.getAttribute('data-loader-message') || 'Memproses...';
                    const subtitle = form.getAttribute('data-loader-subtitle') || 'Mohon tunggu sebentar';
                    
                    showLoader(message, subtitle);
                });
            });
        });

    </script>
@endpush
