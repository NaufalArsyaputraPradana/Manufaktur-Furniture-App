@props([
    'name' => 'search',
    'value' => '',
    'icon' => 'bi-search',
    'iconPosition' => 'left',
    'placeholder' => 'Cari...',
    'clearable' => true,
    'disabled' => false,
    'id' => null,
])

@php
    $id = $id ?? 'search_' . uniqid();
@endphp

<div class="input-group">
    @if($iconPosition === 'left')
        <span class="input-group-text bg-white border-end-0">
            <i class="bi {{ $icon }}"></i>
        </span>
    @endif

    <input 
        type="text" 
        id="{{ $id }}"
        name="{{ $name }}"
        class="form-control border-start-0"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        @if($disabled) disabled @endif
    >

    @if($iconPosition === 'right')
        <span class="input-group-text bg-white border-start-0">
            <i class="bi {{ $icon }}"></i>
        </span>
    @endif

    @if($clearable)
        <button 
            class="btn btn-outline-secondary" 
            type="button"
            id="{{ $id }}_clear"
            onclick="clearSearch('{{ $id }}')"
            style="display: none;"
        >
            <i class="bi bi-x-circle"></i>
        </button>
    @endif
</div>

<script>
    const searchInput = document.getElementById('{{ $id }}');
    const clearBtn = document.getElementById('{{ $id }}_clear');

    // Show/hide clear button
    function toggleClearButton() {
        if (clearBtn) {
            clearBtn.style.display = searchInput.value.trim() ? 'block' : 'none';
        }
    }

    // Clear search
    function clearSearch(inputId) {
        const input = document.getElementById(inputId);
        input.value = '';
        input.focus();
        toggleClearButton();
    }

    // Initial state
    if (searchInput.value.trim()) {
        toggleClearButton();
    }

    // On input change
    searchInput.addEventListener('input', toggleClearButton);
</script>

<style>
    .input-group .form-control {
        border-left: none;
    }

    .input-group .form-control:focus {
        border-color: #86b7fe;
        box-shadow: none;
    }

    .input-group .input-group-text {
        border: 1px solid #dee2e6;
        border-right: none;
    }

    .input-group .input-group-text.border-start-0 {
        border-left: none;
    }

    .input-group .input-group-text.border-end-0 {
        border-right: none;
    }
</style>
