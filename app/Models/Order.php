<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
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
            'status' => OrderStatus::class,
            'shipping_status' => ShippingStatus::class,
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'order_date' => 'date',
            'expected_completion_date' => 'date',
            'actual_completion_date' => 'date',
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
        return $this->shipping_status?->label() ?? 'Belum diperbarui';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'light';
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

    /**
     * Generate unique order number with database-level safety
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        
        // Use database lock to prevent race conditions
        $maxOrder = static::whereDate('created_at', today())
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('order_number');
        
        if ($maxOrder && str_contains($maxOrder, $date)) {
            // Extract counter from existing order (e.g., ORD-20260427-00012 -> 12)
            $parts = explode('-', $maxOrder);
            $counter = (int) end($parts) + 1;
        } else {
            // No orders today yet, start from 1
            $counter = 1;
        }
        
        // Ensure counter doesn't exceed 5 digits (99999 orders per day max)
        if ($counter > 99999) {
            throw new \Exception('Order number counter exceeded maximum for today (99999)');
        }
        
        return $prefix . '-' . $date . '-' . str_pad($counter, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Check if order can transition to a specific status
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        if (!$this->status) {
            return false;
        }

        return in_array($newStatus, $this->status->allowedTransitions());
    }

    /**
     * Validate status transition before updating
     */
    public function transitionTo(OrderStatus $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $this->update(['status' => $newStatus]);
        return true;
    }
}