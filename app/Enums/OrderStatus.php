<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PRODUCTION = 'in_production';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case ON_HOLD = 'on_hold';

    /**
     * Get label untuk display di UI
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::CONFIRMED => 'Dikonfirmasi',
            self::IN_PRODUCTION => 'Dalam Produksi',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
            self::ON_HOLD => 'Ditahan',
        };
    }

    /**
     * Get color untuk badge
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::IN_PRODUCTION => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
            self::ON_HOLD => 'secondary',
        };
    }

    /**
     * Get allowed transitions
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED, self::ON_HOLD],
            self::CONFIRMED => [self::IN_PRODUCTION, self::CANCELLED, self::ON_HOLD],
            self::IN_PRODUCTION => [self::COMPLETED, self::ON_HOLD],
            self::COMPLETED => [],
            self::CANCELLED => [],
            self::ON_HOLD => [self::CONFIRMED, self::IN_PRODUCTION, self::CANCELLED],
        };
    }

    /**
     * Check if transition is allowed
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }
}
