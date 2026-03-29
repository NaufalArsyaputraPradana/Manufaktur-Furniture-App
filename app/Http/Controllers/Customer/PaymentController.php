<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * Customer Payment Controller
 * 
 * Handles payment operations for customers:
 * - Midtrans webhook notifications
 * - Snap token generation for Midtrans payments
 */
class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
        //
    }

    /**
     * Handle Midtrans webhook notification (Server-to-Server)
     * 
     * Processes payment status updates from Midtrans payment gateway.
     * No CSRF protection needed as this is called by Midtrans servers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleNotification(Request $request): JsonResponse
    {
        try {
            Log::info('Midtrans Notification Received', $request->all());
            
            $notification = $request->all();
            if (empty($notification['order_id']) || empty($notification['transaction_status'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Notifikasi tidak valid'
                ], 400);
            }
            
            $this->paymentService->handleMidtransNotification($notification);
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment notification handling failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate Snap token for Midtrans payment
     * 
     * Creates a Midtrans transaction and returns the snap token
     * for displaying the payment popup on client side.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    public function generateSnapToken(Request $request, Order $order): JsonResponse
    {
        try {
            // Check if user is authenticated
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus login terlebih dahulu untuk melakukan pembayaran'
                ], 401);
            }

            // Authorize: only owner or admin may request token
            if ($user->role?->name === 'customer' && $order->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak berhak mengakses order ini'
                ], 403);
            }

            // Eager load required relationships
            $order->loadMissing(['orderDetails', 'user']);
            
            $result = $this->paymentService->createMidtransTransaction($order);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate snap token', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Gagal membuat transaksi pembayaran'
            ], 500);
        }
    }
}
