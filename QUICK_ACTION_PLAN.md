# ⚡ QUICK ACTION PLAN - NEXT 7 DAYS

**Furniture Manufacturing System - Production Readiness**  
**Target:** Production launch ready in 2 weeks  
**Generated:** March 29, 2026

---

## 📅 DAY 1-2: PHASE 2 - QUERY OPTIMIZATION

### Task: Add Eager Loading to All Controllers

**Estimated Time:** 4-6 hours  
**Expected Impact:** 50-70% performance improvement

#### Controllers to Update (in order):

**1. Admin/OrderController.php**
```php
// In index() method
$orders = Order::with(['user', 'orderDetails.product', 'payment'])
    ->latest()
    ->paginate(20);

// In show() method
$order->load(['user', 'orderDetails.product', 'payment', 'productionProcesses']);
```

**2. Admin/ProductController.php**
```php
// In index() method
$products = Product::with(['category', 'orderDetails'])
    ->search($request->search)
    ->filter($request->filter)
    ->paginate(20);
```

**3. Admin/DashboardController.php**
```php
// Optimize all dashboard queries
// Use aggregates instead of loading all data
$orderStats = Order::with(['payment'])
    ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
    ->groupBy('status')
    ->get();
```

**4. Customer/OrderTrackingController.php**
```php
// In index() method
$orders = Order::with(['orderDetails.product', 'payment', 'productionProcesses', 'shippingLogs'])
    ->where('user_id', auth()->id())
    ->latest()
    ->paginate(20);

// In show() method
$order->load(['user', 'orderDetails.product', 'payment', 'productionProcesses', 'shippingLogs']);
```

**5. Customer/InvoiceController.php**
```php
// In show() method
$order->load(['user', 'orderDetails.product', 'payment']);
```

**6. Production/ProductionController.php**
```php
// In index() method
$productions = ProductionProcess::with(['order.user', 'order.orderDetails.product'])
    ->latest()
    ->paginate(20);
```

**7. Production/ProductionProcessController.php**
```php
// In all methods that fetch data
$process->load(['order.user', 'order.orderDetails', 'logs']);
```

**8. Admin/ReportController.php**
```php
// For each report, optimize the query
// Example for sales report:
$orders = Order::with(['user', 'payment', 'orderDetails.product'])
    ->whereBetween('order_date', [$from, $to])
    ->get();
```

**✅ Checklist:**
- [ ] Update all 8 controllers with eager loading
- [ ] Test each controller (load page, check Network tab)
- [ ] Verify data displays correctly
- [ ] Confirm fewer database queries

---

## 📅 DAY 3-4: PHASE 3 - BLADE COMPONENTS

### Task: Create Reusable Components

**Estimated Time:** 6-8 hours

#### Step 1: Create Components Directory
```bash
mkdir -p resources/views/components
```

#### Step 2: Create Button Component
**File:** `resources/views/components/button.blade.php`
```blade
<button type="{{ $type ?? 'button' }}"
        class="px-4 py-2 rounded font-medium
               @if($variant === 'danger')
                   bg-red-600 hover:bg-red-700
               @elseif($variant === 'secondary')
                   bg-gray-500 hover:bg-gray-600
               @else
                   bg-blue-600 hover:bg-blue-700
               @endif
               text-white transition
               {{ $class ?? '' }}"
        @if($disabled ?? false) disabled @endif>
    {{ $slot }}
</button>
```

**Usage:** `<x-button variant="danger">Delete</x-button>`

#### Step 3: Create Alert Component
**File:** `resources/views/components/alert.blade.php`
```blade
@if($type === 'success')
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
@elseif($type === 'error')
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
@elseif($type === 'warning')
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
@else
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
@endif
    {{ $slot }}
</div>
```

**Usage:** `<x-alert type="success">Operation successful!</x-alert>`

#### Step 4: Create Badge Component
**File:** `resources/views/components/badge.blade.php`
```blade
<span class="inline-block px-3 py-1 rounded-full text-sm font-medium
          @if($status === 'pending')
              bg-yellow-200 text-yellow-800
          @elseif($status === 'confirmed')
              bg-blue-200 text-blue-800
          @elseif($status === 'completed')
              bg-green-200 text-green-800
          @elseif($status === 'cancelled')
              bg-red-200 text-red-800
          @else
              bg-gray-200 text-gray-800
          @endif">
    {{ $label ?? ucfirst($status) }}
</span>
```

**Usage:** `<x-badge status="completed" />`

#### Step 5: Create Card Component
**File:** `resources/views/components/card.blade.php`
```blade
<div class="bg-white rounded-lg shadow p-6 {{ $class ?? '' }}">
    @if($title ?? false)
        <h3 class="text-lg font-bold mb-4">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
```

**Usage:**
```blade
<x-card title="Order Details">
    Order information here...
</x-card>
```

#### Step 6: Create Form Input Component
**File:** `resources/views/components/form-input.blade.php`
```blade
<div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2">
        {{ $label }}
        @if($required ?? false)<span class="text-red-500">*</span>@endif
    </label>
    <input type="{{ $type ?? 'text' }}"
           name="{{ $name }}"
           value="{{ old($name, $value ?? '') }}"
           class="w-full px-3 py-2 border rounded
                  @error($name) border-red-500 @else border-gray-300 @enderror"
           {{ $attributes }}>
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

**Usage:** `<x-form-input name="email" label="Email" type="email" required />`

#### Step 7-10: Create Other Components
- [ ] modal.blade.php
- [ ] table.blade.php
- [ ] pagination.blade.php
- [ ] form-select.blade.php

**Refactor Views to Use Components:**
- [ ] Search and replace old HTML patterns with new components
- [ ] Update admin views
- [ ] Update customer views
- [ ] Update production views

**✅ Checklist:**
- [ ] All components created
- [ ] Components documented with examples
- [ ] Views refactored to use components
- [ ] Test components display correctly
- [ ] Verify styling consistency

---

## 📅 DAY 5: PHASE 4 - SECURITY HARDENING

### Task: Validate & Secure All Input

**Estimated Time:** 4-6 hours

**Security Checklist:**

#### 1. Form Request Validation
**Action:** Review each Form Request file and ensure complete validation

```php
// Example: app/Http/Requests/StoreProductRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
    ];
}
```

**Files to Review:**
- [ ] `app/Http/Requests/StoreProductRequest.php`
- [ ] `app/Http/Requests/UpdateProductRequest.php`
- [ ] `app/Http/Requests/StoreOrderRequest.php`
- [ ] `app/Http/Requests/ProcessOrderPaymentRequest.php`
- [ ] `app/Http/Requests/StoreOrderShippingLogRequest.php`
- [ ] All other Form Requests

#### 2. File Upload Validation
**Action:** Ensure all file uploads have type and size restrictions

```php
// ✅ Correct
'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120'

// ❌ Wrong
'photo' => 'required|file|max:10000'  // Too permissive
```

#### 3. Mass Assignment Protection
**Action:** Verify all models have $fillable or $guarded

```php
// app/Models/Product.php
protected $fillable = [
    'category_id',
    'name',
    'slug',
    'price',
    'images',
    'description',
];
// Never allow all: protected $guarded = [];
```

**Models to Check:**
- [ ] User.php
- [ ] Product.php
- [ ] Order.php
- [ ] Payment.php
- [ ] ProductionProcess.php
- [ ] All others

#### 4. Authorization
**Action:** Ensure sensitive operations check authorization

```php
// ✅ Correct
public function show(Order $order)
{
    $this->authorize('view', $order);  // Check if user can view
    return view('customer.orders.show', compact('order'));
}

// ❌ Wrong - no authorization check
public function show(Order $order)
{
    return view('customer.orders.show', compact('order'));
}
```

**Files to Check:**
- [ ] All customer controllers
- [ ] All admin controllers (should have admin role check)
- [ ] All production controllers

#### 5. CSRF Protection
**Action:** Verify all forms have CSRF token

```blade
<!-- ✅ Correct -->
<form method="POST" action="{{ route('product.store') }}">
    @csrf
    <!-- form fields -->
</form>

<!-- ❌ Wrong -->
<form method="POST" action="{{ route('product.store') }}">
    <!-- missing @csrf -->
</form>
```

**Check All Views:**
- [ ] `resources/views/admin/**/*.blade.php`
- [ ] `resources/views/customer/**/*.blade.php`
- [ ] `resources/views/production/**/*.blade.php`

**✅ Checklist:**
- [ ] All Form Requests complete
- [ ] All file uploads validated
- [ ] All models have fillable/guarded
- [ ] All sensitive operations authorized
- [ ] All forms have CSRF tokens

---

## 📅 DAY 6: PHASE 5 - PRODUCTION CONFIGURATION

### Task: Configure for Production Environment

**Estimated Time:** 2-3 hours

#### 1. Update .env File

```env
# BEFORE (Development)
APP_URL=http://localhost:8000
APP_ENV=local
APP_DEBUG=true
SESSION_DOMAIN=localhost
FILESYSTEM_DISK=local

# AFTER (Production)
APP_URL=https://bisafurniture.com
APP_ENV=production
APP_DEBUG=false
SESSION_DOMAIN=.bisafurniture.com
FILESYSTEM_DISK=public
```

#### 2. Create Storage Link
```bash
cd /path/to/furniture-manufacturing-system
php artisan storage:link
```

This creates symlink: `public/storage -> storage/app/public`

#### 3. Clear & Cache Configuration
```bash
php artisan config:clear
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

#### 4. Verify URLs in Code

**Search for hardcoded URLs:**
```bash
grep -r "localhost:8000" app/ resources/
grep -r "127.0.0.1" app/ resources/
grep -r "ngrok" app/ resources/ config/
```

**Replace with:**
- `asset('path')` for static assets
- `url('path')` for app URLs
- `route('name')` for internal routes
- `env('APP_URL')` for full URLs

#### 5. Database Configuration
```bash
# Run migrations
php artisan migrate:fresh --seed

# Verify data
php artisan tinker
>>> Role::count()  # Should show roles
>>> User::count()  # Should show seeded users
>>> Setting::all() # Should show settings
```

**✅ Checklist:**
- [ ] .env updated for production
- [ ] Storage link created
- [ ] Caches cleared and rebuilt
- [ ] No localhost URLs in code
- [ ] Database migrated and seeded
- [ ] All functionality working

---

## 📅 DAY 7: FINAL TESTING & VERIFICATION

### Task: Comprehensive Testing Before Launch

**Estimated Time:** 4-6 hours

#### 1. Functional Testing

**Customer Journey:**
- [ ] Register new account
- [ ] Browse products
- [ ] Add to cart
- [ ] Checkout
- [ ] Pay via Midtrans
- [ ] View order status
- [ ] Download invoice

**Admin Tasks:**
- [ ] Login to admin
- [ ] Verify pending payments
- [ ] Approve/reject payment
- [ ] View reports
- [ ] Create custom order

**Production Staff:**
- [ ] View assigned orders
- [ ] Update production stages
- [ ] Log production notes
- [ ] Mark complete

#### 2. Performance Testing

Test page load times:
```
Home page: < 3 seconds
Admin dashboard: < 5 seconds
Order listing: < 3 seconds
Reports: < 10 seconds
```

#### 3. Security Testing

- [ ] Try to access admin without login (should redirect)
- [ ] Try to view other user's order (should be denied)
- [ ] Try to upload malicious file (should reject)
- [ ] Try SQL injection (should fail safely)
- [ ] Try CSRF attack (should be blocked)

#### 4. Cross-Browser Testing

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browsers

#### 5. Error Handling

Test error scenarios:
- [ ] 404 page displays correctly
- [ ] 500 error handled gracefully
- [ ] Validation errors show properly
- [ ] Session timeout handled

**✅ Checklist:**
- [ ] All functional tests pass
- [ ] Performance acceptable
- [ ] Security tests pass
- [ ] All browsers compatible
- [ ] Error handling working
- [ ] Ready for production!

---

## 🚀 DEPLOYMENT STEPS

Once all 7 days complete:

### Pre-Deployment (1 day before)
```bash
# Backup database
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# Test migrations on staging
php artisan migrate:fresh --seed

# Run all tests
php artisan test

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Deployment
```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan view:cache
php artisan route:cache

# Restart queue workers (if applicable)
# php artisan queue:restart

# Clear application cache
php artisan cache:clear
```

### Post-Deployment
- [ ] Test all features work
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Notify users
- [ ] Document any issues

---

## 📊 Success Criteria

✅ **By End of Week:**
- All eager loading implemented
- Components created and refactored
- Security hardened
- Production configured
- All tests passing
- Ready for launch

✅ **Performance Metrics:**
- Page load time: < 3 seconds
- Database queries: 50-70% reduction
- Error rate: < 0.1%
- Security: 100% protection

---

## 🆘 If You Get Stuck

**Common Issues & Solutions:**

1. **Migrations failed**
   ```bash
   php artisan migrate:rollback
   php artisan migrate:fresh --seed
   ```

2. **Cache issues**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

3. **Permission issues**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap
   ```

4. **Composer issues**
   ```bash
   composer install
   composer dump-autoload
   ```

---

**Timeline:** 7 days  
**Effort:** ~30-40 hours  
**Status:** Ready to start  
**Target:** Production ready by next week

**Questions?** Review REFACTORING_COMPLETE_GUIDE.md for detailed instructions.

---
