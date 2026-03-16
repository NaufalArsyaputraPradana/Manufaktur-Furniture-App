/**
 * SweetAlert2 Helper Functions
 * Global utility functions for consistent SweetAlert2 usage
 */

// Success Toast (Auto-dismiss)
function successToast(message, timer = 3000) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        timer: timer,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        timerProgressBar: true
    });
}

// Error Alert
function errorAlert(message, title = 'Gagal!') {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
}

// Warning Alert
function warningAlert(message, title = 'Perhatian!') {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#f39c12'
    });
}

// Info Alert
function infoAlert(message, title = 'Informasi') {
    Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6'
    });
}

// Delete Confirmation
async function confirmDelete(itemName = 'item ini') {
    const result = await Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus ${itemName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
        cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
        reverseButtons: true
    });
    
    return result.isConfirmed;
}

// General Confirmation
async function confirmAction(title = 'Konfirmasi', text = 'Apakah Anda yakin?', confirmText = 'Ya', cancelText = 'Batal') {
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true
    });
    
    return result.isConfirmed;
}

// Loading Spinner
function showLoading(message = 'Memproses...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Close Loading
function hideLoading() {
    Swal.close();
}

// Custom Alert with HTML
function customAlert(options) {
    return Swal.fire(options);
}

// Input Dialog
async function promptInput(title, inputLabel, inputType = 'text', placeholder = '') {
    const { value: inputValue } = await Swal.fire({
        title: title,
        input: inputType,
        inputLabel: inputLabel,
        inputPlaceholder: placeholder,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda harus mengisi field ini!'
            }
        }
    });
    
    return inputValue;
}

// Select Dialog
async function promptSelect(title, options, placeholder = 'Pilih...') {
    const { value: selectedValue } = await Swal.fire({
        title: title,
        input: 'select',
        inputOptions: options,
        inputPlaceholder: placeholder,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda harus memilih salah satu!'
            }
        }
    });
    
    return selectedValue;
}

// Textarea Dialog
async function promptTextarea(title, inputLabel, placeholder = '') {
    const { value: text } = await Swal.fire({
        title: title,
        input: 'textarea',
        inputLabel: inputLabel,
        inputPlaceholder: placeholder,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda harus mengisi field ini!'
            }
        }
    });
    
    return text;
}

// Success with auto redirect
function successWithRedirect(message, url, timer = 2000) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        timer: timer,
        showConfirmButton: false,
        timerProgressBar: true
    }).then(() => {
        window.location.href = url;
    });
}

// Validation Errors
function showValidationErrors(errors) {
    let errorHtml = '<ul class="text-start mb-0">';
    errors.forEach(error => {
        errorHtml += `<li>${error}</li>`;
    });
    errorHtml += '</ul>';
    
    Swal.fire({
        icon: 'error',
        title: 'Terdapat Kesalahan!',
        html: errorHtml,
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33',
        customClass: {
            htmlContainer: 'text-start'
        }
    });
}

// Make functions globally available
window.successToast = successToast;
window.errorAlert = errorAlert;
window.warningAlert = warningAlert;
window.infoAlert = infoAlert;
window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.customAlert = customAlert;
window.promptInput = promptInput;
window.promptSelect = promptSelect;
window.promptTextarea = promptTextarea;
window.successWithRedirect = successWithRedirect;
window.showValidationErrors = showValidationErrors;
