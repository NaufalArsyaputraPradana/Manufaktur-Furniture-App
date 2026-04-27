<button 
    type="{{ $type ?? 'button' }}" 
    class="{{ $class ?? 'btn btn-primary' }}"
    {{ $attributes->except('class') }}
    @disabled($disabled ?? false)
>
    @if($icon ?? false)
        <i class="{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>
