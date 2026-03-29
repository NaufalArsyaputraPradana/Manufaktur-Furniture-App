<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Admin Payment Controller
 * 
 * Handles payment verification, rejection, and confirmation for admin users.
 * Responsible for managing payment proofs and payment status transitions.
 */
class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display pending payments for verification
     * 
     * Shows payments waiting for admin verification or confirmation.
     * Filters by month/year and payment status.
     *
     * @param Request $request
     * @return View
     */
    public function pendingVerification(Request $request): View
    {
        $tab = $request->get('tab', 'pending');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Build base query with date filtering and eager loading
        $query = Payment::with(['order.user', 'order.orderDetails.product'])
            ->whereNotNull('payment_proof')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        // Get payments based on tab
        if ($tab === 'pending') {
            // Menunggu Verifikasi - show only pending payments
            $payments = $query->whereIn('payment_status', [
                Payment::STATUS_PENDING,
                Payment::STATUS_FULL_PENDING
            ])
                ->latest()
                ->paginate(20);
        } else {
            // Riwayat Pembayaran - show all completed/verified payments
            $payments = $query->whereIn('payment_status', [
                Payment::STATUS_DP_PAID,
                Payment::STATUS_PAID,
                Payment::STATUS_FAILED
            ])
                ->latest()
                ->paginate(20);
        }

        return view('admin.payments.pending', compact('payments', 'tab', 'month', 'year'));
    }

    /**
     * Display detailed payment information
     *
     * @param Payment $payment
     * @return View
     */
    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.orderDetails.product']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Verify manual payment transfer
     * 
     * Transitions payment from PENDING -> DP_PAID or DP_PAID -> FULL_PENDING
     * depending on current status.
     *
     * @param Request $request
     * @param Payment $payment
     * @return RedirectResponse
     */
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
            Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Reject payment with reason
     * 
     * Resets payment status to FAILED and stores rejection reason.
     *
     * @param Request $request
     * @param Payment $payment
     * @return RedirectResponse
     */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.'
        ]);

        try {
            $this->paymentService->rejectPayment($payment, $validated['notes']);
            
            $order = $payment->order;
            $note = '[' . now()->format('d/m/Y H:i') . '] Pembayaran ditolak. Alasan: ' . $validated['notes'];
            $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);
            
            return redirect()->route('admin.payments.pending')
                ->with('success', 'Pembayaran telah berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Payment rejection failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal menolak pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Confirm final payment (full settlement)
     * 
     * Transitions payment from FULL_PENDING -> PAID
     * Updates order status to CONFIRMED.
     *
     * @param Request $request
     * @param Payment $payment
     * @return RedirectResponse
     */
    public function confirmFinalPayment(Request $request, Payment $payment): RedirectResponse
    {
        try {
            // Validate payment is in correct status
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
            Log::error('Final payment confirmation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal mengkonfirmasi pelunasan: ' . $e->getMessage());
        }
    }
}
