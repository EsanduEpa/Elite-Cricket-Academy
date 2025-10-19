# Event Display Debugging Guide

## Step 1: Check Browser Console

1. Open the admin events page: `http://localhost/Elite/admin/events`
2. Open browser DevTools: Press **F12** or **Right-click → Inspect**
3. Go to **Console** tab
4. Look for these debug messages:

### Expected Console Output:

```
=== EVENTS PAGE DEBUG ===
URLROOT: http://localhost/Elite
CSS file path: http://localhost/Elite/css/admin/create-event-wizard.css
JS file path: http://localhost/Elite/js/admin/create-event-wizard.js

=== PHP DATA DEBUG ===
Upcoming Events: [Array of 3 events]
Past Events: []
Upcoming Events Count: 3
Past Events Count: 0

Modal element found: true
Button element found: true
openCreateEventModal function exists: function

=== CALENDAR INITIALIZATION ===
Calendar element found: true
Rendering calendar...
✅ Calendar rendered successfully

=== FETCHING CALENDAR EVENTS ===
Fetching from URL: http://localhost/Elite/admin/get_calendar_events
Response status: 200
Response OK: true
✅ Calendar events received: [Array of 3 events]
Number of events: 3
Event rendered on calendar: Music Festival
Event rendered on calendar: Music Festival
Event rendered on calendar: Music Festival

=== EVENT TABLES DEBUG ===
Upcoming table found: true
Past table found: true
Upcoming table rows: 3
  Row 1: Event ID 3
  Row 2: Event ID 7
  Row 3: Event ID 1
Past table rows: 1
```

---

## Step 2: Check Network Tab

1. Stay in DevTools
2. Go to **Network** tab
3. Reload page (Cmd+R or Ctrl+R)
4. Look for these requests:

### Expected Network Requests:

| File | Status | Type | Size |
|------|--------|------|------|
| events | 200 | document | ~50KB |
| get_calendar_events | 200 | xhr/fetch | ~500B |
| create-event-wizard.js | 200 | script | ~20KB |
| events.js | 200 | script | ~15KB |

**If any show 404 or 500:** That's your problem!

---

## Step 3: Test Data Retrieval Directly

Visit this URL in your browser:
```
http://localhost/Elite/debug_events.php
```

This will show:
1. ✅ Upcoming events from model
2. ✅ Past events from model
3. ✅ Calendar events (formatted for FullCalendar)
4. ✅ Direct database query results
5. ✅ Server time vs database time
6. ✅ All events with future/past flag

**Expected:** Should show 3 upcoming events

---

## Step 4: Test Calendar Endpoint

Visit this URL directly:
```
http://localhost/Elite/admin/get_calendar_events
```

**Expected Response:**
```json
[
  {
    "id": 3,
    "title": "Music Festival",
    "start": "2025-10-20 20:44:00",
    "className": "event-Match",
    "extendedProps": {
      "description": "",
      "location": "main Hall",
      "type": "Match"
    }
  },
  {
    "id": 7,
    "title": "Music Festival",
    "start": "2025-10-20 23:18:00",
    "className": "event-Match",
    "extendedProps": {
      "description": "Good",
      "location": "Main Hall",
      "type": "Match"
    }
  },
  {
    "id": 1,
    "title": "Music Festival",
    "start": "2025-10-27 20:31:00",
    "className": "event-Match",
    "extendedProps": {
      "description": "",
      "location": "J",
      "type": "Match"
    }
  }
]
```

**If you see PHP error instead:** Check PHP error logs!

---

## Step 5: Check PHP Error Logs

In terminal, run:
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
```

Then reload the events page.

**Look for:**
```
=== EVENTS PAGE DEBUG ===
Upcoming events count: 3
Past events count: 0
First upcoming event: Array...
```

**Common Errors:**
- Database connection failed → Check XAMPP MySQL is running
- Class not found → Check file paths in bootloader
- SQL error → Check Event table exists

---

## Step 6: Check Database Connection

In terminal:
```bash
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "SELECT COUNT(*) as total FROM Event;"
```

**Expected:** Shows total count (should be 3)

If error: MySQL not running or database doesn't exist!

---

## Common Issues & Solutions

### Issue 1: Empty Upcoming Events Array
**Symptom:** Console shows `Upcoming Events: []`

**Check:**
```sql
SELECT EventID, Name, StartDate FROM Event WHERE StartDate >= NOW();
```

**Solution:** Events might be in the past. Update their dates:
```sql
UPDATE Event SET StartDate = '2025-10-25 10:00:00' WHERE EventID = 1;
```

### Issue 2: Calendar Not Rendering
**Symptom:** Console shows "Calendar element not found"

**Solution:** Check if FullCalendar CDN is blocked. Look in Network tab for:
```
https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js
```

### Issue 3: JavaScript Errors
**Symptom:** Red errors in console

**Common errors:**
- `FullCalendar is not defined` → CDN not loaded
- `openCreateEventModal is not defined` → create-event-wizard.js not loaded
- `Unexpected token <` → PHP error in AJAX response

### Issue 4: Tables Show "No events found"
**Symptom:** Table shows empty state message

**Check:**
1. Console: `Upcoming Events Count: 0` → Model returning empty array
2. Network: Check if page loaded successfully (200 status)
3. PHP logs: Look for database errors

---

## Quick Diagnostic Commands

```bash
# 1. Check if events exist in future
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "
SELECT EventID, Name, 
       StartDate,
       CASE WHEN StartDate >= NOW() THEN 'FUTURE' ELSE 'PAST' END as TimeStatus
FROM Event 
ORDER BY StartDate;
"

# 2. Check current server time
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "SELECT NOW();"

# 3. Check PHP errors
tail -20 /Applications/XAMPP/xamppfiles/logs/php_error_log

# 4. Check Apache errors  
tail -20 /Applications/XAMPP/xamppfiles/logs/error_log
```

---

## Action Plan

**Do these in order:**

1. ✅ Open browser console and screenshot any errors
2. ✅ Visit `http://localhost/Elite/debug_events.php` 
3. ✅ Visit `http://localhost/Elite/admin/get_calendar_events`
4. ✅ Check PHP error logs
5. ✅ Report back with:
   - What you see in console
   - What debug_events.php shows
   - Any error messages

---

## Files to Check

If you get errors, check these files exist:

```
✅ /Applications/XAMPP/xamppfiles/htdocs/Elite/app/models/Event.php
✅ /Applications/XAMPP/xamppfiles/htdocs/Elite/app/controllers/Admin.php
✅ /Applications/XAMPP/xamppfiles/htdocs/Elite/app/views/admin/events.php
✅ /Applications/XAMPP/xamppfiles/htdocs/Elite/public/js/admin/events.js
✅ /Applications/XAMPP/xamppfiles/htdocs/Elite/public/js/admin/create-event-wizard.js
```

---

## Expected Final Result

When everything works:

1. **Console:** Shows 3 events loaded
2. **Calendar:** Shows events on Oct 20 & 27
3. **Upcoming Events Table:** Shows 3 rows
4. **Past Events Table:** Shows "No past events" message
5. **No errors** in console or network tab

---

## Need Help?

Share the output from:
1. Browser console (screenshot)
2. `http://localhost/Elite/debug_events.php`
3. PHP error logs (last 20 lines)

This will tell us exactly what's wrong! 🔍
