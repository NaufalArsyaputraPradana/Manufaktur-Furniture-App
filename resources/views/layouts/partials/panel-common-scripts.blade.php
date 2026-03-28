function createPanelToast(timer = 3000) {
    return Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

function firePanelSessionToasts(toastInstance, messages = {}) {
    Object.entries(messages).forEach(([icon, message]) => {
        if (message) {
            toastInstance.fire({ icon, title: message });
        }
    });
}

function bindPanelConfirmations(options = {}) {
    const baseOptions = {
        cancelButtonColor: '#858796',
        cancelButtonText: 'Batal',
        reverseButtons: true
    };

    document.body.addEventListener('click', function(e) {
        const triggerConfirmation = (selector, config) => {
            const target = e.target.closest(selector);
            if (!target) return;

            e.preventDefault();
            const form = target.closest('form');
            if (!form) return;

            Swal.fire({
                ...baseOptions,
                ...config
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        };

        triggerConfirmation('.delete-confirm', {
            title: 'Apakah Anda yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            confirmButtonColor: '#e74a3b',
            confirmButtonText: 'Ya, Hapus!'
        });

        triggerConfirmation('.delete-confirm-logout', {
            title: 'Keluar dari akun?',
            text: options.logoutText || 'Anda akan logout dari panel.',
            icon: 'question',
            confirmButtonColor: options.logoutConfirmColor || '#4e73df',
            confirmButtonText: 'Ya, Keluar'
        });
    });
}

function autoHidePanelAlerts(timeout = 5000) {
    document.querySelectorAll('.alert-auto').forEach((alertEl) => {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertEl)?.close();
        }, timeout);
    });
}
