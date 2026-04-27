@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'rows' => 3,
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

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control @error($name) is-invalid @enderror"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($hint)
        <small class="form-text text-muted d-block mt-1">{{ $hint }}</small>
    @endif
</div>
