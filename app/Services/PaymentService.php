<?php


namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;

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

    /**
     * Create Midtrans transaction and return snap token.
     */
    public function createMidtransTransaction(Order $order, array $customer = []): array
    {
        $midtrans = new MidtransService();

        // Ensure a payment record exists and has a transaction_id
        $payment = $this->createOrUpdatePayment($order, ['payment_method' => Payment::METHOD_CREDIT_CARD, 'amount' => $order->total]);

        $transactionId = $payment->transaction_id;

        // Build item details
        $items = [];
        foreach ($order->orderDetails as $detail) {
            $items[] = [
                'id' => $detail->id,
                'price' => (int) round($detail->unit_price),
                'quantity' => (int) $detail->quantity,
                'name' => substr($detail->product_name ?? 'Item', 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => (int) round($order->total),
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? $order->user->name ?? 'Customer',
                'email' => $customer['email'] ?? $order->user->email ?? null,
                'phone' => $customer['phone'] ?? $order->user->phone ?? null,
            ],
        ];

        $snapToken = $midtrans->createSnapToken($params);

        return [
            'snap_token' => $snapToken,
            'client_key' => config('midtrans.client_key'),
            'script_url' => $midtrans->getSnapScriptUrl(),
            'transaction_id' => $transactionId,
        ];
    }

    /**
     * Handle Midtrans server-to-server notification payload.
     */
    public function handleMidtransNotification(array $notification): bool
    {
        try {
            // Verify signature
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
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Map status
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $payment->markAsPaid();
                $order = $payment->order;
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'confirmed']);
                }
            } elseif ($transactionStatus === 'pending') {
                // keep as unpaid/pending
                $payment->update(['payment_status' => Payment::STATUS_UNPAID]);
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $payment->markAsFailed();
            }

            // Optionally store raw payload for audit (if column exists)
            if (\Illuminate\Support\Facades\Schema::hasColumn('payments', 'raw_response')) {
                $payment->update(['raw_response' => json_encode($notification)]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed handling midtrans notification', ['error' => $e->getMessage(), 'payload' => $notification]);
            return false;
        }
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