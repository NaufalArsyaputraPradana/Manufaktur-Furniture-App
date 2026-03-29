# Payment Confirmation System - Implementation Guide

## Overview
This document outlines the complete payment confirmation workflow implemented in the furniture manufacturing e-commerce system. The system prevents double payment uploads and provides transparent status tracking to both customers and admins.

---

## Payment Status Flow

```
PENDING
   ↓
   └─→ Customer uploads DP proof
       ↓
   DP_PAID (DP verified by admin)
       ↓
       └─→ Customer uploads final payment proof
           ↓
       FULL_PENDING (Waiting for admin confirmation)
           ↓
           └─→ Admin verifies and confirms
               ↓
           PAID (Payment complete, production starts)
```

---

## Status Definitions

### 1. **PENDING** (`pending`)
- **When**: Order created, payment not yet initiated
- **Customer View**: "Belum Dibayar" - Shows DP payment form
- **Upload Form**: Enabled, customer can submit DP proof
- **Admin View**: Shows pending payment waiting for verification

### 2. **DP_PAID** (`dp_paid`)
- **When**: DP proof uploaded and verified by admin
- **Customer View**: "DP Sudah Dibayar" - Shows verified DP badge, waiting for final payment
- **Upload Form**: Enabled, customer can upload final payment (pelunasan)
- **Admin View**: Shows DP verified, can accept or reject final payment

### 3. **FULL_PENDING** (`full_pending`) ⭐ NEW
- **When**: Final payment proof uploaded, awaiting admin confirmation
- **Customer View**: "Menunggu Konfirmasi Pelunasan"
  - Detailed payment breakdown (DP 50% + Remaining 50%)
  - Both proofs displayed with status badges
  - Timeline showing verification schedule (Day 0-3)
  - Info boxes explaining verification process
  - Upload form DISABLED to prevent double submission
- **Admin View**: Enhanced payment verification interface
  - Both DP and Pelunasan proofs displayed
  - Payment breakdown with detailed amounts
  - "Konfirmasi Pelunasan" button to approve
  - Reject button to send back for correction
  - Verification checklist with requirements
- **Key Feature**: Prevents customer from uploading payment proof twice

### 4. **PAID** (`paid`)
- **When**: Admin confirms FULL_PENDING payment
- **Customer View**: "Pembayaran Lunas" - Shows complete payment status
- **Order Status**: Automatically changes to "confirmed"
- **Next Step**: Production begins

### 5. **FAILED** (`failed`)
- **When**: Admin rejects payment proof
- **Customer View**: Shows rejection reason and option to re-upload
- **Order Status**: Remains active, can resubmit proof

---

## Database Fields

### Payment Model
```php
$payment->payment_status      // Current status (PENDING, DP_PAID, FULL_PENDING, PAID, FAILED)
$payment->payment_proof       // Temporary field for pending uploads
$payment->payment_proof_dp    // Verified DP proof (moved from payment_proof)
$payment->payment_proof_full  // Verified final payment proof
$payment->amount              // Amount being verified
$payment->payment_date        // Date when status changed to PAID
$payment->amount_paid         // Confirmed amount paid
```

### Calculation Logic
```php
$total = $order->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity);
$dpAmount = round($total * 50 / 100, 2);        // 50% of total
$remainingPayment = $total - $dpAmount;         // 50% of total
```

---

## Implementation Details

### 1. Payment Model (`app/Models/Payment.php`)

**New Constants:**
```php
const STATUS_FULL_PENDING = 'full_pending';
```

**New Methods:**
```php
public function isFullPending(): bool
{
    return $this->payment_status === self::STATUS_FULL_PENDING;
}
```

**Updated Methods:**
```php
public function statusLabel(): string
{
    return match($this->payment_status) {
        self::STATUS_PENDING => 'Belum Dibayar',
        self::STATUS_DP_PAID => 'DP Sudah Dibayar',
        self::STATUS_FULL_PENDING => 'Menunggu Konfirmasi Pelunasan',
        self::STATUS_PAID => 'Pembayaran Lunas',
        self::STATUS_FAILED => 'Pembayaran Ditolak',
        default => 'Unknown',
    };
}

public function statusColor(): string
{
    return match($this->payment_status) {
        self::STATUS_PENDING => 'danger',
        self::STATUS_DP_PAID => 'info',
        self::STATUS_FULL_PENDING => 'warning',
        self::STATUS_PAID => 'success',
        self::STATUS_FAILED => 'danger',
        default => 'secondary',
    };
}
```

### 2. Payment Service (`app/Services/PaymentService.php`)

**Updated Method - verifyManualTransfer():**
```php
public function verifyManualTransfer(Payment $payment): void
{
    if ($payment->payment_status === Payment::STATUS_PENDING) {
        // DP payment received
        $payment->update([
            'payment_status' => Payment::STATUS_DP_PAID,
            'payment_proof_dp' => $payment->payment_proof,
            'payment_proof' => null,
        ]);
    } elseif ($payment->payment_status === Payment::STATUS_DP_PAID) {
        // Final payment received - set to FULL_PENDING (NOT direct PAID)
        $payment->update([
            'payment_status' => Payment::STATUS_FULL_PENDING,
            'payment_proof_full' => $payment->payment_proof,
            'payment_proof' => null,
        ]);
    }
}
```

**New Method - confirmFinalPayment():**
```php
public function confirmFinalPayment(Payment $payment): void
{
    if ($payment->payment_status !== Payment::STATUS_FULL_PENDING) {
        throw new Exception('Payment is not in FULL_PENDING status');
    }

    DB::transaction(function () use ($payment) {
        $payment->update([
            'payment_status' => Payment::STATUS_PAID,
            'payment_date' => now(),
            'amount_paid' => $payment->amount,
        ]);

        // Update order status to confirmed
        $payment->order->update(['order_status' => 'confirmed']);
    });
}
```

### 3. Customer Payment Upload Form (`resources/views/customer/payment/index.blade.php`)

**Form Disable Logic:**
```blade
@php
    $isFullPendingUi = $order->payment?->payment_status === \App\Models\Payment::STATUS_FULL_PENDING;
@endphp

@if ($isFullPendingUi)
    <div class="alert alert-warning">
        <h5>Jangan Unggah Bukti Lagi</h5>
        <p>Bukti pelunasan Anda sudah diterima dan sedang menunggu verifikasi admin.
           Proses verifikasi biasanya memakan waktu 1-3 hari kerja.</p>
    </div>
    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-secondary">
        Kembali ke Pesanan
    </a>
@else
    <!-- Payment upload form here -->
@endif
```

### 4. Customer Order View - FULL_PENDING Section (`resources/views/customer/orders/show.blade.php`)

**Features:**
- Warning alert: "Menunggu Konfirmasi Pelunasan"
- **Left Column (col-lg-7):**
  - Ringkasan Pembayaran card showing:
    - Total Pesanan
    - DP 50% with ✓ verified badge
    - Pelunasan 50% with ⏳ pending badge
  - Status verification info box
  - Timeline showing Day 0-3 verification schedule
  
- **Right Column (col-lg-5):**
  - Proof images section:
    - DP proof (✓ Terverifikasi)
    - Pelunasan proof (⏳ Menunggu Verifikasi)
  - Action box with confirmation message
  - Modal viewers for each proof

**Key Code:**
```blade
@php
    $isFullPending = $paySt === \App\Models\Payment::STATUS_FULL_PENDING;
    $dpAmount = round($calculatedTotal * 50 / 100, 2);
    $remainingPayment = $calculatedTotal - $dpAmount;
@endphp

@if ($isFullPending)
    <!-- Alert -->
    <div class="alert alert-warning">
        <h5>Menunggu Konfirmasi Pelunasan</h5>
    </div>
    
    <!-- Two-column layout -->
    <div class="row">
        <div class="col-lg-7">
            <!-- Payment details and timeline -->
        </div>
        <div class="col-lg-5">
            <!-- Proof images -->
        </div>
    </div>
@endif
```

### 5. Admin Payment Show View (`resources/views/admin/payments/show.blade.php`)

**Features:**
- Status badge: "Menunggu Konfirmasi Pelunasan" (yellow warning)
- **Left Column (col-lg-8):**
  - DP Proof card (✓ Terverifikasi)
  - Pelunasan Proof card (⏳ Menunggu Verifikasi) - **Only for FULL_PENDING**
  
- **Right Column (col-lg-4):**
  - Detail card with order, customer, method info
  - **NEW - Payment Breakdown** (for FULL_PENDING):
    - DP (50%) in green box with ✓ verified
    - Remaining (50%) in yellow box with ⏳ pending
    - Total in dark box
  - **NEW - Action Card** (for FULL_PENDING):
    - "Konfirmasi Pelunasan → LUNAS" button
    - Reject button
    - Verification checklist

**Verification Checklist:**
- ✓ DP sudah terverifikasi
- Periksa bukti pelunasan jelas dan terbaca
- Pastikan jumlah sesuai: Rp [amount]
- Verifikasi nama pengirim & rekening tujuan

### 6. Payment Controller (`app/Http/Controllers/Customer/PaymentController.php`)

**New Method:**
```php
public function confirmFinalPayment(Request $request, Payment $payment): RedirectResponse
{
    try {
        if ($payment->payment_status !== Payment::STATUS_FULL_PENDING) {
            return back()->with('error', 'Pembayaran tidak dalam status menunggu konfirmasi pelunasan.');
        }

        $this->paymentService->confirmFinalPayment($payment);
        $order = $payment->order;
        $note = '[' . now()->format('d/m/Y H:i') . '] Pelunasan dikonfirmasi oleh Admin.';
        $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Pelunasan berhasil dikonfirmasi.');
    } catch (\Exception $e) {
        Log::error('Final payment confirmation failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Gagal mengkonfirmasi pelunasan: ' . $e->getMessage());
    }
}
```

### 7. Routes (`routes/web.php`)

```php
Route::prefix('payments')->name('payments.')->controller(CustomerPaymentController::class)->group(function () {
    Route::get('/pending', 'pendingVerification')->name('pending');
    Route::get('/{payment}', 'show')->name('show');
    Route::post('/{payment}/verify', 'verify')->name('verify');
    Route::post('/{payment}/reject', 'reject')->name('reject');
    Route::post('/{payment}/confirm-final', 'confirmFinalPayment')->name('confirm-final');
});
```

---

## Workflow Examples

### Example 1: Happy Path (Complete Payment Flow)

**Day 0 - Customer Uploads DP:**
1. Order created → Status: PENDING
2. Customer views order, sees DP form enabled
3. Customer uploads DP proof
4. Payment status changes to PENDING (temporary)
5. Admin verifies DP proof
6. Admin clicks "Setujui Pembayaran"
7. Payment status changes to DP_PAID
8. Customer receives notification

**Day 0 (Evening) - Customer Uploads Final Payment:**
1. Customer logs in, sees "DP Sudah Dibayar" status
2. Customer uploads final payment (pelunasan) proof
3. Payment status changes to FULL_PENDING
4. **Upload form DISABLED** to prevent re-upload
5. Customer sees detailed breakdown with both proofs

**Day 1 - Admin Verifies:**
1. Admin sees "Menunggu Konfirmasi Pelunasan" alert
2. Admin reviews both proofs:
   - ✓ DP proof (already verified)
   - ⏳ Pelunasan proof (under review)
3. Admin checks verification checklist
4. Admin clicks "Konfirmasi Pelunasan → LUNAS"
5. Payment status changes to PAID
6. Order status changes to "confirmed"
7. Production can begin

### Example 2: Customer Forgot to Upload Final Payment

1. DP is verified, status: DP_PAID
2. Customer sees upload form, can upload pelunasan
3. But what if customer closes browser without uploading?
4. Customer can return anytime and upload pelunasan
5. Once uploaded → FULL_PENDING
6. Then admin confirms → PAID

### Example 3: Admin Rejects Pelunasan Proof

1. Customer uploads pelunasan → FULL_PENDING
2. Admin sees proof is unclear/wrong amount
3. Admin clicks "Tolak Pembayaran"
4. Admin enters reason: "Bukti tidak jelas, mohon upload ulang"
5. Payment status changes to FAILED
6. Customer receives notification with reason
7. Customer can upload correct proof again
8. Process repeats

---

## UI Components

### Customer View - Payment Status Badges

```
PENDING:         🔴 Belum Dibayar
DP_PAID:         🔵 DP Sudah Dibayar
FULL_PENDING:    🟡 Menunggu Konfirmasi Pelunasan
PAID:            🟢 Pembayaran Lunas
FAILED:          🔴 Pembayaran Ditolak
```

### Admin View - Proof Image Display

**FULL_PENDING Status:**
```
┌─────────────────────────────────┐
│ Bukti Transfer DP (50%)          │
│ ✓ Terverifikasi                  │
│ [Image Display]                  │
│ (Click to enlarge)               │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Bukti Transfer Pelunasan (50%)   │
│ PENDING VERIFIKASI               │
│ ⏳ Menunggu Verifikasi           │
│ [Image Display]                  │
│ (Click to enlarge)               │
└─────────────────────────────────┘
```

### Admin View - Payment Breakdown

```
✓ DP (50%) - TERVERIFIKASI
  Rp 500.000.000
  [Green background box]

⏳ PELUNASAN (50%) - PENDING
  Rp 500.000.000
  [Yellow background box]

TOTAL PESANAN
  Rp 1.000.000.000
  [Dark background box]
```

---

## Testing Checklist

- [ ] Order created → Status: PENDING
- [ ] Customer uploads DP proof → Proof saved, form still enabled
- [ ] Admin verifies DP → Status: DP_PAID, customer notified
- [ ] Customer uploads pelunasan → Status: FULL_PENDING
- [ ] Upload form disabled when FULL_PENDING
- [ ] Admin sees both proofs in show view
- [ ] Admin sees payment breakdown in sidebar
- [ ] Admin clicks "Konfirmasi Pelunasan" → Status: PAID
- [ ] Order status changes to "confirmed"
- [ ] Customer sees PAID status in their order
- [ ] Admin notes updated with confirmation timestamp
- [ ] Rejecting pelunasan works and returns to FAILED status
- [ ] Customer can re-upload after rejection
- [ ] All status transitions correct
- [ ] All status badges display correctly

---

## Security Considerations

1. **Status Validation:** confirmFinalPayment checks status before allowing transition
2. **Authorization:** Only authenticated users can upload/verify payments
3. **Proof Storage:** Proofs stored in protected storage directory
4. **Transaction Safety:** confirmFinalPayment wrapped in DB transaction
5. **Notes Logging:** Admin actions logged in order notes for audit trail

---

## Future Enhancements

1. **Email Notifications:** Send customer/admin notifications on status changes
2. **SMS Alerts:** Send SMS when payment verification timeline exceeded
3. **Payment Proof Expiry:** Auto-reject proofs older than 30 days if not verified
4. **Bulk Actions:** Admin bulk approve/reject multiple pending payments
5. **Payment Reminders:** Auto-send reminder emails if DP not uploaded after X days
6. **Analytics:** Dashboard showing average verification time, rejection rate, etc.

---

## Support & Troubleshooting

### Issue: Customer can't see upload form after DP uploaded
**Solution:** Ensure `payment_status` is correctly set to `DP_PAID` after admin verification.

### Issue: Admin sees blank proof images
**Solution:** Check that `payment_proof_dp` and `payment_proof_full` are correctly set. Verify file path starts with `storage/`.

### Issue: Confirm button not appearing for admin
**Solution:** Check that `$isFullPending` variable is correctly computed and payment status is exactly `full_pending`.

### Issue: Order status not changing to 'confirmed'
**Solution:** Ensure `confirmFinalPayment` method in PaymentService is updating order status in transaction.

---

## Files Modified/Created

**Modified:**
- `app/Models/Payment.php` - Added FULL_PENDING status handling
- `app/Services/PaymentService.php` - Updated verifyManualTransfer(), added confirmFinalPayment()
- `resources/views/customer/orders/show.blade.php` - Enhanced FULL_PENDING section
- `resources/views/admin/payments/show.blade.php` - Added FULL_PENDING support with breakdown
- `app/Http/Controllers/Customer/PaymentController.php` - Added confirmFinalPayment()
- `routes/web.php` - Added confirm-final route

**Already Implemented:**
- `resources/views/customer/payment/index.blade.php` - Form disable logic for FULL_PENDING

---

## Commit History

**Commit 1:** feat: add FULL_PENDING status and prevent double payment uploads
- Added STATUS_FULL_PENDING constant
- Updated PaymentService::verifyManualTransfer()
- Added PaymentService::confirmFinalPayment()
- Disabled payment form when FULL_PENDING

**Commit 2:** feat: enhance admin payment verification with FULL_PENDING status and detailed breakdown
- Enhanced customer FULL_PENDING section with breakdown & proofs
- Enhanced admin payments show view with FULL_PENDING support
- Added confirmFinalPayment controller method
- Added confirm-final route
- Improved visual hierarchy with Bootstrap 5 styling

---

## Document Version
- **Version:** 1.0
- **Last Updated:** 2024
- **Status:** Complete Implementation
