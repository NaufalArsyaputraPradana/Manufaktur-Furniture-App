@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => true,
])

@php
    $classes = match($type) {
        'success' => 'alert-success',
        'error', 'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-info',
    };
    
    $icons = [
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'error' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info' => 'bi-info-circle-fill',
    ];
    
    $iconClass = $icons[$type] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => "alert $classes" . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    <div class="d-flex align-items-center">
        @if($icon)
            <i class="bi {{ $iconClass }} me-2 fs-5"></i>
        @endif
        <div class="flex-grow-1">
            {{ $slot }}
        </div>
    </div>
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
