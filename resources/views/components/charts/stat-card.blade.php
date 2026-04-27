@props([
    'title',
    'value',
    'unit' => null,
    'trend' => null,
    'comparison' => null,
    'color' => 'blue',
    'icon' => null,
    'id' => null,
])

@php
    $rawColor = $color;
    $bsColor = match ($rawColor) {
        'blue', 'indigo' => 'primary',
        'green' => 'success',
        'red' => 'danger',
        'yellow', 'amber', 'orange' => 'warning',
        'cyan', 'teal' => 'info',
        'gray', 'slate', 'stone' => 'secondary',
        default => $rawColor,
    };
@endphp

<div {{ $attributes->class('bg-white rounded border shadow-sm p-3 p-md-4 border-start border-' . $bsColor)->merge(['style' => 'border-left-width: .25rem;']) }}>
    <div class="d-flex align-items-start justify-content-between">
        <div class="flex-grow-1">
            <p class="text-muted small mb-2">{{ $title }}</p>
            <h4 class="fw-bold text-dark mb-2 fs-5" @if ($id) id="{{ $id }}" @endif>
                {{ $value }}
                @if ($unit)
                    <span class="text-muted fs-6">{{ $unit }}</span>
                @endif
            </h4>
            @if ($trend)
                <div class="d-flex align-items-center {{ $trend > 0 ? 'text-success' : 'text-danger' }} small">
                    <i class="bi bi-{{ $trend > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    <span class="fw-semibold">
                        {{ abs($trend) }}% {{ $trend > 0 ? 'increase' : 'decrease' }}
                    </span>
                </div>
            @endif
        </div>
        @if ($icon)
            <div class="flex-shrink-0 p-2 p-md-3 bg-{{ $bsColor }} bg-opacity-10 rounded d-none d-sm-block">
                <svg style="width:24px;height:24px" class="text-{{ $bsColor }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    {{ $icon }}
                </svg>
            </div>
        @endif
    </div>
    @if ($comparison)
        <p class="mt-3 pt-3 border-top text-muted small mb-0">
            {{ $comparison }}
        </p>
    @endif
</div>
