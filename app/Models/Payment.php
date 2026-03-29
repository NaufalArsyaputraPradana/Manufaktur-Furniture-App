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

    public const CHANNEL_MANUAL_DP   = 'manual_dp';

    public const CHANNEL_MANUAL_FULL = 'manual_full';

    public const CHANNEL_MIDTRANS    = 'midtrans';

    public const METHOD_BANK_TRANSFER = 'transfer';

    public const METHOD_CASH = 'cash';

    public const METHOD_CREDIT_CARD = 'credit_card';

    public const METHOD_MIDTRANS = 'midtrans';

    public const STATUS_PENDING = 'pending';

    public const STATUS_DP_PAID = 'dp_paid';

    public const STATUS_FULL_PENDING = 'full_pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'amount',
        'amount_paid',
        'expected_dp_amount',
        'payment_status',
        'payment_method',
        'payment_channel',
        'transaction_id',
        'payment_proof',
        'payment_proof_dp',
        'payment_proof_full',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'payment_date'    => 'datetime',
            'amount'          => 'decimal:2',
            'amount_paid'     => 'decimal:2',
            'expected_dp_amount' => 'decimal:2',
        ];
    }

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
        return $query->where('payment_status', self::STATUS_PENDING);
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
        return $this->payment_status === self::STATUS_PENDING;
    }

    public function isDpPaid(): bool
    {
        return $this->payment_status === self::STATUS_DP_PAID;
    }

    public function isFullPending(): bool
    {
        return $this->payment_status === self::STATUS_FULL_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->payment_status === self::STATUS_FAILED;
    }

    public function markAsPaid(?float $amountPaid = null): bool
    {
        $order = $this->order;
        $total = (float) ($order?->total ?? $this->amount ?? 0);

        return $this->update([
            'payment_status' => self::STATUS_PAID,
            'amount_paid'    => $amountPaid ?? $total,
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
            get: fn () => 'Rp ' . number_format((float) ($this->amount ?? 0), 0, ',', '.')
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->payment_status) {
                self::STATUS_PENDING => 'Menunggu Pembayaran / Verifikasi',
                self::STATUS_DP_PAID => 'DP telah diverifikasi',
                self::STATUS_FULL_PENDING => 'Menunggu Konfirmasi Pelunasan',
                self::STATUS_PAID    => 'Lunas',
                self::STATUS_FAILED  => 'Gagal / Ditolak',
                default              => ucfirst($this->payment_status ?? 'Tidak Diketahui'),
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->payment_status) {
                self::STATUS_PENDING => 'warning',
                self::STATUS_DP_PAID => 'info',
                self::STATUS_FULL_PENDING => 'warning',
                self::STATUS_PAID    => 'success',
                self::STATUS_FAILED  => 'danger',
                default              => 'secondary',
            }
        );
    }

    protected function paymentMethodName(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->payment_method) {
                self::METHOD_BANK_TRANSFER => 'Transfer Bank',
                self::METHOD_CASH          => 'Tunai (COD)',
                self::METHOD_CREDIT_CARD   => 'Kartu Kredit / E-Wallet',
                self::METHOD_MIDTRANS      => 'Payment Gateway (Midtrans)',
                default                    => ucwords(str_replace('_', ' ', $this->payment_method ?? '-')),
            }
        );
    }
}
