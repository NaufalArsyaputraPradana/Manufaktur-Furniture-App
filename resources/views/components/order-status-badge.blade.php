@props([
    'status',
    'payment' => null,
    'isPaid' => false,
    'isDpPaid' => false,
    'size' => 'md',
])

@php
    // Convert Enum to string if needed
    $statusValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
    
    // Determine badge styling based on status and payment state
    $badgeClass = match (true) {
        $statusValue === 'pending' && $isPaid => 'bg-info',
        $statusValue === 'pending' && $isDpPaid => 'bg-warning text-dark',
        $statusValue === 'pending' => 'bg-warning text-dark',
        $statusValue === 'confirmed' => 'bg-info text-dark',
        $statusValue === 'in_production' => 'bg-primary',
        $statusValue === 'completed' => 'bg-success',
        $statusValue === 'cancelled' => 'bg-danger',
        default => 'bg-secondary',
    };

    // Size class mapping
    $sizeClass = match ($size) {
        'sm' => 'badge-sm small',
        'md' => 'fs-6 px-3 py-2',
        'lg' => 'fs-5 px-4 py-3',
        default => 'fs-6 px-3 py-2',
    };

    // Determine status icon and text
    $statusDisplay = match ($statusValue) {
        'pending' => [
            'icon' => $isPaid ? 'bi-cash-coin' : ($isDpPaid ? 'bi-piggy-bank' : 'bi-clock-history'),
            'text' => $isPaid ? 'Menunggu Verifikasi Pembayaran' : ($isDpPaid ? 'DP terverifikasi' : 'Menunggu Pembayaran'),
        ],
        'confirmed' => [
            'icon' => 'bi-check-circle-fill',
            'text' => 'Dikonfirmasi',
        ],
        'in_production' => [
            'icon' => 'bi-gear-fill',
            'text' => 'Dalam Produksi',
        ],
        'completed' => [
            'icon' => 'bi-check-all',
            'text' => 'Selesai',
        ],
        'cancelled' => [
            'icon' => 'bi-x-circle-fill',
            'text' => 'Dibatalkan',
        ],
        default => [
            'icon' => 'bi-question-circle',
            'text' => ucfirst($statusValue),
        ],
    };
@endphp

<span class="badge {{ $badgeClass }} rounded-pill shadow-sm {{ $sizeClass }}"
    role="status"
    aria-label="Status pesanan: {{ $statusDisplay['text'] }}">
    <i class="bi {{ $statusDisplay['icon'] }} me-1" aria-hidden="true"></i>
    {{ $statusDisplay['text'] }}
</span>
