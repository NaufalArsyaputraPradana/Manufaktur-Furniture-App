<?php


namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createOrUpdatePayment(Order $order, array $paymentData): Payment
    {
        return DB::transaction(function () use ($order, $paymentData) {
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'amount'         => $paymentData['amount'] ?? $order->total,
                    'payment_method' => $paymentData['payment_method'],
                    'payment_status' => Payment::STATUS_UNPAID,
                    'transaction_id' => $paymentData['transaction_id'] ?? Payment::generateTransactionId(),
                    'payment_proof'  => $paymentData['payment_proof'] ?? null,
                ]
            );
            return $payment;
        });
    }

    public function verifyManualPayment(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $result = $payment->markAsPaid();
            if ($result) {
                $order = $payment->order;
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'confirmed']);
                }
            }
            return $result;
        });
    }

    public function rejectPayment(Payment $payment, string $reason = ''): bool
    {
        return DB::transaction(function () use ($payment, $reason) {
            Log::info('Payment rejected', ['payment_id' => $payment->id, 'reason' => $reason]);
            return $payment->markAsFailed();
        });
    }
}