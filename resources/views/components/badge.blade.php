@php
    $colorMap = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'in_production' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'on_hold' => 'secondary',
        'cutting' => 'info',
        'assembly' => 'info',
        'sanding' => 'info',
        'finishing' => 'info',
        'quality_control' => 'warning',
        'paid' => 'success',
        'unpaid' => 'warning',
        'dp_paid' => 'info',
        'active' => 'success',
        'inactive' => 'secondary',
        'open' => 'warning',
        'in_progress' => 'primary',
        'resolved' => 'success',
        'closed' => 'secondary',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
    ];
    
    $statusKey = (string)($status ?? 'unknown');
    $color = isset($colorMap[$statusKey]) ? $colorMap[$statusKey] : 'secondary';
    $displayText = $label ?? ucfirst(str_replace('_', ' ', $statusKey));
@endphp

<span class="badge bg-{{ $color }}" {{ $attributes }}>
    {{ $displayText }}
</span>
