# Quick Start Guide - Profile Image Upload Feature

## 🚀 Quick Setup (3 Steps)

### Step 1: Run Database Migration (Required)

Open phpMyAdmin or MySQL command line and run:

```sql
USE cricket_academy;

ALTER TABLE User 
ADD COLUMN ProfileImage VARCHAR(255) DEFAULT NULL;
```

### Step 2: Add Default Avatar Image (Required)

You have 3 options:

**Option A: Use Online Avatar Service (Easiest - No file needed)**
The code already has a fallback that will show a default icon if the image doesn't exist.

**Option B: Download a Default Avatar**
1. Go to: https://www.iconfinder.com/search?q=user+avatar&price=free
2. Download a user avatar icon (PNG, 200x200px)
3. Save as: `/public/images/default-avatar.png`

**Option C: Create Simple Avatar**
Use the SVG template provided in `/public/images/default-avatar-svg-template.html`
- Open the file in a browser
- Take a screenshot
- Save as `default-avatar.png` in `/public/images/`

### Step 3: Test It! 

1. Start XAMPP (Apache + MySQL)
2. Go to: `http://localhost/Elite/player/profile`
3. Click the profile image or "Upload Photo" button
4. Select a JPG or PNG image (under 2MB)
5. Watch it upload! ✨

---

## 📋 Verification Checklist

Run these checks to ensure everything works:

```bash
# 1. Check if directory exists
ls -la /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads/profile_images

# 2. Check directory permissions
chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads/profile_images

# 3. Check if column was added (MySQL)
mysql -u root -p cricket_academy -e "DESCRIBE User;"
```

---

## 🎯 What Was Implemented

| Component | Status | Description |
|-----------|--------|-------------|
| **Model** | ✅ | Added 3 methods to M_Users.php |
| **Controller** | ✅ | Added 2 methods to Player.php |
| **View** | ✅ | Added image upload UI to profile.php |
| **CSS** | ✅ | Added styling in profile.css |
| **JavaScript** | ✅ | Added upload logic in profile.js |
| **Database** | ⚠️ | Need to run SQL migration |
| **Default Image** | ⚠️ | Need to add default avatar |
| **Directory** | ✅ | Created uploads folder |

---

## 🔧 Testing Commands

### Test Upload
```javascript
// Open browser console on profile page
// Verify these functions exist:
console.log(typeof uploadProfileImage); // should show "function"
```

### Test Database
```sql
-- Check if column exists
USE cricket_academy;
SHOW COLUMNS FROM User LIKE 'ProfileImage';

-- Should show:
-- ProfileImage | varchar(255) | YES | | NULL |
```

### Test File Upload
```bash
# Check if uploads directory is writable
touch /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads/profile_images/test.txt
rm /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads/profile_images/test.txt
```

---

## ⚡ Common Issues & Fixes

### "Column ProfileImage not found"
```sql
-- Run this in phpMyAdmin SQL tab:
ALTER TABLE User ADD COLUMN ProfileImage VARCHAR(255) DEFAULT NULL;
```

### "Default image not found"
The system will still work! It shows a fallback icon. But to add a proper image:
1. Find any user avatar PNG online
2. Save to: `/public/images/default-avatar.png`

### "Failed to upload"
```bash
# Fix permissions:
sudo chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/Elite/public/uploads
```

### "Image not displaying"
Check URLROOT in `/app/config/config.php`:
```php
define('URLROOT', 'http://localhost/Elite');
```

---

## 📁 Files Modified/Created

### Modified (5 files):
- ✅ `app/models/M_Users.php` - Added image methods
- ✅ `app/controllers/Player.php` - Added upload/delete handlers
- ✅ `app/views/player/profile.php` - Added UI
- ✅ `public/css/player/profile.css` - Added styling
- ✅ `public/js/player/profile.js` - Added upload logic

### Created (4 files):
- ✅ `public/uploads/profile_images/` - Upload directory
- ✅ `public/uploads/profile_images/.gitignore` - Git ignore
- ✅ `add_profile_image_column.sql` - Database migration
- ✅ `PROFILE_IMAGE_IMPLEMENTATION.md` - Full documentation

---

## 🎨 Features

✨ **Upload** - JPG, JPEG, PNG files up to 2MB
✨ **Preview** - See image before and after upload
✨ **Validation** - Client and server-side validation
✨ **Delete** - Remove profile image anytime
✨ **Default** - Shows placeholder if no image
✨ **Security** - File type and size validation
✨ **Animation** - Smooth hover effects
✨ **Responsive** - Works on all devices

---

## 📖 Usage Example

### In Other Pages
Want to show the user's profile image elsewhere?

```php
<?php
// Get user profile image
$profileImage = $data['user']->ProfileImage ?? null;
$imageUrl = $profileImage 
    ? URLROOT . '/' . $profileImage 
    : URLROOT . '/images/default-avatar.png';
?>

<img src="<?php echo $imageUrl; ?>" alt="Profile" class="avatar">
```

---

## 🎉 You're Done!

After running the SQL migration and adding a default avatar, the feature is ready to use!

**Need help?** Check `PROFILE_IMAGE_IMPLEMENTATION.md` for detailed documentation.

---

## 📊 Technical Specs

- **Max File Size**: 2MB
- **Allowed Types**: JPG, JPEG, PNG
- **Storage**: `/public/uploads/profile_images/`
- **Naming**: `profile_{userId}_{timestamp}.{ext}`
- **Database Field**: VARCHAR(255)
- **Authentication**: Required (user must be logged in)

---

## Support

If something doesn't work:
1. Check PHP error logs: `/Applications/XAMPP/xamppfiles/logs/php_error_log`
2. Check browser console (F12) for JavaScript errors
3. Verify all files exist in correct locations
4. Run the verification commands above

**Everything should work out of the box after the 3-step setup! 🚀**
