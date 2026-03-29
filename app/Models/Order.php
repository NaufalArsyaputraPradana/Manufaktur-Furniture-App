<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'shipping_status',
        'courier',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'shipping_address',
        'phone',
        'customer_notes',
        'admin_notes',
        'subtotal',
        'total',
        'order_date',
        'expected_completion_date',
        'actual_completion_date',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'order_date' => 'datetime',
            'expected_completion_date' => 'datetime',
            'actual_completion_date' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function remainingPayableAmount(): float
    {
        $total = (float) $this->total;
        $paid = (float) ($this->payment?->amount_paid ?? 0);

        return max(0, round($total - $paid, 2));
    }

    public function getShippingStatusLabelAttribute(): string
    {
        return match ($this->shipping_status) {
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Sampai',
            default => $this->shipping_status ? ucfirst(str_replace('_', ' ', $this->shipping_status)) : 'Belum diperbarui',
        };
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $newNumber = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi',
            'in_production' => 'Dalam Produksi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'on_hold' => 'Ditahan',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'in_production' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'on_hold' => 'secondary',
            default => 'light',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function productionProcesses(): HasMany
    {
        return $this->hasMany(ProductionProcess::class);
    }

    public function shippingLogs(): HasMany
    {
        return $this->hasMany(OrderShippingLog::class)->orderByDesc('created_at');
    }
}