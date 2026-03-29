@props([
    'status',
    'payment' => null,
    'isPaid' => false,
    'isDpPaid' => false,
    'size' => 'md',
])

@php
    // Determine badge styling based on status and payment state
    $badgeClass = match (true) {
        $status === 'pending' && $isPaid => 'bg-info',
        $status === 'pending' && $isDpPaid => 'bg-warning text-dark',
        $status === 'pending' => 'bg-warning text-dark',
        $status === 'confirmed' => 'bg-info text-dark',
        $status === 'in_production' => 'bg-primary',
        $status === 'completed' => 'bg-success',
        $status === 'cancelled' => 'bg-danger',
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
    $statusDisplay = match ($status) {
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
            'text' => ucfirst($status),
        ],
    };
@endphp

<span class="badge {{ $badgeClass }} rounded-pill shadow-sm {{ $sizeClass }}"
    role="status"
    aria-label="Status pesanan: {{ $statusDisplay['text'] }}">
    <i class="bi {{ $statusDisplay['icon'] }} me-1" aria-hidden="true"></i>
    {{ $statusDisplay['text'] }}
</span>
