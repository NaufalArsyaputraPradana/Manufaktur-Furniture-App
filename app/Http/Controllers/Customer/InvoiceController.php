<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function show(Order $order): View
    {
        $this->authorizeCustomerOrder($order);
        $this->ensureInvoiceAllowed($order);

        $order->load(['user', 'orderDetails.product', 'payment']);

        return view('customer.invoices.show', compact('order'));
    }

    public function download(Order $order): Response
    {
        $this->authorizeCustomerOrder($order);
        $this->ensureInvoiceAllowed($order);

        $order->load(['user', 'orderDetails.product', 'payment']);

        $pdf = Pdf::loadView('customer.invoices.pdf', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    private function authorizeCustomerOrder(Order $order): void
    {
        $user = Auth::user();
        if ($user && $user->isCustomer() && $order->user_id !== $user->id) {
            abort(403);
        }
    }

    private function ensureInvoiceAllowed(Order $order): void
    {
        $st = $order->payment?->payment_status;
        if (!in_array($st, [Payment::STATUS_DP_PAID, Payment::STATUS_PAID], true)) {
            abort(403, 'Invoice dapat diakses setelah pembayaran DP atau lunas diverifikasi.');
        }
    }
}
