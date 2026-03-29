# Phase 3B: Component Integration Guide

**Phase:** 3B - Template Refactoring  
**Status:** 🚀 READY TO START  
**Duration:** 2-3 hours  
**Target:** Integrate 6 components into all template files

---

## Integration Strategy

### High Priority Files (Start Here)
1. **customer/products/index.blade.php** (ProductCard - 10+ usages)
2. **customer/orders/index.blade.php** (OrderStatusBadge, OrderItemCard - 15+ usages)
3. **admin/orders/index.blade.php** (OrderStatusBadge, OrderItemCard - 10+ usages)

### Medium Priority Files
4. **customer/cart/index.blade.php** (OrderItemCard variant)
5. **admin/payments/index.blade.php** (PaymentBadge - 8+ usages)
6. **All form files** (FormInput - 50+ usages)

### Low Priority Files (Polish)
7. **Production views** (Minor updates)
8. **Email templates** (No changes needed)

---

## File 1: customer/products/index.blade.php

### Current State
- Lines 280-350: Product card grid with repeated 80-line blocks
- ProductCard markup appears 15+ times for product listings
- **Current code:** ~1,200 lines (with all product cards duplicated)

### Integration Plan
Replace the entire product card section with:
```blade
<div class="row g-4">
    @forelse ($products as $product)
        <div class="col-md-6 col-lg-4">
            <x-product-card :product="$product" />
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">Tidak ada produk yang ditemukan.</div>
        </div>
    @endforelse
</div>
```

### Expected Changes
- **Lines removed:** ~200 lines (product card markup)
- **Lines added:** ~10 lines (component loop)
- **Reduction:** 95%+
- **Functionality:** 100% preserved

### Integration Steps
1. Locate product grid section (lines 260-770)
2. Replace with ProductCard component loop
3. Verify all product properties passed correctly
4. Test in browser - check images, badges, buttons
5. Verify responsive layout on mobile

---

## File 2: customer/orders/index.blade.php

### Current State
- Lines 85-120: OrderStatusBadge complex conditional logic
- Lines 145-220: OrderItemCard repeated 75-line blocks
- OrderItemCard appears 10+ times (one per order item)
- **Current code:** ~627 lines total

### Integration Plan

**A. Replace OrderStatusBadge (lines 85-120)**
```blade
<!-- Replace this massive if/elseif block: -->
@php
    $isPaid = $order->payment?->payment_status === \App\Models\Payment::STATUS_PAID;
    $isDpPaid = $order->payment?->payment_status === \App\Models\Payment::STATUS_DP_PAID;
    $badgeClass = match (true) { ... }
@endphp
<span class="badge {{ $badgeClass }} ...">...</span>

<!-- With this: -->
<x-order-status-badge 
    :status="$order->status"
    :payment="$order->payment"
    :isPaid="$isPaid"
    :isDpPaid="$isDpPaid" />
```

**B. Replace OrderItemCard (lines 145-220)**
```blade
<!-- Replace this 75-line block: -->
<div class="card border border-light rounded-3 mb-3 ...">
    <!-- all the nested divs and logic -->
</div>

<!-- With this: -->
@forelse ($order->orderDetails as $detail)
    <x-order-item-card :detail="$detail" />
@empty
    <p class="text-muted">Tidak ada item.</p>
@endforelse
```

### Expected Changes
- **Lines removed:** ~150 lines (OrderStatusBadge + OrderItemCard logic)
- **Lines added:** ~20 lines (component calls)
- **Reduction:** 88%+
- **Functionality:** 100% preserved, improved maintainability

### Integration Steps
1. Replace OrderStatusBadge logic with component (lines 85-120)
2. Test status badge colors and icons for all variants
3. Replace OrderItemCard loop (lines 145-220)
4. Test custom order details display
5. Verify pricing calculations
6. Mobile responsive test

---

## File 3: admin/orders/index.blade.php

### Current State
- Similar structure to customer orders
- OrderStatusBadge and OrderItemCard used repeatedly
- Additional admin-specific actions

### Integration Plan
Similar to customer/orders/index.blade.php but:
1. Keep admin-specific actions (edit, delete buttons)
2. Replace status and item card display
3. Maintain admin-specific styling

```blade
<!-- Admin order row with component -->
<tr>
    <td>#{{ $order->order_number }}</td>
    <td>
        <x-order-status-badge :status="$order->status" :payment="$order->payment" />
    </td>
    <td>
        @foreach ($order->orderDetails as $detail)
            <x-order-item-card :detail="$detail" />
        @endforeach
    </td>
    <td>
        <!-- Admin actions -->
        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">Edit</a>
    </td>
</tr>
```

### Expected Changes
- **Lines removed:** ~120 lines
- **Lines added:** ~15 lines
- **Reduction:** 89%+

---

## File 4: Form Files Integration

### Files to Update
- `customer/checkout/form.blade.php`
- `admin/products/form.blade.php`
- `admin/categories/form.blade.php`
- `admin/settings/form.blade.php`
- etc.

### Current Pattern
```blade
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" id="email" name="email" class="form-control" 
        value="{{ old('email') }}" required>
    @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
```

### Integration Pattern
```blade
<x-form-input 
    name="email"
    label="Email"
    type="email"
    placeholder="your@email.com"
    :value="old('email')"
    :errors="$errors"
    required />
```

### Expected Changes
- **Lines per form field:** 15 → 1 (93% reduction)
- **50+ fields affected** across all forms
- **Total lines removed:** ~500+ lines
- **Functionality:** Enhanced (automatic validation display)

### Integration Steps for Each Form File
1. Identify all input field blocks
2. Replace with FormInput component
3. Maintain form structure and styling
4. Test validation error display
5. Verify form submission works

---

## File 5: Payment Display Integration

### Files to Update
- `admin/payments/index.blade.php` (payment listing)
- `customer/orders/show.blade.php` (order details)
- `admin/dashboard.blade.php` (dashboard stats)

### Integration Example
```blade
<!-- Before -->
<div class="d-flex align-items-center gap-2">
    <span class="badge {{ $payment->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
        {{ ucfirst($payment->payment_status) }}
    </span>
    <span>Rp {{ number_format($payment->amount, 0) }}</span>
    <span>{{ $payment->payment_channel }}</span>
</div>

<!-- After -->
<x-payment-badge :payment="$payment" />
```

---

## File 6: Delete Confirmation Dialogs

### Files to Update
- `admin/products/index.blade.php`
- `admin/orders/index.blade.php`
- `admin/payments/index.blade.php`
- `admin/categories/index.blade.php`

### Integration Pattern
```blade
<!-- Trigger Button -->
<button type="button" class="btn btn-sm btn-danger" 
    data-bs-toggle="modal" data-bs-target="#deleteProduct{{ $product->id }}">
    Delete
</button>

<!-- Form + Modal -->
<form action="{{ route('products.destroy', $product) }}" method="POST">
    @csrf
    @method('DELETE')
    
    <x-confirm-dialog 
        id="deleteProduct{{ $product->id }}"
        title="Delete Product?"
        message="This action cannot be undone. The product will be permanently deleted."
        buttonText="Yes, Delete"
        buttonClass="btn-danger" />
</form>
```

---

## Integration Checklist

### Phase 3B Integration Tasks

#### Task 1: ProductCard Integration ✅ Ready
- [ ] Open `customer/products/index.blade.php`
- [ ] Locate product grid section (lines 260-770)
- [ ] Replace with ProductCard component loop
- [ ] Test in browser on desktop
- [ ] Test on mobile (responsive)
- [ ] Verify image fallback works
- [ ] Verify all product properties display

#### Task 2: OrderStatusBadge Integration ✅ Ready
- [ ] Open `customer/orders/index.blade.php`
- [ ] Remove conditional badge logic (lines 85-120)
- [ ] Replace with component (3 lines)
- [ ] Test all status variants:
  - [ ] pending (no payment)
  - [ ] pending (unpaid)
  - [ ] pending (dp_paid)
  - [ ] pending (fully paid)
  - [ ] confirmed
  - [ ] in_production
  - [ ] completed
  - [ ] cancelled

#### Task 3: OrderItemCard Integration ✅ Ready
- [ ] Locate order items section in both:
  - [ ] `customer/orders/index.blade.php`
  - [ ] `admin/orders/index.blade.php`
- [ ] Replace item card markup with component
- [ ] Test custom order details display
- [ ] Verify pricing display (unit price + subtotal)
- [ ] Test with and without custom specs

#### Task 4: PaymentBadge Integration ✅ Ready
- [ ] Open `admin/payments/index.blade.php`
- [ ] Replace payment status displays with component
- [ ] Test all payment status variants
- [ ] Verify amount and channel display

#### Task 5: FormInput Integration ✅ Ready
- [ ] Select one form file to start (e.g., `products/form.blade.php`)
- [ ] Replace all input blocks with FormInput
- [ ] Test all input types:
  - [ ] text
  - [ ] email
  - [ ] number
  - [ ] select
  - [ ] textarea
  - [ ] checkbox
  - [ ] radio
- [ ] Verify validation error display
- [ ] Test old input restoration

#### Task 6: ConfirmDialog Integration ✅ Ready
- [ ] Find all delete buttons in admin
- [ ] Wrap with form and modal
- [ ] Test modal appearance
- [ ] Test form submission on confirm
- [ ] Test cancel button (dismisses modal)

---

## File-by-File Integration Order

### Day 1 (1-2 hours)
1. **customer/products/index.blade.php** - ProductCard (highest impact)
2. **customer/orders/index.blade.php** - OrderStatusBadge + OrderItemCard

### Day 2 (1 hour)
3. **admin/orders/index.blade.php** - OrderStatusBadge + OrderItemCard
4. **admin/payments/index.blade.php** - PaymentBadge

### Day 3 (30 minutes each)
5. **Form files** - Start with one critical form
6. **Delete dialogs** - Add ConfirmDialog to admin pages

---

## Testing Checklist

### Functionality Testing
- [ ] All components render without errors
- [ ] No console errors or warnings
- [ ] All original functionality preserved
- [ ] Links and buttons work correctly
- [ ] Form submission works
- [ ] Validation displays correctly

### Visual Testing
- [ ] Components match original styling
- [ ] Colors and badges display correctly
- [ ] Images load and fallback works
- [ ] Icons display properly
- [ ] Spacing and alignment correct

### Responsive Testing
- [ ] Desktop view (1920px+)
- [ ] Tablet view (768px-1024px)
- [ ] Mobile view (320px-480px)
- [ ] All components adapt correctly

### Accessibility Testing
- [ ] Keyboard navigation works
- [ ] Tab order is logical
- [ ] Screen reader friendly
- [ ] Color contrast adequate
- [ ] ARIA labels present

### Performance Testing
- [ ] No performance regression
- [ ] Page load time similar or better
- [ ] No memory leaks
- [ ] Component rendering efficient

---

## Rollback Plan

If issues arise during integration:

1. **Minor issue (styling):** Fix component CSS, redeploy
2. **Functionality issue:** Check component props, verify template context
3. **Critical issue:** Revert template changes from git, diagnose component
4. **Last resort:** Revert to Phase 2B state, investigate offline

---

## Success Metrics

✅ **Code Reduction:** 40-50% of view files  
✅ **Functionality:** 100% preserved  
✅ **Performance:** No regression  
✅ **Accessibility:** Improved  
✅ **Maintainability:** High (reusable components)

---

## Status After Phase 3B Completion

- ✅ All templates refactored with components
- ✅ 40-50% code reduction achieved
- ✅ All tests passing
- ✅ Ready for Phase 3C (verification)
- ✅ Overall Phase 3 progress: 85%

---

**Next Step:** Begin integrating ProductCard into `customer/products/index.blade.php`

