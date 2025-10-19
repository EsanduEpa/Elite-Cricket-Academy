# Event Creation Troubleshooting Guide

## Issue: Form Sometimes Fails to Submit

### Enhanced Error Tracking Added

**Files Modified:**
1. `public/js/admin/create-event-wizard.js` - Enhanced submitForm() with detailed logging
2. `app/controllers/Admin.php` - Added validation and error logging

---

## What to Check When Submission Fails

### 1. Check Browser Console (F12)

**Expected successful output:**
```
=== MODAL EVENT FORM SUBMISSION STARTED ===
Current step: 4
✓ Final validation passed
Form found: <form#eventWizardForm>
Form action: http://localhost/Elite/admin/create_event
Form method: POST
Form data being submitted (21 fields):
  1. event_name: Test Event
  2. event_type: tournament
  ... (all fields)
Total fields: 21
✓ Submit button disabled, showing loading state
✓ Submitting form to server...
✓ Form.submit() called successfully
```

**If you see validation errors:**
```
✗ Validation failed - form not submitted
Step 4 validation failed
Invalid fields: [array of field IDs]
```
**Action:** Fill the missing/invalid fields

---

### 2. Check Apache Error Log

**Location:** `/Applications/XAMPP/xamppfiles/logs/error_log`

**Command to view:**
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/error_log | grep -i event
```

**Expected successful output:**
```
======================================
Event creation POST received at: 2025-10-19 15:30:45
Session user_id: 1
POST data count: 21
POST data: Array...
Start DateTime: 2025-10-25 09:00:00
End DateTime: 2025-10-27 18:00:00
Event created successfully
```

**If you see errors:**
```
ERROR: User not logged in!
```
**Action:** Login again as admin

```
ERROR: Missing required fields: event_name, event_type
```
**Action:** Fill those specific fields

---

### 3. Common Failure Scenarios

#### Scenario 1: Not Logged In
**Symptom:** Form submits but redirects to login
**Console:** No error
**Error Log:** `ERROR: User not logged in!`
**Fix:** Login at `/admin/login` with admin credentials

#### Scenario 2: Missing Required Fields
**Symptom:** Alert says "Please fill all required fields"
**Console:** Shows which fields are empty
**Error Log:** `ERROR: Missing required fields: ...`
**Fix:** Go back and fill the missing fields in the wizard

#### Scenario 3: Validation Fails
**Symptom:** Alert says "Please fill all required fields correctly"
**Console:** Shows invalid fields
**Fix:** Check the fields marked with errors (red borders)

#### Scenario 4: Date/Time Issues
**Symptom:** End date validation fails
**Console:** `End date must be after start date`
**Fix:** Make sure end date/time is after start date/time

#### Scenario 5: Form Action Missing
**Symptom:** Alert says "Form action URL is missing"
**Console:** `❌ Form action is empty!`
**Fix:** Hard refresh page (Cmd+Shift+R) and try again

---

## Required Fields Checklist

Before submitting, ensure ALL these are filled:

### Step 1: Basic Info
- [x] Event Name
- [x] Event Type
- [x] Event Category
- [x] Venue

### Step 2: Schedule
- [x] Start Date
- [x] Start Time
- [x] End Date
- [x] End Time

### Step 3: Management
- [x] Primary Contact
- [x] Contact Email
- [x] Contact Phone

**Optional fields can be left empty.**

---

## Debug Commands

### Test if you're logged in:
```bash
# Check PHP session
php -r "session_start(); print_r(\$_SESSION);"
```

### Check if Event table exists:
```bash
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "DESCRIBE Event;"
```

### Check last event created:
```bash
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "SELECT EventID, Name, Type, StartDate FROM Event ORDER BY EventID DESC LIMIT 1;"
```

### Watch error log in real-time:
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/error_log
```

### Clear error log:
```bash
> /Applications/XAMPP/xamppfiles/logs/error_log
```

---

## Step-by-Step Testing Process

### 1. Clear Browser Cache
```
Cmd + Shift + R (macOS)
Ctrl + Shift + R (Windows)
```

### 2. Open Console
```
Press F12
Click "Console" tab
```

### 3. Make Sure You're Logged In
```
Visit: http://localhost/Elite/login
Username: admin
Password: password123
```

### 4. Go to Events Page
```
http://localhost/Elite/admin/events
```

### 5. Open Modal
```
Click "Create New Event" button
Watch console for: "Opening create event modal..."
```

### 6. Fill Step 1
```
Event Name: Test Event
Event Type: Tournament
Event Category: Senior
Venue: Main Stadium
Description: Test description
```

### 7. Fill Step 2
```
Start Date: Tomorrow
Start Time: 09:00
End Date: Tomorrow
End Time: 18:00
```

### 8. Fill Step 3
```
Primary Contact: John Doe
Contact Email: john@test.com
Contact Phone: +94771234567
```

### 9. Review Step 4
```
Check all data is correct
```

### 10. Submit
```
Click "Create Event"
Watch console output
Check for alerts
Wait for redirect
```

### 11. Verify Success
```
- Should redirect to /admin/events
- Should show green success message
- Event should appear in list
```

---

## Enhanced Error Messages

### New JavaScript Alerts:

**Form not found:**
```
Error: Form element not found. Please refresh the page and try again.
```

**Form action missing:**
```
Error: Form action URL is missing. Please contact support.
```

**Required fields empty:**
```
Please fill all required fields:
- event_name
- event_type
```

**Validation failed:**
```
Please fill all required fields correctly before submitting.
```

**Form submission error:**
```
Error submitting form: [error message]
```

---

## New Console Logging

### Added Details:
- Current step number
- Form element reference
- Form action URL verification
- Field count
- Individual field values with numbering
- Empty required field detection
- Try-catch around form.submit()
- Success confirmation after submit()

---

## Server-Side Validation

### Added Checks:
1. ✅ User logged in
2. ✅ All required fields present
3. ✅ Field values not empty
4. ✅ Date/time format valid
5. ✅ Session user_id exists

### Error Messages:
- `⚠️ You must be logged in to create events`
- `⚠️ Missing required fields: [list]`
- `⚠️ Please fill all required fields`
- `❌ Failed to create event`
- `❌ Database error: [message]`

---

## If Still Failing

### 1. Check MySQL is Running
```bash
/Applications/XAMPP/xamppfiles/bin/mysql.server status
```

### 2. Check Database Connection
```bash
/Applications/XAMPP/bin/mysql -u root -e "SELECT 1;"
```

### 3. Check Event Table Structure
```bash
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "SHOW COLUMNS FROM Event;"
```

### 4. Check Apache is Running
```bash
sudo /Applications/XAMPP/xamppfiles/bin/apachectl status
```

### 5. Check PHP Errors
Edit `/Applications/XAMPP/xamppfiles/etc/php.ini`:
```ini
display_errors = On
error_reporting = E_ALL
```

Restart Apache:
```bash
sudo /Applications/XAMPP/xamppfiles/bin/apachectl restart
```

---

## Contact Support

If you've tried everything and it still fails:

1. Copy the **complete console output**
2. Copy the **last 50 lines of error_log**
3. Note **which step fails** (1, 2, 3, or 4)
4. Note **what error message** appears
5. Provide **screenshot** of the form

---

## Success Indicators

You'll know it works when you see:

✅ Console: `✓ Form.submit() called successfully`  
✅ Page redirects to `/admin/events`  
✅ Green message: "✅ Event created successfully!"  
✅ Event appears in the list  
✅ Database has new row in Event table  

---

**Last Updated:** October 19, 2025  
**Status:** Enhanced error tracking active
