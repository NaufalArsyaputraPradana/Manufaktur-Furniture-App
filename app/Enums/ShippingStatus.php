<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public function label(): string
    {
        return match($this) {
            self::PROCESSING => 'Diproses',
            self::SHIPPED => 'Dikirim',
            self::DELIVERED => 'Sampai',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PROCESSING => 'warning',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
        };
    }
}
