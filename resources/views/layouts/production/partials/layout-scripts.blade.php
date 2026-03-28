@include('layouts.partials.panel-common-scripts')

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

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
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar?.classList.contains('show')) closeSidebar();
    });

    const clockEl = document.getElementById('headerClock');
    if (clockEl) {
        const tick = () => clockEl.textContent = new Date().toLocaleTimeString('id-ID');
        tick();
        setInterval(tick, 1000);
    }

    const toast = createPanelToast(3500);
    firePanelSessionToasts(toast, {
        success: @json(session('success')),
        error: @json(session('error')),
        warning: @json(session('warning')),
        info: @json(session('info'))
    });

    bindPanelConfirmations({
        logoutText: 'Anda akan logout dari panel produksi.',
        logoutConfirmColor: '#2d6a4f'
    });

    autoHidePanelAlerts(5000);
});
