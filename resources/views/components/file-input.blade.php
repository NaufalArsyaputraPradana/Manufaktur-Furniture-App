@props([
    'name' => '',
    'label' => '',
    'accept' => '',
    'multiple' => false,
    'maxSize' => '5MB',
    'preview' => false,
    'previewUrl' => null,
    'previewAlt' => 'Preview',
    'helpText' => '',
    'error' => null,
    'disabled' => false,
    'id' => null,
])

@php
    $id = $id ?? 'file_' . uniqid();
    $hasError = !empty($error);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label fw-bold">
            {{ $label }}
            @if($maxSize)
                <small class="text-muted">(Max {{ $maxSize }})</small>
            @endif
        </label>
    @endif

    <!-- Drag-drop zone -->
    <div class="position-relative">
        <div 
            class="border-2 border-dashed rounded p-4 text-center cursor-pointer transition-all"
            id="{{ $id }}_dropzone"
            style="border-color: #dee2e6; background-color: #f8f9fa;"
            onmouseover="this.style.borderColor = '#0d6efd'; this.style.backgroundColor = '#e7f1ff';"
            onmouseout="this.style.borderColor = '#dee2e6'; this.style.backgroundColor = '#f8f9fa';"
        >
            <div class="mb-3">
                <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 2.5rem;"></i>
            </div>
            <p class="mb-2">
                <strong>Klik untuk upload atau drag file ke sini</strong>
            </p>
            <p class="text-muted small mb-0">
                @if($accept)
                    Tipe file: {{ str_replace(',', ', ', $accept) }}
                @else
                    Semua jenis file didukung
                @endif
            </p>

            <!-- Hidden file input -->
            <input 
                type="file" 
                id="{{ $id }}"
                name="{{ $name }}"
                @if($multiple) multiple @endif
                @if($accept) accept="{{ $accept }}" @endif
                @if($disabled) disabled @endif
                class="d-none"
                onchange="handleFileChange(this)"
            >
        </div>

        <!-- Error message -->
        @if($hasError)
            <div class="alert alert-danger mt-2 mb-0 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <small>{{ $error }}</small>
            </div>
        @endif

        <!-- Help text -->
        @if($helpText)
            <small class="form-text text-muted d-block mt-2">
                {{ $helpText }}
            </small>
        @endif
    </div>

    <!-- File preview -->
    @if($preview && $previewUrl)
        <div class="mt-3">
            <h6 class="small fw-bold mb-2">
                <i class="bi bi-image"></i> Preview
            </h6>
            <div class="position-relative d-inline-block">
                @if(in_array(pathinfo($previewUrl, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img 
                        src="{{ $previewUrl }}" 
                        alt="{{ $previewAlt }}"
                        class="img-thumbnail"
                        style="max-width: 150px; max-height: 150px; object-fit: cover;"
                    >
                @else
                    <div class="bg-light border rounded p-3 text-center" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                        <div>
                            <i class="bi bi-file-earmark text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted small mb-0 mt-2">File</p>
                        </div>
                    </div>
                @endif
                <!-- Remove button -->
                <button 
                    type="button" 
                    class="btn btn-sm btn-danger position-absolute"
                    style="top: -10px; right: -10px;"
                    onclick="removePreview(this)"
                    title="Hapus file"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Selected files list -->
    <div id="{{ $id }}_files" class="mt-3">
        <!-- Files will be listed here -->
    </div>
</div>

<script>
    // Get elements
    const fileInput = document.getElementById('{{ $id }}');
    const dropzone = document.getElementById('{{ $id }}_dropzone');
    const filesList = document.getElementById('{{ $id }}_files');

    // Click to select files
    dropzone.addEventListener('click', () => fileInput.click());

    // Drag and drop handling
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#0d6efd';
        dropzone.style.backgroundColor = '#e7f1ff';
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#dee2e6';
        dropzone.style.backgroundColor = '#f8f9fa';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#dee2e6';
        dropzone.style.backgroundColor = '#f8f9fa';
        
        // Get dropped files
        const files = e.dataTransfer.files;
        fileInput.files = files;
        handleFileChange(fileInput);
    });

    // File change handler
    function handleFileChange(input) {
        const files = input.files;
        filesList.innerHTML = '';

        if (files.length === 0) {
            return;
        }

        // Create files list
        const listHtml = Array.from(files).map((file, index) => `
            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2 bg-light">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="bi bi-file-earmark text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <div class="small fw-bold text-truncate">${file.name}</div>
                        <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                    </div>
                </div>
                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-danger ms-2"
                    onclick="removeFile(this, ${index})"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `).join('');

        filesList.innerHTML = `
            <div>
                <h6 class="small fw-bold mb-2">
                    <i class="bi bi-check-circle text-success"></i> File Dipilih (${files.length})
                </h6>
                ${listHtml}
            </div>
        `;
    }

    // Remove file from list
    function removeFile(button, index) {
        const files = new DataTransfer();
        const input = document.getElementById('{{ $id }}');
        
        Array.from(input.files).forEach((file, i) => {
            if (i !== index) {
                files.items.add(file);
            }
        });
        
        input.files = files.files;
        handleFileChange(input);
    }

    // Remove preview
    function removePreview(button) {
        button.closest('.position-relative').remove();
        // Reset file input
        fileInput.value = '';
    }
</script>

<style>
    [id*="_dropzone"] {
        cursor: pointer;
    }

    [id*="_dropzone"]:hover {
        transition: all 0.3s ease;
    }

    .img-thumbnail {
        border-radius: 0.375rem;
    }
</style>
