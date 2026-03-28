document.addEventListener('DOMContentLoaded', function() {
    const translateEl = document.getElementById('google_translate_element');
    const mobileSlot = document.getElementById('translate-slot-mobile');
    const desktopWrapper = document.getElementById('translate-wrapper-desktop');
    const mobileMenuEl = document.getElementById('mobileMenu');

    if (translateEl && mobileSlot && desktopWrapper && mobileMenuEl) {
        mobileMenuEl.addEventListener('show.bs.offcanvas', function() {
            mobileSlot.appendChild(translateEl);
        });

        mobileMenuEl.addEventListener('hidden.bs.offcanvas', function() {
            desktopWrapper.appendChild(translateEl);
        });
    }

    const navbar = document.querySelector('.navbar-modern');
    if (navbar) {
        window.addEventListener('scroll', function() {
            navbar.style.boxShadow = window.scrollY > 20
                ? '0 10px 30px rgba(0,0,0,0.1)'
                : '0 2px 10px rgba(0,0,0,0.05)';
        }, {
            passive: true
        });
    }
});
