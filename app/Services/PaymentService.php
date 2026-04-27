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
     *
     * @param  string  $phase  full | dp | remainder — dp = uang muka 50%, remainder = sisa setelah dp_paid
     */
    public function createMidtransTransaction(Order $order, array $customer = [], string $phase = 'full'): array
    {
        $midtrans = new MidtransService();
        $order->loadMissing(['orderDetails', 'user']);

        $phase = in_array($phase, ['full', 'dp', 'remainder'], true) ? $phase : 'full';

        $total = (float) $order->total;
        $dpAmount = round($total * 0.5, 2);

        $payment = $order->payment;

        if ($phase === 'remainder') {
            if (!$payment || !$payment->isDpPaid()) {
                throw new \InvalidArgumentException('Pelunasan tidak tersedia.');
            }
            if ($payment->payment_status === Payment::STATUS_FULL_PENDING) {
                throw new \InvalidArgumentException('Pelunasan sedang diverifikasi admin.');
            }
            $remaining = $order->remainingPayableAmount();
            if ($remaining <= 0) {
                throw new \InvalidArgumentException('Pelunasan sudah dibayar.');
            }
            $grossAmount = (int) round($remaining);
            $transactionId = Payment::generateTransactionId() . '-FULL';
            $payment->update([
                'payment_method'  => Payment::METHOD_MIDTRANS,
                'payment_channel' => Payment::CHANNEL_MIDTRANS,
                'transaction_id'  => $transactionId,
            ]);
            $items = [
                [
                    'id'       => 'REM-' . $order->id,
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => substr('Pelunasan ' . $order->order_number, 0, 50),
                ],
            ];
        } elseif ($phase === 'dp') {
            if ($payment && $payment->payment_status === Payment::STATUS_PAID) {
                throw new \InvalidArgumentException('Pesanan sudah lunas.');
            }
            if ($payment && $payment->isDpPaid()) {
                throw new \InvalidArgumentException('DP sudah dibayar. Gunakan menu pelunasan.');
            }
            if ($payment && $payment->payment_status === Payment::STATUS_FULL_PENDING) {
                throw new \InvalidArgumentException('Pembayaran sedang diverifikasi.');
            }
            $grossAmount = (int) round($dpAmount);
            $transactionId = Payment::generateTransactionId() . '-DP';
            $payment = $this->createOrUpdatePayment($order, [
                'payment_method'     => Payment::METHOD_MIDTRANS,
                'payment_channel'    => Payment::CHANNEL_MIDTRANS,
                'amount'             => $total,
                'amount_paid'        => 0,
                'expected_dp_amount' => $dpAmount,
                'transaction_id'     => $transactionId,
            ]);
            $items = [
                [
                    'id'       => 'DP-' . $order->id,
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => substr('DP ' . $order->order_number, 0, 50),
                ],
            ];
        } else {
            if ($payment && $payment->payment_status === Payment::STATUS_PAID) {
                throw new \InvalidArgumentException('Pesanan sudah lunas.');
            }
            if ($payment && $payment->isDpPaid()) {
                throw new \InvalidArgumentException('Gunakan pembayaran pelunasan untuk sisa tagihan.');
            }
            if ($payment && $payment->payment_status === Payment::STATUS_FULL_PENDING) {
                throw new \InvalidArgumentException('Pembayaran sedang diverifikasi.');
            }
            $grossAmount = (int) round($total);
            $transactionId = Payment::generateTransactionId() . '-FULL';
            $payment = $this->createOrUpdatePayment($order, [
                'payment_method'     => Payment::METHOD_MIDTRANS,
                'payment_channel'    => Payment::CHANNEL_MIDTRANS,
                'amount'             => $total,
                'amount_paid'        => 0,
                'expected_dp_amount' => null,
                'transaction_id'     => $transactionId,
            ]);
            $items = [];
            foreach ($order->orderDetails as $detail) {
                $items[] = [
                    'id'       => $detail->id,
                    'price'    => (int) round($detail->unit_price),
                    'quantity' => (int) $detail->quantity,
                    'name'     => substr($detail->product_name ?? 'Item', 0, 50),
                ];
            }
            $itemsSum = array_sum(array_map(static fn (array $i) => $i['price'] * $i['quantity'], $items));
            if (abs($itemsSum - $grossAmount) > 1) {
                $items = [
                    [
                        'id'       => 'ORDER-' . $order->id,
                        'price'    => $grossAmount,
                        'quantity' => 1,
                        'name'     => substr($order->order_number, 0, 50),
                    ],
                ];
            }
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->transaction_id,
                'gross_amount' => $grossAmount,
            ],
            'item_details'     => $items,
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? $order->user->name ?? 'Customer',
                'email'      => $customer['email'] ?? $order->user->email ?? null,
                'phone'      => $customer['phone'] ?? $order->user->phone ?? null,
            ],
        ];

        $snapToken = $midtrans->createSnapToken($params);

        return [
            'snap_token'     => $snapToken,
            'client_key'     => config('midtrans.client_key'),
            'script_url'     => $midtrans->getSnapScriptUrl(),
            'transaction_id' => $payment->transaction_id,
            'phase'          => $phase,
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
            $gross = (float) $grossAmount;
            $epsilon = 1.0;

            if ($payment->payment_status === Payment::STATUS_PAID) {
                return true;
            }

            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                $order = $payment->order;
                if (!$order) {
                    return false;
                }
                $total = (float) ($order->total ?? $payment->amount ?? 0);
                $remaining = $order->remainingPayableAmount();

                if ($payment->payment_status === Payment::STATUS_PENDING) {
                    $expectedDp = (float) ($payment->expected_dp_amount ?? 0);
                    if ($expectedDp > 0 && abs($gross - $expectedDp) <= $epsilon) {
                        $payment->update([
                            'payment_status' => Payment::STATUS_DP_PAID,
                            'amount_paid'    => $expectedDp,
                            'payment_date'   => null,
                            'payment_channel'=> Payment::CHANNEL_MIDTRANS,
                        ]);
                    } elseif (abs($gross - $total) <= $epsilon) {
                        $payment->update([
                            'payment_status' => Payment::STATUS_PAID,
                            'amount_paid'    => $total,
                            'payment_date'   => now(),
                            'payment_channel'=> Payment::CHANNEL_MIDTRANS,
                            'expected_dp_amount' => null,
                        ]);
                        if ($order->status === 'pending') {
                            $order->update(['status' => 'confirmed']);
                        }
                    } else {
                        Log::warning('Midtrans gross amount mismatch (pending)', [
                            'order_id' => $orderId,
                            'gross' => $gross,
                            'expected_dp' => $expectedDp,
                            'total' => $total,
                        ]);
                    }
                } elseif ($payment->payment_status === Payment::STATUS_DP_PAID) {
                    if (abs($gross - $remaining) <= $epsilon) {
                        $payment->update([
                            'payment_status' => Payment::STATUS_PAID,
                            'amount_paid'    => $total,
                            'payment_date'   => now(),
                            'payment_channel'=> Payment::CHANNEL_MIDTRANS,
                        ]);
                        if ($order->status === 'pending') {
                            $order->update(['status' => 'confirmed']);
                        }
                    } else {
                        Log::warning('Midtrans gross amount mismatch (remainder)', [
                            'order_id' => $orderId,
                            'gross' => $gross,
                            'remaining' => $remaining,
                        ]);
                    }
                }
            } elseif ($transactionStatus === 'pending') {
                if (! in_array($payment->payment_status, [Payment::STATUS_DP_PAID, Payment::STATUS_FULL_PENDING], true)) {
                    $payment->update(['payment_status' => Payment::STATUS_PENDING]);
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
                if (in_array($payment->payment_status, [Payment::STATUS_DP_PAID, Payment::STATUS_FULL_PENDING], true)) {
                    return true;
                }
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
