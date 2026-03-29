# Final Payment Confirmation System

## Overview

Sistem konfirmasi pembayaran pelunasan yang baru mencegah customer dari upload bukti pembayaran ganda dan memberikan alur workflow yang jelas untuk admin.

## Status Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    PAYMENT WORKFLOW                          │
└─────────────────────────────────────────────────────────────┘

INITIAL STATE
    │
    ├─→ [PENDING] ◄─── Customer upload DP proof
    │      ▲
    │      │ Admin verify DP
    │      │
    │    [DP_PAID] ◄─── DP terverifikasi
    │      ▲
    │      │ Customer upload pelunasan proof
    │      │
    │    [FULL_PENDING] ◄─── Pelunasan diterima (NEW!)
    │      ▲
    │      │ Admin verify pelunasan (confirmFinalPayment)
    │      │
    │    [PAID] ◄─── Pembayaran lunas, produksi mulai
    │      │
    │      └─→ SELESAI

ALTERNATIVE: Pelunasan Sekaligus
    │
    ├─→ [PENDING] ◄─── Customer upload full payment proof
    │      ▲
    │      │ Admin verify full payment
    │      │
    │    [PAID] ◄─── Pembayaran lunas, produksi mulai
    │      │
    │      └─→ SELESAI
```

## Database Changes

### Payment Model Constants

```php
public const STATUS_PENDING = 'pending';
public const STATUS_DP_PAID = 'dp_paid';
public const STATUS_FULL_PENDING = 'full_pending';  // NEW
public const STATUS_PAID = 'paid';
public const STATUS_FAILED = 'failed';
```

### Status Labels

| Status | Label | Color | Description |
|--------|-------|-------|-------------|
| pending | Menunggu Pembayaran / Verifikasi | warning | Menunggu customer upload atau admin verifikasi |
| dp_paid | DP telah diverifikasi | info | DP sudah dikonfirmasi, tunggu pelunasan |
| full_pending | Menunggu Konfirmasi Pelunasan | warning | Bukti pelunasan diterima, tunggu admin verifikasi |
| paid | Lunas | success | Pembayaran selesai, produksi siap dimulai |
| failed | Gagal / Ditolak | danger | Pembayaran ditolak atau gagal |

## Implementation Details

### 1. Payment Model (`app/Models/Payment.php`)

#### New Constants
```php
public const STATUS_FULL_PENDING = 'full_pending';
```

#### New Methods
```php
/**
 * Check if payment is waiting for final confirmation
 */
public function isFullPending(): bool
{
    return $this->payment_status === self::STATUS_FULL_PENDING;
}
```

#### Updated Methods
- `statusLabel()`: Menambah case untuk `STATUS_FULL_PENDING`
- `statusColor()`: Menambah mapping warna untuk `STATUS_FULL_PENDING`

### 2. PaymentService (`app/Services/PaymentService.php`)

#### Updated Method: `verifyManualTransfer()`

**Perubahan Behavior:**

Ketika status saat ini adalah `DP_PAID` dan ada file bukti pelunasan:

**BEFORE:**
```php
// Langsung set PAID (WRONG!)
$payment->update([
    'payment_status' => Payment::STATUS_PAID,
    'amount_paid'    => $total,
    'payment_date'   => now(),
    'payment_proof_full' => $payment->payment_proof,
]);
```

**AFTER:**
```php
// Set ke FULL_PENDING untuk tunggu konfirmasi admin
$payment->update([
    'payment_status' => Payment::STATUS_FULL_PENDING,
    'payment_proof_full' => $payment->payment_proof,
    'payment_proof'  => null,
]);
```

#### New Method: `confirmFinalPayment()`

```php
/**
 * Confirm final payment (FULL_PENDING → PAID).
 * Called by admin after verifying the final payment proof.
 * 
 * @param Payment $payment
 * @throws InvalidArgumentException if status is not FULL_PENDING
 */
public function confirmFinalPayment(Payment $payment): void
{
    DB::transaction(function () use ($payment) {
        if ($payment->payment_status !== Payment::STATUS_FULL_PENDING) {
            throw new \InvalidArgumentException(
                'Pembayaran harus dalam status menunggu konfirmasi.'
            );
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
```

**Usage:**
```php
// Di Admin Payment Controller
$paymentService->confirmFinalPayment($payment);
```

### 3. Customer Order View (`resources/views/customer/orders/show.blade.php`)

#### Pre-computed Variables
```php
$isFullPending = $paySt === \App\Models\Payment::STATUS_FULL_PENDING;
```

#### New Section: FULL_PENDING Status Block

**Displayed when:** `$order->payment?->payment_status === STATUS_FULL_PENDING`

**Shows:**
- Alert: "Menunggu Konfirmasi Pelunasan"
- Payment details dengan breakdown DP vs Pelunasan
- Warning message: "Jangan upload bukti lagi"
- Proof image dengan view/download options
- Info: "Setelah verifikasi, pembayaran akan dikonfirmasi sebagai LUNAS"

**Visual:**
```
┌─────────────────────────────────────────┐
│ ⏳ Menunggu Konfirmasi Pelunasan        │
├─────────────────────────────────────────┤
│ Detail Pembayaran:                      │
│  Total Pesanan:      Rp 2.800.000       │
│  DP Verified (50%):  Rp 1.400.000  ✓   │
│  Pelunasan (50%):    Rp 1.400.000  ⏳  │
│                                         │
│ Bukti Transfer Pelunasan:               │
│ [Preview Image]                         │
│ [View] [Download]                       │
│                                         │
│ ℹ️ Jangan upload bukti lagi             │
│    Bukti telah diterima dan diproses    │
└─────────────────────────────────────────┘
```

#### Status Badge Update
```php
$order->status === 'pending' && $isFullPending 
    => ['bg-warning text-dark', 'bi-hourglass-split', 'Tunggu Konfirmasi']
```

### 4. Payment Page (`resources/views/customer/payment/index.blade.php`)

#### New Variable
```php
$isFullPendingUi = $payUi && $payUi->payment_status === \App\Models\Payment::STATUS_FULL_PENDING;
```

#### Conditional Block

**When FULL_PENDING:**
```blade
@if($isFullPendingUi)
    <div class="alert alert-warning">
        <h5>Menunggu Konfirmasi Pelunasan</h5>
        <p>Bukti pelunasan Anda telah diterima dan sedang menunggu verifikasi admin.</p>
        <p class="small text-muted">
            <strong>Jangan unggah bukti lagi.</strong> Tim kami sedang memproses pembayaran Anda.
        </p>
    </div>
    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary">
        Kembali ke Pesanan
    </a>
@else
    <!-- Normal payment form -->
@endif
```

**Result:**
- Form upload bukti **DISABLED**
- Button submit untuk upload **HIDDEN**
- Clear message kepada customer

## Integration with Admin Panel

### Workflow untuk Admin

1. **View Pending Payments**
   - Admin melihat payments dengan status `FULL_PENDING`
   - Lihat bukti pelunasan yang diupload

2. **Verify Final Payment**
   - Admin verifikasi bukti pelunasan
   - Click "Konfirmasi Pembayaran Lunas" button

3. **Confirm Payment**
   - System call `PaymentService::confirmFinalPayment()`
   - Status berubah: `FULL_PENDING` → `PAID`
   - Order status auto-update ke `confirmed`
   - Produksi dapat dimulai

### Required Admin Controller Updates

```php
// Di Admin Payment Controller (payment.confirm atau similar)
public function confirmFinalPayment(Request $request, Payment $payment)
{
    try {
        $this->paymentService->confirmFinalPayment($payment);
        
        $order = $payment->order;
        $note = '[' . now()->format('d/m/Y H:i') . '] Pelunasan dikonfirmasi oleh Admin.';
        $order->update(['admin_notes' => trim(($order->admin_notes ?? '') . "\n" . $note)]);

        return redirect()->route('admin.payments.pending')
            ->with('success', 'Pembayaran pelunasan berhasil dikonfirmasi sebagai LUNAS.');
    } catch (\Exception $e) {
        Log::error('Payment confirmation failed', [
            'payment_id' => $payment->id,
            'error' => $e->getMessage()
        ]);
        return back()->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
    }
}
```

## Customer Experience Flow

### Scenario: DP + Pelunasan (2 Step Payment)

**Step 1: Upload DP**
```
Customer melihat: "Unggah Bukti Pelunasan" button aktif
Customer upload DP proof
Status berubah ke: DP_PAID
```

**Step 2: Upload Pelunasan**
```
Customer click "Unggah Bukti Pelunasan"
Customer upload pelunasan proof
Status berubah ke: FULL_PENDING
Order detail page menampilkan:
  - Alert: "Menunggu Konfirmasi Pelunasan"
  - Button "Unggah Bukti Pelunasan" DISABLED
  - Message: "Jangan upload bukti lagi"
```

**Step 3: Admin Verify (dari sisi Admin)**
```
Admin verifikasi bukti pelunasan
Status berubah ke: PAID
Customer melihat: "Pembayaran Berhasil - Pesanan Lunas"
Produksi dimulai
```

## Benefits

✅ **Prevent Double Upload**
- Customer tidak bisa upload bukti pelunasan lebih dari 1x
- Form automatically disabled setelah upload pertama

✅ **Clear Status Tracking**
- Customer tahu kapan pembayaran sedang diproses
- Tidak ada kebingungan tentang status pembayaran

✅ **Structured Workflow**
- Admin punya langkah yang jelas untuk confirm pembayaran
- Prevent accidental double-payment confirmation

✅ **Better UX**
- Customer mendapat feedback yang jelas
- Warning message mencegah confusion

✅ **Data Integrity**
- Transactional updates mencegah race condition
- Payment proof files properly tracked

## Testing Checklist

- [ ] Upload DP → status becomes DP_PAID
- [ ] Customer view payment page → button "Unggah Bukti Pelunasan" appears
- [ ] Upload pelunasan proof → status becomes FULL_PENDING
- [ ] Payment page reload → form disabled, message shows
- [ ] Order detail page → FULL_PENDING section displays
- [ ] Admin verify → status becomes PAID
- [ ] Customer order detail → shows PAID status
- [ ] Try upload again in FULL_PENDING state → form disabled

## Database Migration

Tidak perlu migration baru - column `payment_status` sudah ENUM, hanya tambah value:

```sql
ALTER TABLE payments 
MODIFY COLUMN payment_status ENUM(
    'pending', 
    'dp_paid', 
    'full_pending',  -- NEW
    'paid', 
    'failed'
);
```

## Future Enhancements

1. **Email Notification**
   - Kirim email ke customer saat status berubah ke FULL_PENDING
   - Kirim email ke customer saat confirmed PAID

2. **Admin Dashboard Widget**
   - Show count of FULL_PENDING payments
   - Priority queue untuk FULL_PENDING payments

3. **Auto-Confirm Option**
   - Admin bisa set auto-confirm untuk payment tertentu
   - Useful untuk trusted customers

4. **Payment Timeout**
   - Auto-reject jika FULL_PENDING > 7 hari
   - Notify customer untuk reupload

5. **SMS Notification**
   - SMS ke customer notification saat berubah status
   - SMS ke admin notification untuk pending confirmation

## Troubleshooting

### Issue: Status tidak berubah ke FULL_PENDING
**Cause:** Payment channel tidak diset ke `CHANNEL_MANUAL_FULL`
**Solution:** Pastikan payment channel correct saat create payment

### Issue: confirmFinalPayment() throws error
**Cause:** Payment status bukan FULL_PENDING
**Solution:** Check status sebelum call confirmFinalPayment()

### Issue: Customer bisa upload lagi setelah FULL_PENDING
**Cause:** Cache atau stale page view
**Solution:** Clear browser cache, refresh page

## References

- `app/Models/Payment.php` - Payment model with constants
- `app/Services/PaymentService.php` - Business logic for payment
- `resources/views/customer/orders/show.blade.php` - Customer order view
- `resources/views/customer/payment/index.blade.php` - Customer payment page
