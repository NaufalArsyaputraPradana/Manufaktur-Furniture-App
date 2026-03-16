@props([
    'percentage' => 0,
    'color' => 'primary',
    'height' => '25px',
    'striped' => false,
    'animated' => false,
    'label' => true,
])

@php
    $classes = "progress-bar bg-{$color}";
    if ($striped) $classes .= ' progress-bar-striped';
    if ($animated) $classes .= ' progress-bar-animated';
    
    $percentage = max(0, min(100, $percentage));
@endphp

<div class="progress" style="height: {{ $height }};">
    <div 
        class="{{ $classes }}" 
        role="progressbar" 
        style="width: {{ $percentage }}%"
        aria-valuenow="{{ $percentage }}" 
        aria-valuemin="0" 
        aria-valuemax="100"
    >
        @if($label)
            <span class="fw-bold">{{ number_format($percentage, 1) }}%</span>
        @endif
    </div>
</div>
