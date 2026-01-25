# Profile Image Upload - Troubleshooting Guide

## Error: "An error occurred while uploading the image"

This is a generic error message. Let's debug it step by step.

---

## 🔍 Step 1: Run Diagnostic Tests

### Test Configuration
Open in your browser:
```
http://localhost/Elite/public/test_upload_config.php
```

This will check:
- ✅ Directory exists
- ✅ Directory is writable
- ✅ PHP upload settings
- ✅ File write capability

### Test Simple Upload
The page above has a form. Upload a test image to verify basic upload functionality works.

---

## 🐛 Step 2: Check Common Issues

### Issue A: Directory Doesn't Exist
**Fix:**
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/Elite/public
mkdir -p uploads/profile_images
chmod 755 uploads/profile_images
```

### Issue B: Permission Denied
**Fix:**
```bash
chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads
```

### Issue C: Database Column Missing
**Fix:** Run this in phpMyAdmin:
```sql
USE cricket_academy;
ALTER TABLE User ADD COLUMN ProfileImage VARCHAR(255) DEFAULT NULL;
```

### Issue D: Session Not Set
**Check if logged in:**
- Make sure you're logged in to the player account
- Check that `$_SESSION['user_id']` exists

---

## 🔧 Step 3: Check Browser Console

1. Open browser DevTools (F12)
2. Go to Console tab
3. Try uploading again
4. Look for error messages

**Common console errors:**

### "Failed to fetch" or Network Error
**Cause:** URL is incorrect
**Fix:** Check JavaScript URL in `profile.js`:
```javascript
// Should be:
fetch(window.location.origin + '/Elite/player/uploadProfileImage', {
```

### "404 Not Found"
**Cause:** Route doesn't exist
**Check:** URL should be `http://localhost/Elite/player/uploadProfileImage`

---

## 📋 Step 4: Check PHP Error Logs

**Location:** `/Applications/XAMPP/xamppfiles/logs/php_error_log`

**View recent errors:**
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
```

Then try uploading again and watch for new errors.

---

## 🗃️ Step 5: Verify Database

**Check if column exists:**
```sql
USE cricket_academy;
DESCRIBE User;
```

Look for `ProfileImage` column in the output.

**Check user exists:**
```sql
SELECT UserID, Name, Email, ProfileImage FROM User WHERE UserID = 1;
```

---

## 🌐 Step 6: Test Direct Upload

Create a simple test file to bypass the MVC structure:

**File:** `/public/direct_upload_test.php`
```php
<?php
session_start();
$_SESSION['user_id'] = 1; // Set test user

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = __DIR__ . '/uploads/profile_images/';
    $fileName = 'test_' . time() . '.jpg';
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
        echo "SUCCESS: Image uploaded to " . $uploadDir . $fileName;
    } else {
        echo "FAILED: Could not move file";
    }
} else {
    echo '<form method="POST" enctype="multipart/form-data">';
    echo '<input type="file" name="image" accept="image/*">';
    echo '<button type="submit">Upload</button>';
    echo '</form>';
}
?>
```

Access: `http://localhost/Elite/public/direct_upload_test.php`

---

## 🔄 Step 7: Check Controller Method

Open browser Network tab and check the response when uploading:

### Expected Response (Success):
```json
{
  "success": true,
  "message": "Profile image updated successfully",
  "image_url": "http://localhost/Elite/uploads/profile_images/profile_1_1234567890.jpg"
}
```

### Error Responses:
- `"No file was uploaded"` → File input name mismatch
- `"File upload error occurred"` → PHP upload error
- `"File size must be less than 2MB"` → File too large
- `"Only JPG, JPEG, and PNG files are allowed"` → Wrong file type
- `"Failed to move uploaded file"` → Permission issue
- `"Failed to update profile image in database"` → Database error

---

## 🎯 Quick Fixes

### Fix 1: Reset Permissions
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/Elite
chmod -R 755 public/uploads
chown -R $(whoami) public/uploads
```

### Fix 2: Verify PHP Settings
Check `/Applications/XAMPP/xamppfiles/etc/php.ini`:
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
upload_tmp_dir = /Applications/XAMPP/xamppfiles/temp
```

### Fix 3: Restart Apache
```bash
/Applications/XAMPP/xamppfiles/xampp restart
```

### Fix 4: Clear Browser Cache
- Chrome: Ctrl+Shift+Delete
- Safari: Cmd+Option+E

---

## 📱 Check JavaScript Console

Look for these specific errors:

### TypeError: Cannot read property 'files' of null
**Fix:** File input ID mismatch
```javascript
// Check profile.js has correct ID:
const profileImageInput = document.getElementById('profileImageInput');
```

### Failed to fetch
**Fix:** URL issue in JavaScript
```javascript
// Update profile.js:
fetch(window.location.origin + '/Elite/player/uploadProfileImage', {
    method: 'POST',
    body: formData
})
```

---

## ✅ Final Checklist

Before the upload can work, verify:

- [ ] MySQL is running
- [ ] Apache is running
- [ ] Database `cricket_academy` exists
- [ ] Table `User` has `ProfileImage` column
- [ ] You are logged in as a player
- [ ] Directory `/public/uploads/profile_images/` exists
- [ ] Directory has write permissions (755)
- [ ] File size is under 2MB
- [ ] File type is JPG, JPEG, or PNG
- [ ] Browser console shows no JavaScript errors
- [ ] Network tab shows request to `/player/uploadProfileImage`

---

## 🚑 Emergency Debug Mode

Add this at the start of `uploadProfileImage()` method:

```php
// TEMPORARY DEBUG - REMOVE AFTER FIXING
error_log("=== UPLOAD DEBUG START ===");
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));
error_log("SESSION: " . print_r($_SESSION, true));
error_log("=== UPLOAD DEBUG END ===");
```

Then check PHP error log for details.

---

## 📞 Still Not Working?

Run through this checklist:

1. ✅ Run `test_upload_config.php` - all tests pass?
2. ✅ Run simple upload test - works?
3. ✅ Check PHP error log - any errors?
4. ✅ Check browser console - any errors?
5. ✅ Database column exists?
6. ✅ Logged in as player?

If all above pass but still fails, the issue is likely:
- Session user_id not set correctly
- Database connection issue
- Model method error

**Next step:** Check the exact error message in browser Network tab (Response section).

---

## Contact Info

For additional help, provide:
1. Screenshot of browser console errors
2. Screenshot of Network tab response
3. Output from `test_upload_config.php`
4. Any PHP error log entries
5. Screenshot of the error message
