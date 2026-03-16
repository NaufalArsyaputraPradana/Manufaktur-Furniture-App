@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
    'loading' => false,
    'disabled' => false,
])

@php
    $classes = "btn btn-{$variant}";
    
    $sizeClass = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
    
    $classes .= " {$sizeClass}";
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Loading...
    @else
        @if($icon)
            <i class="bi bi-{{ $icon }} me-1"></i>
        @endif
        {{ $slot }}
    @endif
</button>
