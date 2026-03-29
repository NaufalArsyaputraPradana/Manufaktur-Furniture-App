@props([
    'id' => 'confirmDialog',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin ingin melanjutkan?',
    'buttonText' => 'Konfirmasi',
    'buttonClass' => 'btn-danger',
    'cancelText' => 'Batal',
    'size' => 'md',
])

@php
    // Size class mapping for modal
    $sizeClass = match ($size) {
        'sm' => 'modal-sm',
        'md' => '',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        default => '',
    };
@endphp

{{-- Confirmation Modal Dialog --}}
<div class="modal fade" 
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">
    
    <div class="modal-dialog {{ $sizeClass }} modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            
            {{-- Modal Header --}}
            <div class="modal-header border-0 bg-light rounded-top-4 py-4">
                <h5 class="modal-title fw-bold text-dark" id="{{ $id }}Label">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2" aria-hidden="true"></i>
                    {{ $title }}
                </h5>
                <button type="button"
                    class="btn-close rounded-circle"
                    data-bs-dismiss="modal"
                    aria-label="Tutup dialog"
                    style="width: 2rem; height: 2rem;">
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body px-4 py-4">
                <p class="text-muted mb-0 lh-lg">
                    {{ $message }}
                </p>
                
                {{-- Slot for additional content --}}
                @if ($slot->isNotEmpty())
                    <div class="mt-3">
                        {{ $slot }}
                    </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 bg-light rounded-bottom-4 py-3 px-4 gap-2">
                <button type="button"
                    class="btn btn-secondary rounded-3 fw-medium"
                    data-bs-dismiss="modal"
                    aria-label="{{ $cancelText }}">
                    <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                    {{ $cancelText }}
                </button>

                {{-- Confirm Button (usually red for destructive actions) --}}
                <button type="button"
                    class="btn {{ $buttonClass }} rounded-3 fw-medium confirm-button"
                    id="{{ $id }}_confirm"
                    aria-label="{{ $buttonText }}">
                    <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>
                    {{ $buttonText }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript Helper for Form Submission --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('{{ $id }}_confirm');
        const confirmModal = document.getElementById('{{ $id }}');

        if (confirmBtn && confirmModal) {
            // Handle button click for form submission
            confirmBtn.addEventListener('click', function() {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    // Fallback: dispatch custom event for other handlers
                    const event = new CustomEvent('confirm', { detail: { id: '{{ $id }}' } });
                    document.dispatchEvent(event);
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(confirmModal);
                if (modal) modal.hide();
            });
        }
    });
</script>

{{-- Usage Example:
    
    <!-- Trigger Button -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirm">
        Hapus
    </button>

    <!-- Modal Component -->
    <x-confirm-dialog 
        id="deleteConfirm"
        title="Hapus Pesanan?"
        message="Tindakan ini tidak dapat dibatalkan. Data pesanan akan dihapus selamanya."
        buttonText="Ya, Hapus"
        buttonClass="btn-danger" />
--}}
