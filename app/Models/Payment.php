<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'payment_proof',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount'       => 'decimal:2',
        ];
    }

    const METHOD_BANK_TRANSFER = 'transfer';
    const METHOD_CASH          = 'cash';
    const METHOD_CREDIT_CARD   = 'credit_card';

    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID   = 'paid';
    const STATUS_FAILED = 'failed';

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('payment_status', $status);
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_PAID);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_UNPAID);
    }

    public static function generateTransactionId(): string
    {
        return 'TRX-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
    }

    public function isSuccessful(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_UNPAID;
    }

    public function isFailed(): bool
    {
        return $this->payment_status === self::STATUS_FAILED;
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'payment_status' => self::STATUS_PAID,
            'payment_date'   => now(),
        ]);
    }

    public function markAsFailed(): bool
    {
        return $this->update([
            'payment_status' => self::STATUS_FAILED,
        ]);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format((float) ($this->amount ?? 0), 0, ',', '.')
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->payment_status) {
                self::STATUS_UNPAID => 'Belum Dibayar',
                self::STATUS_PAID   => 'Lunas',
                self::STATUS_FAILED => 'Gagal / Ditolak',
                default             => ucfirst($this->payment_status ?? 'Tidak Diketahui'),
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->payment_status) {
                self::STATUS_UNPAID => 'warning',
                self::STATUS_PAID   => 'success',
                self::STATUS_FAILED => 'danger',
                default             => 'secondary',
            }
        );
    }

    protected function paymentMethodName(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->payment_method) {
                self::METHOD_BANK_TRANSFER => 'Transfer Bank',
                self::METHOD_CASH          => 'Tunai (COD)',
                self::METHOD_CREDIT_CARD   => 'Kartu Kredit / E-Wallet',
                default                    => ucwords(str_replace('_', ' ', $this->payment_method ?? '-')),
            }
        );
    }
}