<?php


namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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

    public function pendingVerification(): View
    {
        $payments = Payment::with(['order.user', 'order.orderDetails.product'])
            ->where('payment_status', Payment::STATUS_UNPAID)
            ->whereNotNull('payment_proof')
            ->latest()
            ->paginate(20);
        return view('admin.payments.pending', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.orderDetails.product']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        try {
            $this->paymentService->verifyManualPayment($payment);
            $order = $payment->order;
            $note  = '[' . now()->format('d/m/Y H:i') . '] Pembayaran diverifikasi oleh Admin.';
            $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);
            return redirect()->route('admin.payments.pending')
                ->with('success', 'Pembayaran berhasil diverifikasi dan pesanan telah dikonfirmasi!');
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
}