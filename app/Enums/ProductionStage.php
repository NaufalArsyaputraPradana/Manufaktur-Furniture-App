<?php

namespace App\Enums;

enum ProductionStage: string
{
    case PENDING = 'pending';
    case CUTTING = 'cutting';
    case ASSEMBLY = 'assembly';
    case SANDING = 'sanding';
    case FINISHING = 'finishing';
    case QUALITY_CONTROL = 'quality_control';
    case COMPLETED = 'completed';

    /**
     * Get progress percentage
     */
    public function progressPercentage(): int
    {
        return match($this) {
            self::PENDING => 0,
            self::CUTTING => 15,
            self::ASSEMBLY => 45,
            self::SANDING => 65,
            self::FINISHING => 85,
            self::QUALITY_CONTROL => 95,
            self::COMPLETED => 100,
        };
    }

    /**
     * Get label untuk display
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::CUTTING => 'Pemotongan Bahan',
            self::ASSEMBLY => 'Perakitan',
            self::SANDING => 'Penghalusan',
            self::FINISHING => 'Finishing',
            self::QUALITY_CONTROL => 'Quality Control',
            self::COMPLETED => 'Selesai',
        };
    }

    /**
     * Get color untuk badge
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'secondary',
            self::CUTTING => 'danger',
            self::ASSEMBLY => 'warning',
            self::SANDING => 'info',
            self::FINISHING => 'primary',
            self::QUALITY_CONTROL => 'warning',
            self::COMPLETED => 'success',
        };
    }
}
