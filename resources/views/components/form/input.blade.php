@props([
    'name',
    'label' => null,
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'value' => '',
])

@php
    $inputId = $attributes->get('id', $name);
    $displayLabel = $label ?? ucfirst(str_replace('_', ' ', $name));
@endphp

<div class="mb-3">
    @if($label !== false)
        <label for="{{ $inputId }}" class="form-label">
            {{ $displayLabel }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $inputId }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($required) required @endif
            placeholder="{{ $placeholder }}"
            rows="3"
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            name="{{ $name }}"
            id="{{ $inputId }}"
            {{ $attributes->merge(['class' => 'form-select' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($required) required @endif
        >
            <option value="">{{ $placeholder ?: "Pilih {$displayLabel}" }}</option>
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
        />
    @endif
    
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
    
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
