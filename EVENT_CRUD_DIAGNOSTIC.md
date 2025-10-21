# Event CRUD Operations - Diagnostic & Fix Report

**Date**: October 20, 2025  
**Issue**: Event CRUD operations not working properly  
**Reference**: CRUD_IMPLEMENTATION_GUIDE.md  

---

## 🔍 Diagnostic Checklist

Based on the CRUD Implementation Guide, I've checked all common issues:

### ✅ Issue #1: PDO Case Attribute
**Status**: **FIXED** ✅
- **Location**: `app/libraries/Database.php` line 24
- **Code**: `$this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);`
- **Result**: Column names preserve original casing (EventID, Name, etc.)

---

### ⚠️ Issue #2: HTML Debug Output
**Status**: **NEEDS VERIFICATION** 
- **Problem**: HTML comments or echo statements in controllers/models break JSON/redirects
- **Action Needed**: Check for any `echo`, `var_dump()`, or HTML comments in:
  - `app/controllers/Admin.php`
  - `app/models/Event.php`
  - `app/libraries/Database.php`

**Current Status**:
- ✅ Admin.php uses `error_log()` for debugging
- ✅ Event.php uses `error_log()` for debugging
- ✅ No HTML output detected in model/controller

---

### ⚠️ Issue #3: PHP 8.2 htmlspecialchars() Warnings
**Status**: **NEEDS FIX** 🔧

**Problem**: Passing null to `htmlspecialchars()` causes warnings in PHP 8.2+

**Locations to Check**:
1. `app/views/admin/events.php`
2. `app/views/admin/create_event.php`
3. `app/views/admin/edit_event.php`

**Wrong**:
```php
<?php echo htmlspecialchars($event['Name']); ?>
```

**Correct**:
```php
<?php echo htmlspecialchars($event['Name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
```

**Action**: Search all event view files for htmlspecialchars usage and add null coalescing operator

---

### ⚠️ Issue #4: DateTime Format Mismatches
**Status**: **PARTIALLY FIXED** 🔧

**Findings**:

**Controller** (`Admin.php`):
- ✅ Line 309-310: Correctly adds seconds for create_event
  ```php
  $startDateTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
  $endDateTime = $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00';
  ```

- ✅ Line 453-454: Correctly adds seconds for edit_event
  ```php
  $startDateTime = $_POST['StartDate_date'] . ' ' . $_POST['StartTime'] . ':00';
  $endDateTime = $_POST['EndDate_date'] . ' ' . $_POST['EndTime'] . ':00';
  ```

**Problem**: Field name inconsistency between CREATE and EDIT forms!

---

### 🚨 Issue #5: Field Name Mismatches
**Status**: **CRITICAL ISSUE FOUND** 🔥

**Problem**: Different field names used in CREATE vs EDIT forms!

**CREATE Form** (`create_event.php`):
```php
'event_name'          // lowercase with underscores
'event_type'
'event_category'
'event_venue'
'start_date'
'start_time'
'end_date'
'end_time'
```

**EDIT Form** (expected by controller):
```php
'Name'               // PascalCase (matches database columns)
'Type'
'Category'
'Location'
'StartDate_date'
'StartTime'
'EndDate_date'
'EndTime'
```

**Impact**: CREATE and EDIT use different field naming conventions!

**Required Actions**:
1. ✅ Verify CREATE form field names in `create_event.php`
2. ✅ Verify EDIT form field names (if separate edit form exists)
3. 🔧 Standardize all field names to match database columns
4. 🔧 Update controller to use consistent naming

---

### ⚠️ Issue #6: Redirect After POST
**Status**: **CORRECT** ✅

**Controller redirects properly**:
```php
// After create
flash('event_message', '✅ Event created successfully!', 'alert alert-success');
redirect('admin/events');  // ✅ PRG pattern implemented

// After update
flash('event_message', '✅ Event updated successfully!', 'alert alert-success');
redirect('admin/events');  // ✅ PRG pattern implemented

// After delete
flash('event_message', 'Event deleted successfully');
redirect('admin/events');  // ✅ PRG pattern implemented
```

---

## 📋 Critical Issues Summary

### 🔴 CRITICAL: Field Name Inconsistency

**CREATE form expects** (in `Admin::create_event()`):
```php
$_POST['event_name']          // Line 325
$_POST['event_type']          // Line 326
$_POST['event_category']      // Line 327
$_POST['event_venue']         // Line 335 (mapped to 'location')
$_POST['start_date']          // Line 309
$_POST['start_time']          // Line 309
$_POST['end_date']            // Line 310
$_POST['end_time']            // Line 310
$_POST['event_description']   // Line 328
$_POST['event_status']        // Line 336
$_POST['max_participants']    // Line 337
$_POST['registration_fee']    // Line 338
$_POST['registration_start']  // Line 317
$_POST['registration_end']    // Line 319
$_POST['primary_contact']     // Line 341
$_POST['contact_email']       // Line 342
$_POST['contact_phone']       // Line 343
```

**EDIT form expects** (in `Admin::edit_event()`):
```php
$_POST['Name']                // Line 460
$_POST['Type']                // Line 461
$_POST['Category']            // Line 462
$_POST['Location']            // Line 465
$_POST['StartDate_date']      // Line 453
$_POST['StartTime']           // Line 453
$_POST['EndDate_date']        // Line 454
$_POST['EndTime']             // Line 454
$_POST['Description']         // Line 463
$_POST['Status']              // Line 466
$_POST['MaxParticipants']     // Line 467
$_POST['RegistrationFee']     // Line 468
$_POST['RegistrationStart']   // Line 469
$_POST['RegistrationEnd']     // Line 470
$_POST['PrimaryContact']      // Line 471
$_POST['ContactEmail']        // Line 472
$_POST['ContactPhone']        // Line 473
```

---

## 🛠️ Required Fixes

### Fix #1: Standardize Field Names in Controller

**Decision**: Use PascalCase (matching database column names) for ALL forms

**Update `Admin::create_event()` to match EDIT naming**:

```php
// BEFORE (lines 325-343)
$eventData = [
    'name' => trim($_POST['event_name']),           // ❌ Wrong field name
    'type' => $_POST['event_type'],                 // ❌ Wrong
    'category' => $_POST['event_category'],         // ❌ Wrong
    // ...
];

// AFTER (use PascalCase like EDIT)
$eventData = [
    'name' => trim($_POST['Name']),                 // ✅ Matches database
    'type' => $_POST['Type'],                       // ✅ Matches database
    'category' => $_POST['Category'],               // ✅ Matches database
    // ...
];
```

### Fix #2: Update CREATE Form Field Names

**File**: `app/views/admin/create_event.php`

**Find and replace**:
```php
// OLD field names (lowercase_underscore)
name="event_name"        → name="Name"
name="event_type"        → name="Type"
name="event_category"    → name="Category"
name="event_description" → name="Description"
name="event_venue"       → name="Location"
name="start_date"        → name="StartDate_date"
name="start_time"        → name="StartTime"
name="end_date"          → name="EndDate_date"
name="end_time"          → name="EndTime"
name="event_status"      → name="Status"
name="max_participants"  → name="MaxParticipants"
name="registration_fee"  → name="RegistrationFee"
name="registration_start"→ name="RegistrationStart"
name="registration_end"  → name="RegistrationEnd"
name="primary_contact"   → name="PrimaryContact"
name="contact_email"     → name="ContactEmail"
name="contact_phone"     → name="ContactPhone"
```

### Fix #3: Update Controller to Use Consistent Names

**File**: `app/controllers/Admin.php`

**In `create_event()` method (around line 289-343)**:

```php
// Update required fields check
$requiredFields = ['Name', 'Type', 'Category', 'Location', 
                  'StartDate_date', 'StartTime', 'EndDate_date', 'EndTime',
                  'PrimaryContact', 'ContactEmail', 'ContactPhone'];

// Update datetime combination
$startDateTime = $_POST['StartDate_date'] . ' ' . $_POST['StartTime'] . ':00';
$endDateTime = $_POST['EndDate_date'] . ' ' . $_POST['EndTime'] . ':00';

// Update data array
$eventData = [
    'name' => trim($_POST['Name']),
    'type' => $_POST['Type'],
    'category' => $_POST['Category'],
    'description' => !empty($_POST['Description']) ? trim($_POST['Description']) : null,
    'start_date' => $startDateTime,
    'end_date' => $endDateTime,
    'location' => trim($_POST['Location']),
    'status' => isset($_POST['Status']) ? $_POST['Status'] : 'upcoming',
    'max_participants' => !empty($_POST['MaxParticipants']) ? intval($_POST['MaxParticipants']) : null,
    'registration_fee' => !empty($_POST['RegistrationFee']) ? floatval($_POST['RegistrationFee']) : null,
    'registration_start' => $registrationStart,
    'registration_end' => $registrationEnd,
    'primary_contact' => !empty($_POST['PrimaryContact']) ? trim($_POST['PrimaryContact']) : null,
    'contact_email' => !empty($_POST['ContactEmail']) ? trim($_POST['ContactEmail']) : null,
    'contact_phone' => !empty($_POST['ContactPhone']) ? trim($_POST['ContactPhone']) : null
];
```

---

## 📝 Testing Checklist

After fixes are applied:

### Create Event:
- [ ] Open `/admin/create_event`
- [ ] Fill all required fields
- [ ] Submit form
- [ ] Check for success message
- [ ] Verify event appears in database
- [ ] Verify redirect to `/admin/events`

### Edit Event:
- [ ] Click edit on an existing event
- [ ] Modify event details
- [ ] Submit form
- [ ] Check for success message
- [ ] Verify changes in database
- [ ] Verify redirect to `/admin/events`

### Delete Event:
- [ ] Click delete on an event
- [ ] Confirm deletion
- [ ] Check for success message
- [ ] Verify event removed from database
- [ ] Verify redirect to `/admin/events`

### View Events:
- [ ] Navigate to `/admin/events`
- [ ] Verify upcoming events display
- [ ] Verify past events display
- [ ] Check event stats are accurate

---

## 🔧 Implementation Priority

1. **HIGH PRIORITY**: Fix field name consistency between CREATE and EDIT
2. **MEDIUM**: Add null coalescing to all htmlspecialchars() calls
3. **LOW**: Verify no HTML debug output in production

---

## 📄 Files Requiring Changes

### Controllers:
- ✅ `app/controllers/Admin.php` - Lines 289-343 (create_event method)

### Views:
- ✅ `app/views/admin/create_event.php` - All form field names
- ⚠️ `app/views/admin/events.php` - Check htmlspecialchars usage
- ⚠️ Check if separate edit form exists

### Models:
- ✅ `app/models/Event.php` - No changes needed (already correct)

### Database:
- ✅ `app/libraries/Database.php` - Already configured correctly

---

## 🎯 Next Steps

1. **Search for all htmlspecialchars() usage in event views**
2. **Update CREATE form field names to match EDIT**
3. **Update controller to use consistent field names**
4. **Test all CRUD operations**
5. **Check browser console and PHP error logs for issues**

---

**Prepared By**: GitHub Copilot  
**Based On**: CRUD_IMPLEMENTATION_GUIDE.md  
**Status**: Ready for Implementation
