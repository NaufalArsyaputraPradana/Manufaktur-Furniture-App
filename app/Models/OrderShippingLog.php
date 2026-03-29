<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShippingLog extends Model
{
    public const STAGE_LOADING = 'loading';

    public const STAGE_HANDOVER = 'handover';

    public const STAGE_IN_TRANSIT = 'in_transit';

    public const STAGE_OUT_FOR_DELIVERY = 'out_for_delivery';

    public const STAGE_DELIVERED = 'delivered';

    public const STAGE_ISSUE = 'issue';

    public static function stageLabels(): array
    {
        return [
            self::STAGE_LOADING => 'Persiapan & muat barang',
            self::STAGE_HANDOVER => 'Serah ke ekspedisi / kurir',
            self::STAGE_IN_TRANSIT => 'Dalam perjalanan',
            self::STAGE_OUT_FOR_DELIVERY => 'Menuju alamat penerima',
            self::STAGE_DELIVERED => 'Barang diterima di tujuan',
            self::STAGE_ISSUE => 'Kendala / eskalasi',
        ];
    }

    public static function stageIcons(): array
    {
        return [
            self::STAGE_LOADING => 'bi-box-seam',
            self::STAGE_HANDOVER => 'bi-truck',
            self::STAGE_IN_TRANSIT => 'bi-geo-alt',
            self::STAGE_OUT_FOR_DELIVERY => 'bi-signpost-2',
            self::STAGE_DELIVERED => 'bi-house-check',
            self::STAGE_ISSUE => 'bi-exclamation-triangle',
        ];
    }

    protected $fillable = [
        'order_id',
        'stage',
        'status',
        'notes',
        'documentation',
        'courier_note',
        'tracking_note',
        'recorded_by',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getStageLabelAttribute(): string
    {
        return self::stageLabels()[$this->stage] ?? ucfirst(str_replace('_', ' ', $this->stage));
    }
}
