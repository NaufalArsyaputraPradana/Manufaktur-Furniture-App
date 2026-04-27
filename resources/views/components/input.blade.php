@props(['label', 'type' => 'text', 'required' => false, 'error' => null, 'value' => '', 'placeholder' => ''])

<div class="mb-3">
    <label for="{{ $attributes->get('name') }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}"
        class="form-control @if($error) is-invalid @endif"
        id="{{ $attributes->get('name') }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($attributes->get('name'), $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except('class') }}
    >
    @if($error)
        <div class="invalid-feedback d-block">
            {{ $error }}
        </div>
    @elseif($slot->isEmpty() === false)
        <small class="form-text text-muted">
            {!! $slot !!}
        </small>
    @endif
</div>
