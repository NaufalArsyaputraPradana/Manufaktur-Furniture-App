@props([
    'status' => 'secondary',
    'size' => 'md',
    'label' => null,
])

@php
    $classes = match($status) {
        'pending' => 'bg-warning text-dark',
        'confirmed' => 'bg-info text-white',
        'in_production' => 'bg-primary text-white',
        'completed' => 'bg-success text-white',
        'cancelled' => 'bg-danger text-white',
        'paid' => 'bg-success text-white',
        'unpaid' => 'bg-danger text-white',
        'active' => 'bg-success text-white',
        'inactive' => 'bg-secondary text-white',
        'cutting' => 'bg-info text-white',
        'assembly' => 'bg-info text-white',
        'sanding' => 'bg-info text-white',
        'finishing' => 'bg-info text-white',
        'quality_control' => 'bg-warning text-dark',
        default => 'bg-secondary text-white',
    };
    
    $sizeClasses = match($size) {
        'sm' => 'badge-sm',
        'lg' => 'badge-lg fs-6',
        default => '',
    };
    
    $displayLabel = $label ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "badge $classes $sizeClasses"]) }}>
    {{ $slot->isEmpty() ? $displayLabel : $slot }}
</span>
