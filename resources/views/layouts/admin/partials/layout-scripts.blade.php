@include('layouts.partials.panel-common-scripts')

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('sidebarClose');

    function toggleSidebar() {
        sidebar?.classList.toggle('show');
        backdrop?.classList.toggle('show');
    }

    function closeSidebar() {
        sidebar?.classList.remove('show');
        backdrop?.classList.remove('show');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
            closeSidebar();
        }
    });

    autoHidePanelAlerts(5000);

    const toast = createPanelToast(3000);
    firePanelSessionToasts(toast, {
        success: @json(session('success')),
        error: @json(session('error')),
        warning: @json(session('warning'))
    });

    bindPanelConfirmations({
        logoutText: 'Anda akan logout dari panel admin.',
        logoutConfirmColor: '#4e73df'
    });
});

function showImageModal(src, title = 'Preview Gambar') {
    const modalEl = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('imageModalLabel');
    modalImage.src = src;
    modalTitle.textContent = title;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
