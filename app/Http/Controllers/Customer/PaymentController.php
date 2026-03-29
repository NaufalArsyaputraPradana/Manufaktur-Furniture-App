<?php


namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function handleNotification(Request $request): JsonResponse
    {
        try {
            Log::info('Midtrans Notification Received', $request->all());
            $notification = $request->all();
            if (empty($notification['order_id']) || empty($notification['transaction_status'])) {
                return response()->json(['status' => 'error', 'message' => 'Notifikasi tidak valid'], 400);
            }
            $this->paymentService->handleMidtransNotification($notification);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment notification handling failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

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

            $order->loadMissing(['orderDetails', 'user']);
            $result = $this->paymentService->createMidtransTransaction($order);

            return response()->json(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Failed to generate snap token', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Gagal membuat transaksi pembayaran'
            ], 500);
        }
    }

    public function pendingVerification(Request $request): View
    {
        $tab = $request->get('tab', 'pending');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Build base query with date filtering
        $query = Payment::with(['order.user', 'order.orderDetails.product'])
            ->whereNotNull('payment_proof')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        // Get payments based on tab
        if ($tab === 'pending') {
            // Menunggu Verifikasi - show only pending payments
            $payments = $query->whereIn('payment_status', [Payment::STATUS_PENDING, Payment::STATUS_FULL_PENDING])
                ->latest()
                ->paginate(20);
        } else {
            // Riwayat Pembayaran - show all completed/verified payments
            $payments = $query->whereIn('payment_status', [Payment::STATUS_DP_PAID, Payment::STATUS_PAID, Payment::STATUS_FAILED])
                ->latest()
                ->paginate(20);
        }

        return view('admin.payments.pending', compact('payments', 'tab', 'month', 'year'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.orderDetails.product']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        try {
            $this->paymentService->verifyManualTransfer($payment);
            $order = $payment->order;
            $note = '[' . now()->format('d/m/Y H:i') . '] Pembayaran diverifikasi oleh Admin.';
            $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);

            return redirect()->route('admin.payments.pending')
                ->with('success', 'Pembayaran berhasil diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Payment verification failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ], ['notes.required' => 'Alasan penolakan wajib diisi.']);

        try {
            $this->paymentService->rejectPayment($payment, $validated['notes']);
            $order = $payment->order;
            $note  = '[' . now()->format('d/m/Y H:i') . '] Pembayaran ditolak. Alasan: ' . $validated['notes'];
            $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);
            return redirect()->route('admin.payments.pending')
                ->with('success', 'Pembayaran telah berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Payment rejection failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menolak pembayaran. Silakan coba lagi.');
        }
    }

    public function confirmFinalPayment(Request $request, Payment $payment): RedirectResponse
    {
        try {
            // Check if payment is in FULL_PENDING status
            if ($payment->payment_status !== Payment::STATUS_FULL_PENDING) {
                return back()->with('error', 'Pembayaran tidak dalam status menunggu konfirmasi pelunasan.');
            }

            $this->paymentService->confirmFinalPayment($payment);
            $order = $payment->order;
            $note = '[' . now()->format('d/m/Y H:i') . '] Pelunasan dikonfirmasi oleh Admin. Status pesanan berubah menjadi LUNAS.';
            $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);

            return redirect()->route('admin.payments.show', $payment)
                ->with('success', 'Pelunasan berhasil dikonfirmasi. Pesanan sekarang berstatus LUNAS.');
        } catch (\Exception $e) {
            Log::error('Final payment confirmation failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengkonfirmasi pelunasan: ' . $e->getMessage());
        }
    }
}