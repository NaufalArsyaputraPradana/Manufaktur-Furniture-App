@props([
    'payment' => null,
    'showAmount' => true,
    'showChannel' => true,
    'showStatus' => true,
    'size' => 'md',
])

@php
    // If no payment, show unpaid status
    if (!$payment) {
        $badgeClass = 'bg-secondary';
        $icon = 'bi-x-circle-fill';
        $text = 'Belum Dibayar';
        $amount = null;
        $channel = null;
    } else {
        // Determine badge styling based on payment status
        $badgeClass = match ($payment->payment_status ?? null) {
            'paid' => 'bg-success',
            'dp_paid' => 'bg-warning text-dark',
            'pending' => 'bg-info',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };

        // Icon and status text
        $statusDisplay = match ($payment->payment_status ?? null) {
            'paid' => [
                'icon' => 'bi-check-circle-fill',
                'text' => 'Pembayaran Penuh',
            ],
            'dp_paid' => [
                'icon' => 'bi-piggy-bank',
                'text' => 'DP Lunas',
            ],
            'pending' => [
                'icon' => 'bi-clock-history',
                'text' => 'Menunggu Pembayaran',
            ],
            'failed' => [
                'icon' => 'bi-x-circle-fill',
                'text' => 'Pembayaran Gagal',
            ],
            'cancelled' => [
                'icon' => 'bi-ban',
                'text' => 'Pembayaran Dibatalkan',
            ],
            default => [
                'icon' => 'bi-question-circle',
                'text' => ucfirst($payment->payment_status ?? 'unknown'),
            ],
        };

        $icon = $statusDisplay['icon'];
        $text = $statusDisplay['text'];
        $amount = $payment->amount ?? null;
        $channel = $payment->payment_channel ?? null;
    }

    // Size class mapping
    $sizeClass = match ($size) {
        'sm' => 'badge-sm small',
        'md' => 'fs-6 px-3 py-2',
        'lg' => 'fs-5 px-4 py-3',
        default => 'fs-6 px-3 py-2',
    };
@endphp

<div class="d-flex align-items-center gap-2">
    {{-- Payment Status Badge --}}
    @if ($showStatus)
        <span class="badge {{ $badgeClass }} rounded-pill shadow-sm {{ $sizeClass }}"
            role="status"
            aria-label="Status pembayaran: {{ $text }}">
            <i class="bi {{ $icon }} me-1" aria-hidden="true"></i>
            {{ $text }}
        </span>
    @endif

    {{-- Payment Amount (Optional) --}}
    @if ($showAmount && $amount)
        <span class="text-muted small fw-medium"
            aria-label="Jumlah pembayaran: Rp {{ number_format($amount, 0, ',', '.') }}">
            <i class="bi bi-wallet2 me-1" aria-hidden="true"></i>
            Rp {{ number_format($amount, 0, ',', '.') }}
        </span>
    @endif

    {{-- Payment Channel (Optional) --}}
    @if ($showChannel && $channel)
        <span class="text-muted small fw-medium"
            aria-label="Metode pembayaran: {{ $channel }}">
            <i class="bi bi-credit-card me-1" aria-hidden="true"></i>
            {{ $channel }}
        </span>
    @endif
</div>
