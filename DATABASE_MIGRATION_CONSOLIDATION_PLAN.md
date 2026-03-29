# Database Migration Consolidation Plan

## Status: In Progress
Date: 2026-03-29

## STEP 1: Create Consolidated Master Migrations

We will create NEW consolidated migrations that combine all fields, then delete old fragmented migrations.

### Migration Strategy:
1. Create: `YYYY_MM_DD_000000_create_base_tables.php` (roles, categories, bank_accounts)
2. Create: `YYYY_MM_DD_000001_create_users_table_consolidated.php` (includes google_id, avatar)
3. Create: `YYYY_MM_DD_000002_create_products_table_consolidated.php`
4. Create: `YYYY_MM_DD_000003_create_orders_table_consolidated.php` (all shipping fields)
5. Create: `YYYY_MM_DD_000004_create_payments_table_consolidated.php` (all proof fields)
6. Create: `YYYY_MM_DD_000005_create_order_relationships.php` (order_details, etc)
7. Create: `YYYY_MM_DD_000006_create_production_tables.php`
8. Create: `YYYY_MM_DD_000007_create_logs_and_utilities.php`

### Files To Delete (After Consolidation):
```
DATABASE/MIGRATIONS/
❌ 2024_12_20_000000_add_phone_to_orders_table.php
❌ 2026_03_29_100000_extend_orders_payments_users_shipping_oauth.php
❌ 2026_03_29_141500_add_payment_proofs_to_payments_table.php
❌ 2026_03_30_120000_create_order_shipping_logs_table.php (if separate, consolidate)
```

### Keep (Already consolidated):
```
✅ 0001_01_01_000000_create_cache_table.php
✅ 0001_01_01_000001_create_jobs_table.php
✅ 2004_01_01_000000_create_roles_table.php
✅ 2004_01_01_000001_create_users_table.php (but we'll update it)
✅ 2024_01_01_000002_create_categories_table.php
✅ 2024_01_01_000004_create_products_table.php
✅ 2024_01_01_000006_create_orders_table.php (but we'll update it)
✅ 2024_01_01_000010_create_order_details_table.php
✅ 2024_01_01_000011_create_production_processes_table.php
✅ 2024_01_01_000014_create_production_logs_table.php
✅ 2024_01_01_000015_create_payments_table.php (but we'll update it)
✅ 2026_02_18_022824_create_reports_table.php
✅ 2026_02_18_031002_create_settings_table.php
✅ 2026_02_24_000001_create_production_todos_table.php
✅ 2026_02_24_000002_create_production_schedules_table.php
✅ 2026_03_29_082048_create_bank_accounts_table.php
```

## STEP 2: Update Existing Core Migrations

For development purposes, we'll update the main migration files to include all necessary fields:

### 2004_01_01_000001_create_users_table.php (UPDATE)
ADD:
- google_id (for OAuth)
- avatar (for profile photo)

### 2024_01_01_000006_create_orders_table.php (UPDATE)
ADD:
- phone (shipping contact)
- shipping_status (processing, shipped, delivered)
- courier (shipping provider)
- tracking_number (tracking ID)
- shipped_at (timestamp)
- delivered_at (timestamp)

### 2024_01_01_000015_create_payments_table.php (UPDATE)
ADD:
- amount_paid (track partial payments)
- expected_dp_amount (DP calculation)
- payment_channel (manual_dp, manual_full, midtrans)
- payment_proof_dp (DP proof)
- payment_proof_full (final proof)

MODIFY:
- payment_status: Change from ENUM('unpaid','paid','failed') to VARCHAR(30) with values:
  * pending
  * dp_paid
  * full_pending
  * paid
  * failed

## STEP 3: Database Seeding

After migration consolidation, seed with:
- Default roles (customer, admin, production_staff)
- Default settings
- Test data (categories, products, users)

## STEP 4: Cleanup

After confirming new migrations work:
1. Delete old fragmentary migrations
2. Run fresh: `php artisan migrate:fresh --seed`
3. Verify all data integrity
4. Commit to git

## Implementation Timeline

### Phase 1: Create Updates (This step)
- Update existing migrations to include all fields
- Ensure no conflicts with current migration state

### Phase 2: Testing
- Run migrations on fresh database
- Verify all tables and fields exist
- Check foreign keys and constraints

### Phase 3: Production Preparation
- Generate migration files with proper timestamps
- Document rollback procedures
- Create backup strategy

## Notes

- This consolidation makes the project more maintainable
- Fresh migration runs will be cleaner
- New developers can understand schema immediately
- Reduces database schema fragmentation

---

Generated: 2026-03-29
