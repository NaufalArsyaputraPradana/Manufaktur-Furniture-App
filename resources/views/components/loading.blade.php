@props([
    'text' => 'Loading...',
    'size' => 'md',
    'overlay' => false,
])

@php
    $spinnerSize = match($size) {
        'sm' => 'spinner-border-sm',
        'lg' => '',
        default => '',
    };
@endphp

@if($overlay)
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50" style="z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border {{ $spinnerSize }} mb-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>{{ $text }}</div>
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'text-center py-4']) }}>
        <div class="spinner-border {{ $spinnerSize }} text-primary mb-2" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="text-muted">{{ $text }}</div>
    </div>
@endif
