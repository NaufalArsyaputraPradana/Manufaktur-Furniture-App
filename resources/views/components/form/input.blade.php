@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'readonly' => false,
    'hint' => null,
])

<div class="form-group mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        {{ $attributes }}
    >

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($hint)
        <small class="form-text text-muted d-block mt-1">{{ $hint }}</small>
    @endif
</div>
