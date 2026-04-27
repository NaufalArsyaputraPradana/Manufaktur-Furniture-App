@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'placeholder' => null,
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

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-select @error($name) is-invalid @enderror"
        @if($required) required @endif
        {{ $attributes }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $label)
            <option value="{{ $key }}" @selected(old($name, $value) == $key)>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
