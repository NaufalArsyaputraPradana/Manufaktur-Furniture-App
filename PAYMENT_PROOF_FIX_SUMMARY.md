# 🔧 Payment Proof Display Fix - Summary

## Problem Identified ❌
Payment proof images (bukti pembayaran) were NOT displaying on order detail pages despite:
- Code modifications being in place
- Database migration creating new fields
- Images being uploaded successfully

**Root Cause:** 
- In `PaymentService.php`, the `verifyManualTransfer()` method was **DELETING payment proof files** and setting `payment_proof` field to NULL after verification
- Customer/Orders view was trying to display data from NULL fields

## Solution Implemented ✅

### 1. **PaymentService.php Changes**

#### `verifyManualTransfer()` Method (Lines 143-191)
**Before:** 
```php
// BAD: Deletes file from storage!
if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
    Storage::disk('public')->delete($payment->payment_proof);
}
$payment->update([
    'payment_status' => Payment::STATUS_DP_PAID,
    'payment_proof'  => null,  // ❌ This deleted the proof!
]);
```

**After:**
```php
// GOOD: Moves file to permanent field
$proofPath = $payment->payment_proof;
$payment->update([
    'payment_status' => Payment::STATUS_DP_PAID,
    'payment_proof_dp' => $proofPath,  // ✅ Save to DP field
    'payment_proof' => null,            // Clear from temporary field
]);
```

#### `rejectPayment()` Method (Lines 193-220)
**Changed:** Now deletes all three proof fields when rejecting
```php
// Delete from all three possible fields
Storage::disk('public')->delete($payment->payment_proof);
Storage::disk('public')->delete($payment->payment_proof_dp);
Storage::disk('public')->delete($payment->payment_proof_full);

// Clear all proof fields
$payment->update([
    'payment_proof' => null,
    'payment_proof_dp' => null,
    'payment_proof_full' => null,
]);
```

### 2. **Customer/Orders Show View Changes**

#### DP_PAID Status Section (Lines ~855-910)
**Updated:** Now checks `payment_proof_dp` field (with fallback to `payment_proof`)
```php
@php
    $proofPath = null;
    // For DP_PAID status, check payment_proof_dp field first, then payment_proof
    if ($order->payment?->payment_proof_dp) {
        $proof = $order->payment->payment_proof_dp;
        // ... path handling ...
    } elseif ($order->payment?->payment_proof) {
        // Fallback to payment_proof if payment_proof_dp is not yet migrated
        $proof = $order->payment->payment_proof;
        // ... path handling ...
    }
@endphp
```

#### PAID Status Section (Lines ~720-830)
**Updated:** Now shows BOTH proofs in a single card with 2 sections
- **Top Section:** Bukti Transfer DP (50%) - from `payment_proof_dp`
- **Bottom Section:** Bukti Transfer Pelunasan (50%) - from `payment_proof_full`

Each section includes:
- Image preview with hover effect
- "Lihat" (View) button - opens modal for full-screen viewing
- "DL" (Download) button - downloads the proof

### 3. **Admin Orders Show View**
**Status:** ✅ Already implemented correctly
- Already checks `payment_proof_dp` and `payment_proof_full` fields
- Displays both proofs when STATUS_PAID
- Proper path handling with 3 format support
- Clickable images that open modal viewer

## Database Schema ✅

The migration `2026_03_29_141500_add_payment_proofs_to_payments_table.php` creates:
```sql
ALTER TABLE payments
ADD COLUMN payment_proof_dp VARCHAR(255) NULL AFTER payment_proof
ADD COLUMN payment_proof_full VARCHAR(255) NULL AFTER payment_proof_dp
```

Migration Status: **✅ APPLIED** (Batch 5)

## Payment Flow After Fix

### DP Payment Flow:
```
1. User uploads DP proof
   ↓ Saved to: payment_proof (temporary)
2. Admin verifies in admin/payments/show
   ↓ PaymentService.verifyManualTransfer() called
3. File moved: payment_proof → payment_proof_dp
4. Status changed: PENDING → DP_PAID
   ↓ payment_proof set to NULL
5. Customer views order → payment_proof_dp displays ✅
```

### Full Payment (2-Step) Flow:
```
1. User uploads remaining payment proof
   ↓ Saved to: payment_proof (temporary)
2. Admin verifies
   ↓ PaymentService.verifyManualTransfer() called
3. File moved: payment_proof → payment_proof_full
4. Status changed: DP_PAID → PAID
   ↓ payment_proof set to NULL
5. Customer views order → Shows BOTH:
   - payment_proof_dp (DP proof) ✅
   - payment_proof_full (Final payment proof) ✅
```

## Testing Checklist

- [ ] **Payment Proof Display - DP Status**
  - Create order, upload DP proof, admin verifies
  - Check: Customer order detail shows payment_proof_dp image
  - Check: Admin order detail shows DP proof image

- [ ] **Payment Proof Display - PAID Status**
  - Complete DP payment, upload final proof, admin verifies
  - Check: Customer order detail shows BOTH images
  - Check: Admin order detail shows BOTH images
  - Check: Modal opens when clicking images
  - Check: Download buttons work

- [ ] **Payment Rejection**
  - Upload proof, reject from admin/payments/show
  - Check: All proof files deleted from storage
  - Check: payment_proof, payment_proof_dp, payment_proof_full all NULL
  - Check: File no longer visible on customer/admin order detail

- [ ] **Path Handling**
  - Verify images display correctly (not 404)
  - Check all 3 path formats work:
    - `storage/payment-proofs/xxx.jpg` ✅
    - `/payment-proofs/xxx.jpg` ✅
    - `payment-proofs/xxx.jpg` ✅

- [ ] **Legacy Fallback**
  - Verify old payments still show if payment_proof_full is NULL
  - Should fallback to payment_proof for backward compatibility

## Files Modified

1. **app/Services/PaymentService.php**
   - verifyManualTransfer() method
   - rejectPayment() method

2. **resources/views/customer/orders/show.blade.php**
   - DP_PAID status section (lines ~855-910)
   - PAID status section (lines ~720-830)
   - Added proper field checking

3. **resources/views/admin/orders/show.blade.php**
   - ✅ Already correct (no changes needed)

## Notes

- **Storage Symlink:** Make sure `php artisan storage:link` has been run
- **Backup:** Old payments that only have `payment_proof` field will fallback correctly
- **Future:** Consider adding data migration to move existing `payment_proof` to appropriate new fields

---

**Status:** ✅ READY FOR TESTING

**Next Steps:**
1. Test the DP payment flow
2. Test the 2-step payment flow (DP + Final)
3. Test payment rejection
4. Verify modal opening/downloading works
