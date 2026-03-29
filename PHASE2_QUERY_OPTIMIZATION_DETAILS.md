# 📊 PHASE 2: QUERY OPTIMIZATION DETAILS

**Status:** In Progress  
**Objective:** Add eager loading to eliminate N+1 queries  
**Expected Impact:** 50-70% query reduction  
**Timeline:** 4-6 hours

---

## 🔍 ANALYSIS FINDINGS

### Controllers Analyzed (11 Total)

#### ✅ ALREADY OPTIMIZED (3)
1. **Admin\OrderController**
   - Line 23: `Order::with(['user', 'orderDetails.product', 'payment'])`
   - Line 118: `$order->load(['user', 'orderDetails.product', 'payment'])`
   - Status: ✅ GOOD - Eager loading properly implemented

2. **Admin\PaymentController** 
   - Line 43: `Payment::with(['order.user', 'order.orderDetails.product'])`
   - Status: ✅ GOOD - Nested eager loading correct

3. **Production\ProductionController**
   - Line 43: `ProductionProcess::with([...])`
   - Line 71: `Order::with([...])`
   - Status: ✅ GOOD - Multi-relationship eager loading

#### ⚠️ NEEDS OPTIMIZATION (8)

---

## 🎯 OPTIMIZATION TASKS

### Task 1: Admin\DashboardController
**File:** `app/Http/Controllers/Admin/DashboardController.php`

**Issues Found:**
```php
// Line 46: Multiple Count Queries - Can be optimized with single query
'total_orders'    => Order::count(),                        // Query 1
'pending_orders'  => Order::where('status', 'pending')->count(),  // Query 2
'process_orders'  => Order::whereIn('status', [...])->count(),    // Query 3
'completed_orders' => Order::where('status', 'completed')->count(), // Query 4
'total_revenue'   => Order::where('status', 'completed')->sum('total'), // Query 5

// Line 82: Missing Eager Loading for Relations
$recentOrders = Order::with(['user', 'orderDetails.product'])
    ->latest()
    ->limit(5)
    ->get();
// This is ALREADY good, but needs testing
```

**Optimization Strategy:**
1. Combine multiple count() queries into single query using selectRaw()
2. Cache results (already done with Cache::remember)
3. Verify orderDetails.product eager loading works

**Code Changes Required:**
```php
// BEFORE (5 separate queries)
'total_orders'    => Order::count(),
'pending_orders'  => Order::where('status', 'pending')->count(),

// AFTER (1 query with selectRaw)
$stats = Order::select(
    DB::raw('COUNT(*) as total_orders'),
    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders'),
    DB::raw('SUM(CASE WHEN status IN ("confirmed", "in_production") THEN 1 ELSE 0 END) as process_orders'),
    DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders'),
    DB::raw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_revenue')
)->first();
```

**Priority:** HIGH - Reduces 5 queries to 1  
**Effort:** 1-2 hours

---

### Task 2: Admin\ReportController  
**File:** `app/Http/Controllers/Admin/ReportController.php`

**Issues Found:**
```php
// Lines 30-32: Multiple clone queries
$jumlahPesanan = (clone $ordersQuery)->count();           // Query 1
$totalTransaksi = (clone $ordersQuery)->sum('total');     // Query 2

// Lines 34-36: Multiple whereHas queries  
$pembayaranSukses = (clone $ordersQuery)->whereHas('payment', ...)->count(); // Query 3
$pembayaranGagal = (clone $ordersQuery)->whereHas('payment', ...)->count();  // Query 4

// Line 49: Missing eager loading for user and payment
$orders = Order::with(['user', 'payment'])
    ->whereMonth(...)
    ->paginate(10);
// This is good but check nested relationships
```

**Optimization Strategy:**
1. Combine into single query with DB::raw()
2. Eager load payment when needed in view
3. Use selectRaw() for calculated fields

**Code Changes Required:**
```php
// Combine 4 queries into 1
$stats = (clone $ordersQuery)
    ->select(
        DB::raw('COUNT(*) as total'),
        DB::raw('SUM(total) as transaction_total'),
        DB::raw('SUM(CASE WHEN EXISTS (
            SELECT 1 FROM payments 
            WHERE payments.order_id = orders.id 
            AND payment_status = "paid"
        ) THEN 1 ELSE 0 END) as payment_success'),
        DB::raw('SUM(CASE WHEN EXISTS (
            SELECT 1 FROM payments 
            WHERE payments.order_id = orders.id 
            AND payment_status = "failed"
        ) THEN 1 ELSE 0 END) as payment_failed')
    )
    ->first();

// Then extract values
$jumlahPesanan = $stats->total;
$totalTransaksi = $stats->transaction_total;
$pembayaranSukses = $stats->payment_success ?? 0;
$pembayaranGagal = $stats->payment_failed ?? 0;
```

**Alternative (Simpler):**
```php
// Group counts by single query
$orderStats = Order::whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->get()
    ->groupBy(fn($o) => $o->status)
    ->map(fn($group) => $group->count());

$jumlahPesanan = $orderStats->sum();
$pembayaranSukses = Order::whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->with('payment')
    ->get()
    ->filter(fn($o) => $o->payment?->payment_status === 'paid')
    ->count();
```

**Priority:** HIGH  
**Effort:** 2-3 hours

---

### Task 3: Production\ShippingMonitoringController
**File:** `app/Http/Controllers/Production/ShippingMonitoringController.php`

**Issues Found:**
```php
// Line 18: Missing eager loading
$orders = Order::query()
    ->where(...)
    ->paginate(15);
// Missing: ->with(['user', 'payment', 'orderDetails'])

// Line 95: No eager loading on lockForUpdate
$o = Order::query()->lockForUpdate()->findOrFail($order->id);
// Should be: Order::with([...])->lockForUpdate()->findOrFail($order->id)
```

**Optimization Strategy:**
1. Add eager loading to order queries
2. Load payment and orderDetails in optimized way
3. Maintain row locking for concurrent updates

**Code Changes Required:**
```php
// BEFORE
$orders = Order::query()
    ->where('shipping_status', '!=', 'delivered')
    ->paginate(15);

// AFTER  
$orders = Order::with(['user', 'payment', 'orderDetails.product', 'shippingLogs'])
    ->where('shipping_status', '!=', 'delivered')
    ->latest()
    ->paginate(15);

// For locked query
$o = Order::with(['user', 'orderDetails.product'])
    ->lockForUpdate()
    ->findOrFail($order->id);
```

**Priority:** HIGH  
**Effort:** 1-2 hours

---

### Task 4: Production\ProductionProcessController
**File:** `app/Http/Controllers/Production/ProductionProcessController.php`

**Issues Found:**
```php
// Line 17: Missing eager loading
$orders = Order::whereIn('status', ['confirmed', 'in_production'])
    ->get();
// Missing: ->with(['payment', 'user', 'orderDetails.product'])

// Line 41: No eager loading
$processes = ProductionProcess::where('order_id', $order->id)
    ->get();
// Missing: ->with(['order.user', 'productionLogs'])

// Line 54: Has eager loading but incomplete
$order = Order::with(['user:id,name', 'orderDetails.product:id,name,sku'])
    ->findOrFail($order_id);
// Should add: payment, productionProcesses
```

**Optimization Strategy:**
1. Add comprehensive eager loading to all queries
2. Include nested relationships
3. Use column selection for memory efficiency

**Code Changes Required:**
```php
// Task 4.1 - Line 17
$orders = Order::with(['user:id,name,email', 'payment:id,order_id,payment_status', 'orderDetails.product:id,name,sku'])
    ->whereIn('status', ['confirmed', 'in_production'])
    ->latest()
    ->get();

// Task 4.2 - Line 41
$processes = ProductionProcess::with(['order.user:id,name', 'productionLogs'])
    ->where('order_id', $order->id)
    ->orderBy('created_at')
    ->get();

// Task 4.3 - Line 54  
$order = Order::with([
    'user:id,name,email',
    'orderDetails.product:id,name,sku',
    'payment:id,order_id,payment_status',
    'productionProcesses:id,order_id,stage,status'
])->findOrFail($order_id);
```

**Priority:** HIGH  
**Effort:** 1-2 hours

---

### Task 5: Customer\ProductController
**File:** `app/Http/Controllers/Customer/ProductController.php`

**Issues Found:**
```php
// Likely missing eager loading for category
$product = Product::findOrFail($id);
// Missing: ->with('category')

$products = Product::paginate(12);
// Missing: ->with('category')
```

**Optimization Strategy:**
1. Always eager load category relationship
2. Add filtering by category if applicable
3. Load images/variations if they exist

**Code Changes Required:**
```php
// Product listing
$products = Product::with('category')
    ->where('is_active', true)
    ->latest()
    ->paginate(12);

// Product detail
$product = Product::with('category')
    ->findOrFail($id);
```

**Priority:** MEDIUM  
**Effort:** 30 minutes

---

### Task 6: Customer\OrderTrackingController
**File:** `app/Http/Controllers/Customer/OrderTrackingController.php`

**Issues Found:**
```php
// Missing eager loading for:
// - user (customer info)
// - payment (payment status)
// - shippingLogs (tracking history)
// - productionProcesses (status updates)
```

**Optimization Strategy:**
1. Load all related data in single query
2. Order shippingLogs by date for proper history
3. Include payment for complete status

**Code Changes Required:**
```python
$order = Order::with([
    'user:id,name,email,phone',
    'payment:id,order_id,payment_status,amount_paid',
    'shippingLogs:id,order_id,status,notes,created_at',
    'productionProcesses:id,order_id,stage,status,progress_percentage'
])->findOrFail($id);
```

**Priority:** MEDIUM  
**Effort:** 30 minutes - 1 hour

---

### Task 7: Admin\ProductController
**File:** `app/Http/Controllers/Admin/ProductController.php`

**Issues Found:**
```php
// Missing eager loading for category in listings and single product view
$products = Product::paginate(15);
// Missing: ->with('category')

$product = Product::findOrFail($id);
// Missing: ->with('category')

// Also check for orderDetails relationship
```

**Optimization Strategy:**
1. Always include category in queries
2. Include count of orders if applicable
3. Add filtering by category

**Code Changes Required:**
```php
// Products listing
$products = Product::with('category')
    ->latest()
    ->paginate(15);

// Product detail
$product = Product::with(['category', 'orderDetails'])
    ->findOrFail($id);

// Products by category
$products = Product::with('category')
    ->where('category_id', $categoryId)
    ->paginate(15);
```

**Priority:** MEDIUM  
**Effort:** 30 minutes - 1 hour

---

### Task 8: Customer\InvoiceController
**File:** `app/Http/Controllers/Customer/InvoiceController.php`

**Issues Found:**
```php
// Missing eager loading for complete order data
$order = Order::findOrFail($id);
// Should load: user, payment, orderDetails.product, shippingLogs
```

**Optimization Strategy:**
1. Load all invoice-related data upfront
2. Avoid N+1 when rendering invoice items
3. Include payment proof images

**Code Changes Required:**
```php
$order = Order::with([
    'user:id,name,email,phone,address',
    'payment:id,order_id,payment_status,amount_paid,payment_channel',
    'orderDetails.product:id,name,sku,base_price',
    'shippingLogs:id,order_id,status,notes'
])->findOrFail($id);
```

**Priority:** MEDIUM  
**Effort:** 30 minutes

---

## 📋 EXECUTION ORDER

### Day 1 (High Priority - 3-4 hours)
1. **Task 3:** ShippingMonitoringController (1-2 hours)
   - Easiest to implement
   - High impact on shipping workflow

2. **Task 4:** ProductionProcessController (1-2 hours)
   - Multiple issues found
   - Core to production flow

### Day 2 (High Priority - 2-3 hours)
3. **Task 1:** DashboardController (1-2 hours)
   - Consolidate count queries
   - Higher technical complexity

4. **Task 2:** ReportController (1-2 hours)
   - Consolidate reportQuery
   - Similar pattern to Dashboard

### Day 3 (Medium Priority - 2-3 hours)
5. **Task 5:** Customer\ProductController (30 min)
6. **Task 6:** Customer\OrderTrackingController (1 hour)
7. **Task 7:** Admin\ProductController (1 hour)
8. **Task 8:** Customer\InvoiceController (30 min)

---

## ✅ VERIFICATION CHECKLIST

### After Each Optimization:
- [ ] Code compiles without errors
- [ ] Controller method returns correct data
- [ ] View renders without errors
- [ ] Check Laravel Debugbar: fewer queries shown
- [ ] Page load time improved

### Final Verification:
- [ ] All 8 controllers optimized
- [ ] No N+1 patterns remaining
- [ ] All tests passing
- [ ] Benchmark: Query count reduced by 50-70%

---

## 🎯 SUCCESS METRICS

**Target:**
- Initial: ~20-30 queries per page
- After: ~5-10 queries per page
- Improvement: 50-70% reduction

**Measurable Results:**
- Dashboard: 8-10 queries → 2-3 queries
- Report: 10-12 queries → 3-4 queries
- Order listing: 15-20 queries → 5-7 queries
- Order detail: 8-10 queries → 3-4 queries

---

## 📝 NOTES

1. **Column Selection:** Use `->select('id', 'name', ...)` in eager loading for memory efficiency
2. **Nested Eager Loading:** Use dot notation `'orderDetails.product'`
3. **Conditional Loading:** Use `when()` if relationships are optional
4. **Testing:** Use `\Illuminate\Database\Query\Builder::enableQueryLog()` to verify

---

**Ready to Start Phase 2 Execution!**

Next: Execute tasks in the order specified above.
