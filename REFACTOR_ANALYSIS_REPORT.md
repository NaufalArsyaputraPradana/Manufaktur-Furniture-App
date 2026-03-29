# 📋 Refactor Analysis Report - Furniture Manufacturing System

**Status:** Analysis In Progress  
**Date:** March 29, 2026  
**Version:** 1.0

---

## 1. PROJECT STRUCTURE OVERVIEW

### Technology Stack
- **Framework:** Laravel 11.x
- **PHP:** 8.2+
- **Frontend:** Blade, Tailwind CSS (Bootstrap 5.3 untuk admin), JavaScript
- **Database:** MySQL 8.0+
- **Payment:** Midtrans
- **External APIs:** Google Translate, ExchangeRate API

### Key Directories
```
app/
├── Http/Controllers/
│   ├── Admin/          (10 controllers)
│   ├── Customer/       (8 controllers)
│   ├── Production/     (5 controllers)
│   └── Auth/           (2 controllers)
├── Models/             (15 models)
├── Services/           (6 services)
├── Repositories/       (if any)
├── Policies/           (2 policies)
├── Observers/          (3 observers)
└── Helpers/            (helpers.php)

resources/
├── views/
│   ├── layouts/        (4 main layouts)
│   ├── admin/
│   ├── customer/
│   ├── production/
│   ├── auth/
│   └── components/
├── css/
└── js/

database/
├── migrations/         (20 migration files)
├── seeders/
└── factories/

routes/
├── web.php             (270 lines)
└── api.php             (Simple, 2 routes)
```

---

## 2. DUPLICATE & UNUSED CODE ANALYSIS

### 2.1 Multiple ProfileControllers
⚠️ **FOUND DUPLICATES:**
- `App\Http\Controllers\Admin\ProfileController`
- `App\Http\Controllers\Customer\ProfileController`

**STATUS:** These are intentional (different namespaces, different functionality)  
**ACTION:** Keep but ensure consistency in methods and logic

### 2.2 Product Controllers Aliasing
⚠️ **NAMING ISSUE:**
- `Customer\ProductController` aliased as `CustomerProductController` in routes
- `Admin\ProductController` aliased as `AdminProductController` in routes

**STATUS:** This is clean aliasing for clarity  
**ACTION:** Keep as-is for route clarity

### 2.3 Payment Controllers
- `Customer\PaymentController` - handles both:
  - Payment notifications (webhook)
  - Payment verification (admin panel)
  - Payment generation (snap token)

**ISSUE:** Mixing customer and admin concerns  
**RECOMMENDATION:** Separate into:
  - `PaymentController` (customer - notification, snap token)
  - `Admin\PaymentController` (admin - verification, rejection, confirmation)

### 2.4 Migration Issues - CRITICAL
Found multiple "extend" migrations that modify core tables:
- `2026_03_29_100000_extend_orders_payments_users_shipping_oauth.php`
- `2026_03_29_141500_add_payment_proofs_to_payments_table.php`
- `2024_12_20_000000_add_phone_to_orders_table.php`

**ISSUE:** Fragmented migrations make deployment harder  
**ACTION:** Consolidate into main migrations (requires migration reset)

### 2.5 Order Related Files
- `OrderTrackingController` has order creation methods (custom orders)
- `Admin/CustomOrderController` handles custom order calculations
- `Admin/OrderController` handles standard orders

**ISSUE:** Order creation split between 2-3 controllers  
**RECOMMENDATION:** Consider consolidating order logic into unified service

### 2.6 Duplicate Routes
**POTENTIAL ISSUE:** 
- Order cancellation route in both:
  - `customer/orders/{order}/cancel` (OrderTrackingController)
  - `/admin/orders/{order}/cancel` (OrderController)

**STATUS:** These are separate endpoints, but same logic  
**ACTION:** Extract cancel logic to service layer

---

## 3. DATABASE SCHEMA ISSUES

### 3.1 Migration Strategy Problem
Current migrations are incremental adds to existing tables:
- Creates coupling between migrations
- Hard to reset/reseed in development
- Difficult to deploy in fresh environment

**CONSOLIDATED MIGRATION PLAN:**

```
CREATE TABLES:
✅ users                  (base with google_id, avatar)
✅ orders                 (all fields including shipping_status, courier)
✅ payments               (all fields including amount_paid, proofs)
✅ products               (all fields)
✅ categories             (all fields)
✅ order_details          (all fields)
✅ production_processes   (all fields)
✅ production_logs        (all fields)
✅ bank_accounts          (new)
✅ order_shipping_logs    (new)
✅ production_todos       (new)
✅ production_schedules   (new)
✅ roles                  (base)
✅ settings               (new)
✅ reports                (new)
```

**FILES TO DELETE (after consolidation):**
- `2024_12_20_000000_add_phone_to_orders_table.php`
- `2026_03_29_100000_extend_orders_payments_users_shipping_oauth.php`
- `2026_03_29_141500_add_payment_proofs_to_payments_table.php`

### 3.2 Foreign Key Issues to Check
Need to verify:
- Cascade delete settings
- Foreign key constraints
- Proper indexing on foreign keys

---

## 4. MODEL RELATIONSHIPS

### 4.1 Verified Relationships ✅
- `Order::belongsTo(User)`
- `Order::hasMany(OrderDetail)`
- `Order::hasOne(Payment)`
- `Order::hasMany(ProductionProcess)`
- `Order::hasMany(OrderShippingLog)`
- `Product::belongsTo(Category)`
- `Product::hasMany(OrderDetail)`
- `Payment::belongsTo(Order)`

### 4.2 Eager Loading Issues
**FOUND:** Multiple views and controllers not using eager loading:
- `OrderTrackingController::show()` loads without `with()`
- `Admin/PaymentController::pendingVerification()` uses `with(['order.user', 'order.orderDetails.product'])`

**ACTION:** Add consistent eager loading across all controllers

### 4.3 N+1 Query Potential
Files to check for N+1:
- Admin dashboard
- Reports
- Order listing pages
- Payment verification page

---

## 5. CODE QUALITY ISSUES

### 5.1 Logic in Controllers
**FILES WITH BUSINESS LOGIC:**
- `CartController::add()` - cart logic should be in Service
- `CheckoutController::process()` - order creation should be in Service
- `OrderTrackingController::storeCustomOrder()` - custom order logic in controller

**ACTION:** Create `OrderService`, `CartService`

### 5.2 Validation
**STATUS:** Using Form Requests ✅
- `StoreProductRequest`
- `UpdateProductRequest`
- `StoreOrderRequest`
- `UpdateOrderStatusRequest`
- `ProcessOrderPaymentRequest`
- etc.

**ISSUE:** Some controllers still validate in controller method  
**ACTION:** Migrate all validation to Form Requests

### 5.3 Comments & Debug Code
**TO CHECK:**
- Console.log statements in JavaScript
- dd() or dump() calls
- TODO comments
- Unused imports

---

## 6. FRONTEND STRUCTURE

### 6.1 Blade Components Status
**MISSING COMPONENTS:** Should extract common patterns:
- Button component (various styles)
- Form input component
- Card component
- Modal component
- Alert/notification component
- Badge/status component
- Table component

### 6.2 Layout Files
**CURRENT:**
- `layouts/app.blade.php`
- `layouts/admin.blade.php`
- `layouts/customer.blade.php`
- `layouts/production.blade.php`
- Partials for navbar, sidebar, footer, scripts, styles

**STATUS:** Good structure, but need to check for duplicate HTML/CSS

### 6.3 Tailwind vs Bootstrap Mixing
**ISSUE:** Project uses both Bootstrap 5 (admin) and Tailwind CSS  
**ACTION:** Decide on one CSS framework, migrate to consistency

---

## 7. SECURITY ANALYSIS

### 7.1 Authentication & Authorization
**VERIFIED:**
- Role-based middleware in routes ✅
- Policies for PostProductionTodo, ProductionSchedule ✅
- Middleware checks in production routes ✅

**TO CHECK:**
- CSRF protection on forms
- Mass assignment protection (fillable/guarded)
- File upload validation
- Input sanitization

### 7.2 Payment Security
**VERIFIED:**
- Midtrans integration ✅
- Webhook validation ✅

**TO CHECK:**
- Payment amount validation before processing
- Authorization check for payment access
- File upload protection for payment proofs

### 7.3 File Upload
**ISSUE:** Products have image array field  
**TO CHECK:**
- File type validation
- File size limits
- Storage path security
- Disk configuration (public vs storage)

---

## 8. CONFIGURATION FOR PRODUCTION

### 8.1 Environment Variables to Update
```
APP_URL=https://bisafurniture.com (currently localhost/ngrok)
APP_ENV=production
APP_DEBUG=false
SESSION_DOMAIN=.bisafurniture.com
SANCTUM_STATEFUL_DOMAINS=bisafurniture.com
FILESYSTEM_DISK=public
```

### 8.2 Routes & URLs
**HARDCODED URLS TO FIND:**
- 127.0.0.1:8000
- localhost
- ngrok URLs
- http:// links (should be https://)

---

## 9. PERFORMANCE OPTIMIZATION

### 9.1 Query Optimization Needed
1. Eager load relationships globally
2. Add database indexes on foreign keys
3. Cache frequently accessed data
4. Optimize dashboard queries

### 9.2 Code Optimization
1. Use collections efficiently
2. Lazy load when appropriate
3. Cache computed properties
4. Optimize loop operations

---

## 10. TESTING STATUS

### 10.1 Test Files Found
- `tests/Feature/Admin/UserCrudTest.php`
- `tests/Feature/Admin/ProductCrudValidationTest.php`
- `tests/Feature/Admin/ProductEditValidationTest.php`
- Various other feature tests

**STATUS:** Good test coverage exists ✅

### 10.2 Test Coverage Areas
- CRUD operations
- Validation
- Authorization
- Payment processing

---

## 11. IMMEDIATE ACTION ITEMS

### Priority 1 (CRITICAL)
- [ ] Consolidate migrations (reset & combine)
- [ ] Separate payment controllers (Admin & Customer)
- [ ] Add eager loading to all queries
- [ ] Implement asset path fix for production

### Priority 2 (HIGH)
- [ ] Refactor business logic to Service layer
- [ ] Create reusable Blade components
- [ ] Extract cart logic to service
- [ ] Implement comprehensive validation

### Priority 3 (MEDIUM)
- [ ] Remove debug code (dd, console.log, TODO)
- [ ] Standardize CSS framework (Tailwind vs Bootstrap)
- [ ] Add missing form components
- [ ] Optimize database queries

### Priority 4 (LOW)
- [ ] Documentation improvements
- [ ] Code style consistency
- [ ] Performance monitoring setup

---

## 12. FILES TO REVIEW IN DETAIL

**HIGH PRIORITY:**
- [ ] `app/Http/Controllers/Customer/PaymentController.php`
- [ ] `app/Http/Controllers/Customer/OrderTrackingController.php`
- [ ] `app/Http/Controllers/Admin/CustomOrderController.php`
- [ ] `app/Services/PaymentService.php`
- [ ] `routes/web.php`
- [ ] All migration files

**MEDIUM PRIORITY:**
- [ ] All controller store/update methods
- [ ] All views under `resources/views`
- [ ] JavaScript files in `resources/js`

---

## 13. NEXT STEPS

1. **Review and Approve** this analysis
2. **Begin Migration Consolidation** (database reset required)
3. **Refactor Controllers** to Service layer
4. **Create Blade Components** for frontend
5. **Implement Eager Loading**
6. **Security Audit** (validation, file upload, authorization)
7. **Production Configuration** (env, URLs, paths)
8. **Comprehensive Testing** (all CRUD, payment, auth)
9. **Performance Testing** (query optimization, load testing)
10. **Final Cleanup** (debug code removal, documentation)

---

**Generated:** 2026-03-29  
**By:** Code Analysis System
