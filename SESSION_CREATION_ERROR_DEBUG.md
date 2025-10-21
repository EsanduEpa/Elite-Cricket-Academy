# Session Creation Error - Debugging Guide

## Error: "Failed to create session"

This guide will help you identify and fix the issue.

---

## Step 1: Check Browser Console

1. Open browser DevTools (F12)
2. Go to Console tab
3. Try to create a session
4. Look for the following outputs:

```javascript
Session Data to be saved: { ... }  // What's being sent
Response status: 200              // HTTP status
Response data: { ... }            // What backend returns
```

**What to look for:**
- ✅ All required fields are present in "Session Data to be saved"
- ✅ Response status is 200
- ❌ If `success: false`, check the `message` field

---

## Step 2: Check PHP Error Logs

### Location of Error Logs:
- **XAMPP on macOS**: `/Applications/XAMPP/xamppfiles/logs/php_error_log`
- **Alternative**: Check XAMPP control panel logs

### How to check:
```bash
# In terminal, run:
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log

# Then try creating a session
# Look for error messages
```

**What to look for:**
- Database connection errors
- SQL syntax errors
- Missing required fields
- PDO exceptions

---

## Step 3: Run Test Script

I've created a test file to isolate the issue.

### Run the test:
1. Open browser
2. Go to: `http://localhost/test_session_creation.php`

**Before running:**
Edit line 10 in `/public/test_session_creation.php`:
```php
$_SESSION['user_id'] = 1; // ← Change to YOUR coach UserID
```

### Find your coach ID:
```sql
-- Run in phpMyAdmin or MySQL:
SELECT UserID, Username, Role FROM User WHERE Role = 'Coach';
```

---

## Step 4: Common Issues & Solutions

### Issue 1: Session user_id not set
**Error:** `coach_id is NULL or 0`

**Solution:**
```php
// Check in browser console:
console.log('Session user_id:', <?php echo $_SESSION['user_id'] ?? 'NOT SET'; ?>);

// If not set, you're not logged in
// Log in as a coach first
```

### Issue 2: SessionDetails table doesn't exist
**Error:** `Table 'SessionDetails' doesn't exist`

**Check:**
```sql
SHOW TABLES LIKE 'SessionDetails';
```

**Solution:** Create the table or comment out SessionDetails insert in M_Session.php (lines 66-87)

### Issue 3: Foreign key constraint fails
**Error:** `Cannot add or update a child row: a foreign key constraint fails`

**Cause:** `CoachOrTrainerID` doesn't exist in User table

**Check:**
```sql
SELECT UserID, Username, Role FROM User WHERE UserID = YOUR_ID;
```

**Solution:** Use a valid coach UserID

### Issue 4: Date/Time format error
**Error:** `Incorrect DATE value` or `Incorrect TIME value`

**Check format in wizard:**
- Date should be: `YYYY-MM-DD` (e.g., `2025-10-25`)
- Time should be: `HH:MM` (e.g., `09:00`)

**Fix:** Check JavaScript date formatting in wizard

### Issue 5: ENUM value mismatch
**Error:** `Data truncated for column 'SessionType'`

**Valid values:**
- SessionType: `'Coaching'` or `'Physical Training'` (case-sensitive!)
- SessionMode: `'Group'` or `'Private'`

---

## Step 5: Manual Database Test

Try inserting directly into database to isolate if it's a database issue:

```sql
-- Test insert (change values as needed)
INSERT INTO Session (
    SessionType,
    SessionMode,
    CoachOrTrainerID,
    Name,
    Date,
    StartTime,
    EndTime,
    Location,
    Status,
    MaxParticipants,
    PricePerSession,
    IsRecurring
) VALUES (
    'Coaching',           -- Must match ENUM
    'Private',            -- Must match ENUM
    1,                    -- YOUR coach UserID
    'Test Session',       
    '2025-10-25',         -- Future date
    '09:00:00',          
    '11:00:00',          
    'Test Location',      
    'active',             
    10,                   
    50.00,                
    TRUE                  
);

-- Check if it worked
SELECT * FROM Session ORDER BY SessionID DESC LIMIT 1;
```

**If this fails:** The issue is with your database schema or data
**If this works:** The issue is with the PHP code

---

## Step 6: Check Network Request

1. Open DevTools → Network tab
2. Try creating a session
3. Find the `create_session` request
4. Click on it
5. Check:
   - **Headers tab**: Method should be POST
   - **Payload tab**: Should show JSON data
   - **Response tab**: Should show `{"success":false,"message":"..."}`

---

## Step 7: Enable Full Error Display

Temporarily enable error display in PHP:

**Edit `/app/controllers/Coach.php` line 1:**
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Coach extends Controller {
    // ... rest of code
```

**⚠️ Remember to remove this in production!**

---

## Step 8: Verify Data Mapping

Check what's being sent to the model:

**Add debug output in `/app/controllers/Coach.php` (around line 392):**
```php
// Before calling createSession
error_log('=== SESSION CREATION DEBUG ===');
error_log('Mapped data: ' . print_r($data, true));

$sessionId = $sessionModel->createSession($data);

error_log('Result sessionId: ' . ($sessionId ?: 'FALSE'));
error_log('=== END DEBUG ===');
```

Then check PHP error log for the output.

---

## Quick Fix Checklist

- [ ] Logged in as a Coach user
- [ ] `$_SESSION['user_id']` is set and valid
- [ ] Coach exists in User table with matching UserID
- [ ] Session table exists in database
- [ ] Date is in format `YYYY-MM-DD`
- [ ] Time is in format `HH:MM`
- [ ] SessionType is exactly `'Coaching'` or `'Physical Training'`
- [ ] SessionMode is exactly `'Group'` or `'Private'`
- [ ] Database user has INSERT permissions
- [ ] No foreign key constraint violations

---

## Most Likely Causes (in order)

1. **Not logged in / Session expired** (80%)
   - Solution: Log in as coach again

2. **SessionDetails table doesn't exist** (15%)
   - Solution: Comment out SessionDetails insert (temporary)

3. **Invalid CoachOrTrainerID** (3%)
   - Solution: Use correct coach UserID

4. **Database connection issue** (1%)
   - Solution: Check XAMPP MySQL is running

5. **Other** (1%)
   - Solution: Follow debug steps above

---

## Next Steps

1. **Check browser console** for detailed error
2. **Run test script** at `/test_session_creation.php`
3. **Check PHP error log** for exceptions
4. **Try manual SQL insert** to verify database

**Report back with:**
- Browser console output
- PHP error log messages  
- Test script results
- Any SQL errors

This will help identify the exact issue!

---

## Temporary Workaround

If SessionDetails is causing the issue, you can temporarily disable it:

**Edit `/app/models/M_Session.php` line 63:**
```php
if ($this->db->execute()) {
    $sessionId = $this->db->lastInsertId();
    
    // TEMPORARILY COMMENTED OUT - SessionDetails might not exist
    /*
    $this->db->query('INSERT INTO SessionDetails (
        SessionID,
        ...
    )');
    ...
    $this->db->execute();
    */
    
    return $sessionId;
}
```

This will allow sessions to be created without SessionDetails.
