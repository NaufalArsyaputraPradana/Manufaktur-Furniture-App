# Phase 3: Blade Components Creation - Detailed Plan

## Overview
Create reusable Blade components to eliminate template duplication and reduce code by 30-50%.

**Duration:** 6-8 hours  
**Status:** ⏳ IN PROGRESS  
**Target:** 6 components created, integrated across all templates

---

## Component Candidates Identified

### 1. **ProductCard Component**
**Current Location:** Scattered in `customer/products/index.blade.php` (lines 280-350)

**Responsibility:**
- Display product with image, name, description, price
- Show availability status badge
- Show category badge
- Handle image fallback with placeholder
- Add to cart button

**Props:**
```php
<x-product-card 
    :product="$product"
    :showCategory="true"
    :showDimensions="true" />
```

**Current Code Duplication:** ~80 lines in products index, repeated variations

**Expected Reduction:** 30-40 lines → 3 lines per usage (90% reduction per instance)

---

### 2. **OrderStatusBadge Component**
**Current Location:** Scattered in `customer/orders/index.blade.php` (lines 85-120)

**Responsibility:**
- Display order status with appropriate icon
- Color coding based on status + payment state
- Handle multiple status scenarios (pending, confirmed, in_production, completed, cancelled)
- Support payment status overlays (paid, dp_paid, unpaid)

**Props:**
```php
<x-order-status-badge 
    :status="$order->status"
    :payment="$order->payment"
    :isPaid="$isPaid"
    :isDpPaid="$isDpPaid" />
```

**Current Code Duplication:** ~35 lines of conditional logic per usage

**Expected Reduction:** 35 lines → 2 lines per usage (94% reduction)

---

### 3. **OrderItemCard Component**
**Current Location:** `customer/orders/index.blade.php` (lines 145-220)

**Responsibility:**
- Display order item with product image, name, quantity, price
- Handle custom specifications display
- Show customization details if applicable
- Format pricing information

**Props:**
```php
<x-order-item-card 
    :detail="$orderDetail"
    :product="$detail->product" />
```

**Current Code Duplication:** ~75 lines per order listing

**Expected Reduction:** 75 lines → 2-3 lines per usage

---

### 4. **PaymentBadge Component**
**Current Location:** Scattered across `customer/orders`, `admin/orders`, `admin/payments`

**Responsibility:**
- Display payment status with icon
- Show payment amount/channel
- Color code based on payment status
- Support inline and expanded modes

**Props:**
```php
<x-payment-badge 
    :payment="$payment"
    :showAmount="true"
    :showChannel="true" />
```

**Current Code Duplication:** ~20-30 lines across multiple views

**Expected Reduction:** 20-30 lines → 1-2 lines per usage

---

### 5. **FormInput Component**
**Current Location:** Multiple form files use repeated input structure

**Responsibility:**
- Render form input with label, validation, error message
- Support different input types (text, email, number, select, textarea)
- Display error messages from validation
- Add Bootstrap styling and accessibility attributes

**Props:**
```php
<x-form-input 
    name="email"
    label="Email Address"
    type="email"
    placeholder="your@email.com"
    :value="old('email')"
    :errors="$errors" />
```

**Current Code Duplication:** ~15-20 lines per form field

**Expected Reduction:** 15-20 lines → 1 line per field

---

### 6. **ConfirmDialog Component**
**Current Location:** Repeated modal patterns in multiple views

**Responsibility:**
- Reusable confirmation dialog/modal
- Support custom title, message, action button text
- Handle both form submission and link deletion
- Accessibility compliant

**Props:**
```php
<x-confirm-dialog 
    id="deleteConfirm"
    title="Hapus Pesanan?"
    message="Tindakan ini tidak dapat dibatalkan."
    buttonText="Ya, Hapus"
    buttonClass="btn-danger" />
```

**Current Code Duplication:** ~30-40 lines of modal code per delete/confirm action

**Expected Reduction:** 30-40 lines → 2-3 lines per usage

---

## Implementation Order

### Day 1 (3-4 hours):
1. ✅ Create directory structure: `resources/views/components/`
2. ✅ Create **ProductCard** component (highest usage, 10+ times)
3. ✅ Create **OrderStatusBadge** component (5-8 usages)
4. ✅ Integrate ProductCard in `customer/products/index.blade.php`

### Day 2 (2-3 hours):
5. ✅ Create **OrderItemCard** component
6. ✅ Create **PaymentBadge** component
7. ✅ Integrate in `customer/orders/index.blade.php` and payment views

### Day 3 (1-2 hours):
8. ✅ Create **FormInput** component
9. ✅ Create **ConfirmDialog** component
10. ✅ Integration sweep across all templates

---

## Code Reduction Expected

| Component | Usage Count | Before | After | Reduction |
|-----------|------------|--------|-------|-----------|
| ProductCard | 15+ | 80 lines | 3 lines | 96% |
| OrderStatusBadge | 8 | 35 lines | 2 lines | 94% |
| OrderItemCard | 10+ | 75 lines | 2 lines | 97% |
| PaymentBadge | 12 | 25 lines | 1 line | 96% |
| FormInput | 50+ | 15 lines | 1 line | 93% |
| ConfirmDialog | 20+ | 35 lines | 2 lines | 94% |
| **TOTAL** | **115+** | **~265 lines avg** | **~11 lines avg** | **~96% reduction** |

**Overall Template Reduction:** 30-50% code reduction in view layer

---

## Quality Standards

✅ **Accessibility:** ARIA labels, semantic HTML  
✅ **Responsiveness:** Bootstrap grid classes  
✅ **Styling:** Consistent with app design system  
✅ **Documentation:** @props and @php docblocks  
✅ **Type Hints:** Full type hints in component props  
✅ **Error Handling:** Graceful fallbacks

---

## Files to Create

```
resources/views/components/
├── product-card.blade.php           (80 lines)
├── order-status-badge.blade.php     (60 lines)
├── order-item-card.blade.php        (75 lines)
├── payment-badge.blade.php          (50 lines)
├── form-input.blade.php             (65 lines)
├── confirm-dialog.blade.php         (80 lines)
└── COMPONENTS_DOCUMENTATION.md      (Reference guide)
```

---

## Files to Modify

```
resources/views/customer/
├── products/index.blade.php         (Remove ~200 lines, add ~30 lines)
├── orders/index.blade.php           (Remove ~150 lines, add ~50 lines)
├── cart/index.blade.php             (Minor updates)

resources/views/admin/
├── orders/index.blade.php           (Remove ~100 lines)
├── payments/index.blade.php         (Remove ~80 lines)

resources/views/production/
├── index.blade.php                  (Minor updates)
```

---

## Testing Checklist

- [ ] ProductCard renders correctly with image
- [ ] ProductCard shows availability status
- [ ] ProductCard handles missing images gracefully
- [ ] OrderStatusBadge shows all status variants
- [ ] OrderStatusBadge colors match status correctly
- [ ] OrderItemCard displays quantities and prices
- [ ] PaymentBadge shows payment status icons
- [ ] FormInput renders all input types
- [ ] FormInput displays validation errors
- [ ] ConfirmDialog appears on trigger
- [ ] All components responsive on mobile
- [ ] All components accessible with keyboard
- [ ] No console errors or warnings

---

## Success Metrics

✅ **Code Quality:** A+ (reusable, maintainable)  
✅ **Code Reduction:** 30-50% in template files  
✅ **Performance:** No change (still static generation)  
✅ **Functionality:** 100% preserved  
✅ **Accessibility:** WCAG 2.1 AA compliant  
✅ **Documentation:** Complete with examples

---

## Current Status

**Phase:** ⏳ STARTING  
**Completed:** 0%  
**Next Step:** Analyze template patterns → Create ProductCard component

