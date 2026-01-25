# User Profile Image Upload Feature - Implementation Guide

## Overview
This implementation adds a complete profile image upload system to your Elite Cricket Academy web application following MVC architecture. Users can now upload, update, and delete their profile images with proper validation and security.

## Features Implemented

### 1. Database Schema
- Added `ProfileImage` column to the `User` table
- Column stores relative path to profile images (e.g., `uploads/profile_images/profile_1_123456789.jpg`)

### 2. Model Layer (M_Users.php)
Three new methods added to handle profile image operations:
- `updateProfileImage($userId, $imagePath)` - Update user's profile image path
- `getProfileImage($userId)` - Retrieve user's current profile image path
- `deleteProfileImage($userId)` - Remove profile image from database

### 3. Controller Layer (Player.php)
Two new methods added to handle image upload and deletion:
- `uploadProfileImage()` - Handles file upload with validation
  - Validates file type (JPG, JPEG, PNG only)
  - Validates file size (max 2MB)
  - Generates unique filename to prevent conflicts
  - Deletes old image when uploading new one
  - Returns JSON response for AJAX handling
  
- `deleteProfileImage()` - Handles image deletion
  - Removes file from server
  - Clears database reference
  - Returns JSON response

### 4. View Layer (profile.php)
Enhanced profile page with image upload section featuring:
- Profile image display with circular styling
- Hover overlay effect for better UX
- Upload and delete buttons
- Real-time file validation info
- Success/error message display
- Preview functionality

### 5. Styling (profile.css)
Added comprehensive styling for:
- Circular profile image with blue gradient border
- Smooth hover effects and animations
- Responsive button design
- Message notifications with slide-down animation
- Mobile-friendly layout

### 6. JavaScript (profile.js)
Client-side functionality including:
- Image preview before upload
- Client-side validation (file type and size)
- AJAX upload without page refresh
- Success/error message handling
- Automatic page reload after upload/delete

## File Structure

```
Elite/
├── app/
│   ├── models/
│   │   └── M_Users.php (✓ Updated - Added image methods)
│   ├── controllers/
│   │   └── Player.php (✓ Updated - Added upload/delete methods)
│   └── views/
│       └── player/
│           └── profile.php (✓ Updated - Added image upload UI)
├── public/
│   ├── css/
│   │   └── player/
│   │       └── profile.css (✓ Updated - Added image styles)
│   ├── js/
│   │   └── player/
│   │       └── profile.js (✓ Updated - Added upload functionality)
│   ├── images/
│   │   └── default-avatar.png (⚠️ Need to add)
│   └── uploads/
│       └── profile_images/ (✓ Created - With .gitignore)
└── add_profile_image_column.sql (✓ Created - Database migration)
```

## Setup Instructions

### Step 1: Database Migration
Run the SQL migration script to add the ProfileImage column:

```bash
# Option 1: Using MySQL command line
mysql -u root -p cricket_academy < add_profile_image_column.sql

# Option 2: Using phpMyAdmin
# 1. Open phpMyAdmin (http://localhost/phpmyadmin)
# 2. Select 'cricket_academy' database
# 3. Click 'SQL' tab
# 4. Open and paste contents of add_profile_image_column.sql
# 5. Click 'Go' button
```

### Step 2: Add Default Avatar Image
You need to add a default avatar image for users without profile pictures:

1. **Find or create a default avatar image** (PNG format recommended)
2. **Name it**: `default-avatar.png`
3. **Place it in**: `/public/images/default-avatar.png`

**Recommended**: Use a neutral silhouette or placeholder avatar (200x200px minimum)

**Alternative**: You can use any online placeholder service by updating the image URL in profile.php:
```php
$imageUrl = $profileImage ? URLROOT . '/' . $profileImage : 'https://ui-avatars.com/api/?name=' . urlencode($data['user']->Name) . '&size=200';
```

### Step 3: Verify Directory Permissions
Ensure the uploads directory has write permissions:

```bash
# Navigate to your project directory
cd /Applications/XAMPP/xamppfiles/htdocs/Elite

# Set proper permissions for uploads directory
chmod -R 755 public/uploads/profile_images
```

### Step 4: Test the Feature

1. **Start XAMPP** (Apache and MySQL)
2. **Navigate to** `http://localhost/Elite/player/profile`
3. **Test Upload**:
   - Click on profile image or "Upload Photo" button
   - Select a JPG/PNG image (under 2MB)
   - Verify image uploads successfully
4. **Test Delete**:
   - Click "Remove" button
   - Confirm deletion
   - Verify image is removed and default shows

## Technical Details

### File Upload Configuration

**Allowed file types**: JPG, JPEG, PNG
**Maximum file size**: 2MB (2,097,152 bytes)
**Upload directory**: `/public/uploads/profile_images/`
**Filename format**: `profile_{userId}_{timestamp}.{extension}`

### Security Features

✅ **File type validation** - Uses `mime_content_type()` to verify actual file type
✅ **File size validation** - Prevents large files from being uploaded
✅ **Unique filenames** - Prevents filename collisions
✅ **Old file deletion** - Removes previous images to save space
✅ **Path sanitization** - Stores relative paths only
✅ **Authentication check** - Requires user login

### API Endpoints

#### Upload Profile Image
```
POST /Elite/player/uploadProfileImage
Content-Type: multipart/form-data
Body: profile_image (file)

Response:
{
  "success": true,
  "message": "Profile image updated successfully",
  "image_url": "http://localhost/Elite/uploads/profile_images/profile_1_1234567890.jpg"
}
```

#### Delete Profile Image
```
POST /Elite/player/deleteProfileImage

Response:
{
  "success": true,
  "message": "Profile image deleted successfully"
}
```

## Troubleshooting

### Issue: Upload fails with "Failed to move uploaded file"
**Solution**: Check directory permissions
```bash
chmod -R 755 public/uploads/profile_images
```

### Issue: "Default image not found" error
**Solution**: Add default-avatar.png to `/public/images/` directory

### Issue: Large files not uploading
**Solution**: Check PHP upload limits in php.ini:
```ini
upload_max_filesize = 2M
post_max_size = 8M
```

### Issue: Images not displaying
**Solution**: Verify the path in `config.php`:
```php
define('URLROOT', 'http://localhost/Elite');
```

## Future Enhancements (Optional)

### 1. Image Cropping
Add client-side image cropping before upload using libraries like Cropper.js

### 2. Multiple Image Formats
Extend support for WebP, GIF formats

### 3. Image Optimization
Automatically resize/compress images on upload to save storage

### 4. Progress Bar
Show upload progress for large files

### 5. Drag & Drop
Add drag-and-drop functionality for better UX

## Code Examples

### How to Display User Image Elsewhere

```php
// In any view file
<?php
$userModel = $this->model('M_Users');
$userId = $_SESSION['user_id'];
$imagePath = $userModel->getProfileImage($userId);
$imageUrl = $imagePath ? URLROOT . '/' . $imagePath : URLROOT . '/images/default-avatar.png';
?>

<img src="<?php echo $imageUrl; ?>" alt="Profile" class="user-avatar">
```

### How to Update getUserWithProfile Method

The existing `getUserWithProfile()` method in M_Users.php already includes ProfileImage in the SELECT statement since we added it to the User table.

## Testing Checklist

- [ ] Database migration completed successfully
- [ ] Default avatar image added
- [ ] Upload directory has correct permissions
- [ ] Can upload JPG image successfully
- [ ] Can upload PNG image successfully
- [ ] Cannot upload files larger than 2MB
- [ ] Cannot upload non-image files
- [ ] Old image is deleted when new one is uploaded
- [ ] Can delete profile image successfully
- [ ] Default avatar shows when no image exists
- [ ] Image displays correctly in profile page
- [ ] Success/error messages display properly
- [ ] Page layout responsive on mobile

## Support

For issues or questions:
1. Check error logs: `/Applications/XAMPP/xamppfiles/logs/php_error_log`
2. Check browser console for JavaScript errors
3. Verify all files are in correct locations
4. Ensure database connection is working

## Summary

✅ **Model**: 3 new methods added to M_Users.php
✅ **Controller**: 2 new methods added to Player.php  
✅ **View**: Profile image section added to profile.php
✅ **CSS**: Complete styling in profile.css
✅ **JavaScript**: Upload functionality in profile.js
✅ **Database**: SQL migration script created
✅ **Security**: File validation and authentication implemented
✅ **UX**: Smooth animations and error handling

**Total Files Modified**: 5
**Total Files Created**: 3
**Lines of Code Added**: ~400

The feature is now ready to use! Follow the setup instructions above to complete the implementation.
