@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'errors' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'class' => '',
    'options' => [], // For select/radio/checkbox
    'rows' => 3, // For textarea
])

@php
    // Check if field has error
    $hasError = $errors && $errors->has($name);
    $errorMessage = $hasError ? $errors->first($name) : null;

    // Get value from old input or prop
    $fieldValue = old($name, $value);

    // Determine input class
    $inputClass = 'form-control rounded-3 ' . ($hasError ? 'is-invalid' : '');
@endphp

<div class="mb-3">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="form-label fw-medium text-dark">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-label="Field required">*</span>
            @endif
        </label>
    @endif

    {{-- Text Input, Email, Password, Number, URL --}}
    @if (in_array($type, ['text', 'email', 'password', 'number', 'url', 'tel', 'date', 'time', 'datetime-local']))
        <input 
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }} {{ $class }}"
            value="{{ $fieldValue }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            @if ($readonly) readonly @endif
            aria-label="{{ $label ?? $name }}"
            {{ $attributes }}>
    @endif

    {{-- Select Dropdown --}}
    @if ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }} {{ $class }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            aria-label="{{ $label ?? $name }}"
            {{ $attributes }}>
            <option value="" disabled {{ !$fieldValue ? 'selected' : '' }}>
                {{ $placeholder ?? 'Pilih ' . ($label ?? $name) }}
            </option>
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ $fieldValue == $optValue ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
    @endif

    {{-- Textarea --}}
    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }} {{ $class }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            @if ($readonly) readonly @endif
            aria-label="{{ $label ?? $name }}"
            {{ $attributes }}>{{ $fieldValue }}</textarea>
    @endif

    {{-- Checkbox --}}
    @if ($type === 'checkbox')
        <div class="form-check mt-2">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                class="form-check-input rounded {{ $hasError ? 'is-invalid' : '' }}"
                value="{{ $fieldValue ?? '1' }}"
                @if ($fieldValue) checked @endif
                @if ($disabled) disabled @endif
                aria-label="{{ $label ?? $name }}"
                {{ $attributes }}>
            @if ($label)
                <label class="form-check-label" for="{{ $name }}">
                    {{ $label }}
                </label>
            @endif
        </div>
    @endif

    {{-- Radio Buttons --}}
    @if ($type === 'radio')
        <div class="mt-2">
            @foreach ($options as $optValue => $optLabel)
                <div class="form-check">
                    <input
                        type="radio"
                        id="{{ $name }}_{{ $optValue }}"
                        name="{{ $name }}"
                        class="form-check-input {{ $hasError ? 'is-invalid' : '' }}"
                        value="{{ $optValue }}"
                        @if ($fieldValue == $optValue) checked @endif
                        @if ($disabled) disabled @endif
                        aria-label="{{ $optLabel }}"
                        {{ $attributes }}>
                    <label class="form-check-label" for="{{ $name }}_{{ $optValue }}">
                        {{ $optLabel }}
                    </label>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Help Text --}}
    @if ($help)
        <small class="form-text text-muted d-block mt-2">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            {{ $help }}
        </small>
    @endif

    {{-- Error Message --}}
    @if ($hasError)
        <div class="invalid-feedback d-block mt-2">
            <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
            {{ $errorMessage }}
        </div>
    @endif
</div>
