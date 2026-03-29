# Phase 4.1 Testing Guide - Manual & Automated

**Status:** Ready for Testing  
**Date:** 2026-02-26  
**Scope:** All 11 web routes and CRUD operations

---

## 🧪 Quick Start: Manual Testing (30 minutes)

### 1. Dashboard Route
```
URL: http://localhost:8000/admin/reports/dashboard
Expected: Dashboard loads with charts and statistics
Steps:
  1. Navigate to dashboard
  2. Verify date range filters appear
  3. Check that all 4 stat cards display
  4. Confirm charts render (sales, production, inventory)
  5. Verify financial summary table shows
  6. Test date range filter changes
```

### 2. Create Report Form
```
URL: http://localhost:8000/admin/reports/create
Expected: Empty form with all fields
Steps:
  1. Navigate to create form
  2. Leave all fields blank and submit
  3. Verify validation errors appear
  4. Fill in all required fields:
     - Type: "Sales"
     - Title: "Test Report January"
     - Start: 2026-01-01
     - End: 2026-01-31
  5. Click "Buat Laporan"
  6. Should redirect to reports list
  7. Verify new report appears in table
```

### 3. Reports List
```
URL: http://localhost:8000/admin/reports
Expected: Table of created reports
Steps:
  1. Navigate to list
  2. Verify table shows report from step 2
  3. Test type filter dropdown
  4. Test status filter dropdown
  5. Test reset button
  6. Check pagination (if multiple reports)
  7. Verify action buttons (view, edit, delete)
```

### 4. View Report Detail
```
URL: http://localhost:8000/admin/reports/{id}
Expected: Report details with actions
Steps:
  1. Click view icon on any report
  2. Verify report title displays
  3. Check status badge shows
  4. Verify metadata (creator, date created)
  5. Confirm action buttons visible
```

### 5. Edit Report
```
URL: http://localhost:8000/admin/reports/{id}/edit
Expected: Form pre-filled with data
Steps:
  1. Click edit icon on report
  2. Verify all fields are pre-filled
  3. Change title to "Updated Test Report"
  4. Change status filter
  5. Click "Simpan Perubahan"
  6. Redirect to report detail
  7. Verify title updated in detail view
```

### 6. Delete Report
```
URL: POST to /admin/reports/{id}
Expected: Report removed from list
Steps:
  1. On reports list, click delete icon
  2. Confirm deletion in popup
  3. Verify success message
  4. Check report no longer in table
```

### 7. Export Report (CSV)
```
URL: http://localhost:8000/admin/reports/{id}/export?format=csv
Expected: CSV file download
Steps:
  1. On report detail, click export button
  2. Select CSV format
  3. Verify file downloads
  4. Open in Excel/Sheets
  5. Verify structure: Title, Period, Type, etc.
```

---

## 🤖 Automated Testing (Setup)

### Unit Tests to Create

```php
// tests/Unit/Models/ReportTest.php
class ReportTest extends TestCase {
    public function test_report_has_correct_attributes() { }
    public function test_report_belongs_to_user() { }
    public function test_report_can_be_scoped_by_type() { }
    public function test_report_can_be_scoped_by_status() { }
    public function test_report_casts_data_as_array() { }
}
```

### Feature Tests to Create

```php
// tests/Feature/ReportControllerTest.php
class ReportControllerTest extends TestCase {
    public function test_dashboard_loads() { }
    public function test_can_view_reports_list() { }
    public function test_can_create_report() { }
    public function test_can_view_report_detail() { }
    public function test_can_edit_report() { }
    public function test_can_delete_report() { }
    public function test_can_export_report_as_csv() { }
    public function test_create_requires_authentication() { }
    public function test_create_validates_required_fields() { }
    public function test_report_filters_work() { }
}
```

---

## ✅ Verification Checklist

### Routes & Controllers
- [ ] All 11 routes respond (no 404)
- [ ] Authentication required (401 if not logged in)
- [ ] Authorization working (only own reports accessible)
- [ ] Proper HTTP methods (GET, POST, PATCH, DELETE)

### Forms & Validation
- [ ] Create form shows all fields
- [ ] Edit form pre-fills data correctly
- [ ] Validation displays for empty fields
- [ ] Validation displays for invalid dates
- [ ] Cancel buttons work
- [ ] Submit buttons save data

### Data Integrity
- [ ] Report title saves correctly
- [ ] Date range saves correctly
- [ ] Filters save correctly
- [ ] User ID auto-assigned
- [ ] Status auto-set to 'completed'
- [ ] Created/updated timestamps correct

### UI & UX
- [ ] Forms are responsive
- [ ] Tables are readable
- [ ] Status badges color-coded
- [ ] Action buttons visible and clickable
- [ ] Empty states show helpful message
- [ ] Success/error messages appear
- [ ] Pagination works

---

## 🔍 Database Verification Queries

Run these to verify data integrity:

```sql
-- Check reports created
SELECT COUNT(*) FROM reports;

-- Verify latest report
SELECT * FROM reports ORDER BY created_at DESC LIMIT 1;

-- Check report status distribution
SELECT status, COUNT(*) FROM reports GROUP BY status;

-- Verify user associations
SELECT r.id, r.title, u.name 
FROM reports r 
LEFT JOIN users u ON r.generated_by = u.id;

-- Check filter data structure
SELECT id, title, filters FROM reports LIMIT 5;
```

---

## 🚀 Testing Command Line

### Run Unit Tests
```bash
php artisan test --filter=ReportTest
```

### Run Feature Tests
```bash
php artisan test --filter=ReportControllerTest
```

### Run All Tests
```bash
php artisan test
php artisan test --coverage  # With coverage report
```

### Test a Single Method
```bash
php artisan test tests/Feature/ReportControllerTest::test_can_create_report
```

---

## 🐛 Common Issues & Fixes

### Issue: Form validation not showing
**Solution:** Ensure form uses @csrf and @error directives

### Issue: Report not saving
**Solution:** Check auth()->id() returns user, verify table structure

### Issue: Routes return 404
**Solution:** Run `php artisan route:list | grep reports`

### Issue: Export downloads broken file
**Solution:** Check CSV headers and file formatting

### Issue: Date filters not working
**Solution:** Verify date format Y-m-d, check database dates

---

## 📱 Device Testing

- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)
- [ ] Form inputs responsive
- [ ] Table scrollable on mobile
- [ ] Buttons clickable on touch devices

---

## 🎯 Success Criteria

All tests pass when:
- ✅ All 11 routes respond correctly
- ✅ CRUD operations work end-to-end
- ✅ Forms validate properly
- ✅ Data persists to database
- ✅ Export generates valid files
- ✅ Dashboard shows accurate data
- ✅ No JavaScript console errors
- ✅ All UI elements render correctly
- ✅ User messages display appropriately
- ✅ Mobile responsive design works

---

## 📊 Test Coverage Goals

| Component | Target | Achieved |
|-----------|--------|----------|
| Routes | 100% | ⏳ |
| Controllers | 80% | ⏳ |
| Models | 90% | ⏳ |
| Views | 50% | ⏳ |
| **Overall** | **80%** | **⏳** |

---

## 🚦 Go/No-Go Checklist

Before moving to Phase 4.2 Mobile Optimization:

- [ ] All manual tests pass
- [ ] No critical bugs found
- [ ] Dashboard data accurate
- [ ] Export functionality working
- [ ] Forms validate correctly
- [ ] Database integration stable
- [ ] 100+ test coverage achieved
- [ ] Documentation updated
- [ ] Code review completed
- [ ] Performance acceptable

---

**Next Steps:** Run manual tests, create automated tests, fix any issues, implement export, then move to Phase 4.2

