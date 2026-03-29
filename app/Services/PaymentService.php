<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function createOrUpdatePayment(Order $order, array $paymentData): Payment
    {
        return DB::transaction(function () use ($order, $paymentData) {
            return Payment::updateOrCreate(
                ['order_id' => $order->id],
                array_merge(
                    [
                        'amount'         => $paymentData['amount'] ?? $order->total,
                        'payment_status' => Payment::STATUS_PENDING,
                        'transaction_id' => $paymentData['transaction_id'] ?? Payment::generateTransactionId(),
                    ],
                    $paymentData
                )
            );
        });
    }

    /**
     * Create Midtrans transaction and return snap token.
     */
    public function createMidtransTransaction(Order $order, array $customer = []): array
    {
        $midtrans = new MidtransService();

        $payment = $this->createOrUpdatePayment($order, [
            'payment_method'   => Payment::METHOD_MIDTRANS,
            'payment_channel'  => Payment::CHANNEL_MIDTRANS,
            'amount'           => $order->total,
            'amount_paid'      => 0,
            'expected_dp_amount' => null,
        ]);

        $transactionId = $payment->transaction_id;

        $items = [];
        foreach ($order->orderDetails as $detail) {
            $items[] = [
                'id'       => $detail->id,
                'price'    => (int) round($detail->unit_price),
                'quantity' => (int) $detail->quantity,
                'name'     => substr($detail->product_name ?? 'Item', 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $transactionId,
                'gross_amount' => (int) round($order->total),
            ],
            'item_details'       => $items,
            'customer_details'   => [
                'first_name' => $customer['first_name'] ?? $order->user->name ?? 'Customer',
                'email'      => $customer['email'] ?? $order->user->email ?? null,
                'phone'      => $customer['phone'] ?? $order->user->phone ?? null,
            ],
        ];

        $snapToken = $midtrans->createSnapToken($params);

        return [
            'snap_token'       => $snapToken,
            'client_key'       => config('midtrans.client_key'),
            'script_url'       => $midtrans->getSnapScriptUrl(),
            'transaction_id'   => $transactionId,
        ];
    }

    public function handleMidtransNotification(array $notification): bool
    {
        try {
            $orderId = $notification['order_id'] ?? null;
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $signature = $notification['signature_key'] ?? null;

            $serverKey = config('midtrans.server_key');
            $calc = hash('sha512', ($orderId ?? '') . ($statusCode ?? '') . ($grossAmount ?? '') . $serverKey);
            if (!$signature || $calc !== $signature) {
                Log::warning('Midtrans signature mismatch', ['payload' => $notification]);

                return false;
            }

            $payment = Payment::where('transaction_id', $orderId)->first();
            if (!$payment) {
                Log::warning('Midtrans notification for unknown transaction', ['order_id' => $orderId]);

                return false;
            }

            $transactionStatus = $notification['transaction_status'] ?? null;

            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                $order = $payment->order;
                $total = (float) ($order?->total ?? $payment->amount ?? 0);
                $payment->update([
                    'payment_status' => Payment::STATUS_PAID,
                    'amount_paid'    => $total,
                    'payment_date'   => now(),
                    'payment_channel'=> Payment::CHANNEL_MIDTRANS,
                ]);
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'confirmed']);
                }
            } elseif ($transactionStatus === 'pending') {
                $payment->update(['payment_status' => Payment::STATUS_PENDING]);
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
                $payment->markAsFailed();
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('payments', 'raw_response')) {
                $payment->update(['raw_response' => json_encode($notification)]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed handling midtrans notification', ['error' => $e->getMessage(), 'payload' => $notification]);

            return false;
        }
    }

    /**
     * Verifikasi transfer manual: DP pertama atau pelunasan / transfer penuh.
     */
    public function verifyManualTransfer(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->refresh();
            $order = $payment->order;
            if (!$order) {
                throw new \InvalidArgumentException('Pesanan tidak ditemukan.');
            }

            $total = (float) $order->total;

            if ($payment->payment_channel === Payment::CHANNEL_MANUAL_DP) {
                if ($payment->payment_status === Payment::STATUS_PENDING) {
                    $dp = (float) ($payment->expected_dp_amount ?? 0);
                    // Move payment_proof to payment_proof_dp instead of deleting
                    $proofPath = $payment->payment_proof;
                    $payment->update([
                        'payment_status' => Payment::STATUS_DP_PAID,
                        'amount_paid'    => $dp,
                        'payment_date'   => null,
                        'payment_proof_dp' => $proofPath,  // Save to DP field
                        'payment_proof'  => null,           // Clear from legacy field
                    ]);

                    return;
                }

                if ($payment->payment_status === Payment::STATUS_DP_PAID) {
                    // When final payment proof is uploaded after DP, set to FULL_PENDING
                    // instead of directly PAID, to wait for admin confirmation
                    $payment->update([
                        'payment_status' => Payment::STATUS_FULL_PENDING,
                        'payment_proof_full' => $payment->payment_proof,  // Move to full payment field
                        'payment_proof'  => null,  // Clear from temp field
                    ]);

                    return;
                }
            }

            if ($payment->payment_channel === Payment::CHANNEL_MANUAL_FULL) {
                $payment->update([
                    'payment_status' => Payment::STATUS_PAID,
                    'amount_paid'    => $total,
                    'payment_date'   => now(),
                    'payment_proof_full' => $payment->payment_proof,  // Save to full payment field
                ]);
                if ($order->status === 'pending') {
                    $order->update(['status' => 'confirmed']);
                }

                return;
            }

            // Legacy rows (tanpa payment_channel): anggap pelunasan penuh
            $payment->markAsPaid($total);
            if ($order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
            }
        });
    }

    /** @deprecated Gunakan verifyManualTransfer */
    public function verifyManualPayment(Payment $payment): bool
    {
        try {
            $this->verifyManualTransfer($payment);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Confirm final payment (FULL_PENDING → PAID).
     * Called by admin after verifying the final payment proof.
     */
    public function confirmFinalPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            if ($payment->payment_status !== Payment::STATUS_FULL_PENDING) {
                throw new \InvalidArgumentException('Pembayaran harus dalam status menunggu konfirmasi.');
            }

            $order = $payment->order;
            if (!$order) {
                throw new \InvalidArgumentException('Pesanan tidak ditemukan.');
            }

            $total = (float) $order->total;

            $payment->update([
                'payment_status' => Payment::STATUS_PAID,
                'amount_paid'    => $total,
                'payment_date'   => now(),
            ]);

            if ($order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
            }
        });
    }

    public function rejectPayment(Payment $payment, string $reason = ''): bool
    {
        return DB::transaction(function () use ($payment, $reason) {
            Log::info('Payment rejected', ['payment_id' => $payment->id, 'reason' => $reason]);

            // Delete the file from storage when rejecting
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            // Also delete DP proof if it exists
            if ($payment->payment_proof_dp && Storage::disk('public')->exists($payment->payment_proof_dp)) {
                Storage::disk('public')->delete($payment->payment_proof_dp);
            }
            // Also delete full payment proof if it exists
            if ($payment->payment_proof_full && Storage::disk('public')->exists($payment->payment_proof_full)) {
                Storage::disk('public')->delete($payment->payment_proof_full);
            }

            return $payment->update([
                'payment_status' => Payment::STATUS_FAILED,
                'payment_proof'  => null,
                'payment_proof_dp' => null,
                'payment_proof_full' => null,
            ]);
        });
    }
}
