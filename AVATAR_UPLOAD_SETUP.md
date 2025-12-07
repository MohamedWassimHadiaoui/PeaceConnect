# 📸 Avatar Upload Feature Setup

## ✅ What's Been Implemented

Your application now supports user-uploaded profile pictures! Here's what was added:

1. **Database Support**
   - Added `avatar` column to store uploaded avatar filename
   - SQL migration file: `model/add_avatar_column.sql`

2. **File Upload System**
   - Secure file upload handling
   - Image validation (type, size)
   - Automatic directory creation
   - Old avatar cleanup

3. **User Interface**
   - Upload form in profile page
   - Image preview before upload
   - Delete avatar option
   - Fallback to default avatars

---

## 🚀 Setup Steps

### Step 1: Update Database

Run this SQL in phpMyAdmin:

```sql
ALTER TABLE `user` 
ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `role`;
```

Or use the file: `model/add_avatar_column.sql`

### Step 2: Create Uploads Directory

The uploads directory will be created automatically when a user uploads their first avatar. However, you can create it manually:

1. Create directory: `uploads/avatars/`
2. Set permissions: `chmod 755 uploads/avatars/` (or `0755` on Windows)
3. The `.htaccess` files are already created for security

### Step 3: Test the Feature

1. **Log in** to your account
2. Go to **Profile** page
3. Click **"Upload Avatar"** button
4. Select an image file (JPEG, PNG, GIF, or WebP)
5. See preview
6. Click **"Upload Avatar"**
7. Your avatar should appear! ✅

---

## 📋 Features

### ✅ Supported Features:
- **File Types:** JPEG, JPG, PNG, GIF, WebP
- **Max Size:** 5MB
- **Image Preview:** See image before uploading
- **Automatic Resize:** Images are stored as-is (you can add resizing later)
- **Delete Option:** Remove uploaded avatar and return to default
- **Security:** File type validation, size limits, secure file naming

### 🔒 Security Features:
- MIME type validation (not just file extension)
- File size limits (5MB max)
- Secure filename generation (prevents conflicts)
- Old file cleanup when uploading new avatar
- `.htaccess` protection for uploads directory

---

## 📁 File Structure

```
uploads/
├── .htaccess (security)
└── avatars/
    ├── .htaccess (security)
    └── avatar_USERID_TIMESTAMP.ext (uploaded files)
```

---

## 🎯 How It Works

### For Users:

1. **Upload Avatar:**
   - Click "Upload Avatar" button
   - Select image file
   - See preview
   - Click "Upload Avatar" to save

2. **Change Avatar:**
   - Click "Change Avatar" (if you have one)
   - Upload new image
   - Old avatar is automatically deleted

3. **Delete Avatar:**
   - Click "Delete Avatar" button
   - Confirm deletion
   - Returns to default avatar

### For Developers:

- **File Storage:** `uploads/avatars/avatar_USERID_TIMESTAMP.ext`
- **Database:** Stores filename in `user.avatar` column
- **Display:** Checks if `avatar` column has value, otherwise uses default
- **Cleanup:** Old files deleted when new avatar uploaded

---

## 🔧 Configuration

### Change Max File Size

Edit `view/frontoffice/profile.php`:

```php
$maxSize = 5 * 1024 * 1024; // Change 5 to your desired MB
```

### Change Allowed File Types

Edit `view/frontoffice/profile.php`:

```php
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
```

### Change Upload Directory

Edit `view/frontoffice/profile.php`:

```php
$uploadDir = __DIR__ . '/../../uploads/avatars/';
```

---

## 🆘 Troubleshooting

### "Failed to upload file"
- Check `uploads/avatars/` directory exists and is writable
- Check PHP `upload_max_filesize` and `post_max_size` in `php.ini`
- Check file permissions (should be 755 or 777)

### "Invalid file type"
- Make sure file is actually an image (not just renamed)
- Check file is one of: JPEG, PNG, GIF, WebP
- Try opening the file in an image viewer first

### "File is too large"
- Reduce image size before uploading
- Or increase `$maxSize` in profile.php
- Or increase PHP `upload_max_filesize` in php.ini

### Avatar Not Displaying
- Check file path is correct: `../uploads/avatars/FILENAME`
- Check file exists in uploads directory
- Check file permissions
- Check browser console for 404 errors

### Permission Errors
On Windows (XAMPP):
- Right-click `uploads` folder → Properties → Security
- Give "Users" full control

On Linux:
```bash
chmod -R 755 uploads/
chown -R www-data:www-data uploads/
```

---

## 📝 Files Modified/Created

### New Files:
- `model/add_avatar_column.sql` - Database migration
- `uploads/.htaccess` - Security for uploads
- `uploads/avatars/.htaccess` - Security for avatars

### Modified Files:
- `model/User.php` - Added avatar property
- `controller/userController.php` - Added avatar methods
- `view/frontoffice/profile.php` - Added upload form and handling
- All files with `new User()` - Updated to include avatar parameter

---

## 🎨 Future Enhancements (Optional)

- **Image Resizing:** Automatically resize large images
- **Image Cropping:** Allow users to crop before upload
- **Multiple Formats:** Convert to WebP for better compression
- **Avatar History:** Keep previous avatars
- **Default Avatar Selection:** Let users choose from default avatars

---

## ✅ Checklist

- [ ] Database column added (`avatar`)
- [ ] Uploads directory created (`uploads/avatars/`)
- [ ] Directory permissions set (755 or 777)
- [ ] Test upload works
- [ ] Test delete works
- [ ] Test preview works
- [ ] Check file security (.htaccess)

---

## 🎉 You're All Set!

Users can now upload their own profile pictures! The system will:
- ✅ Validate file types and sizes
- ✅ Store files securely
- ✅ Display uploaded avatars
- ✅ Clean up old files automatically
- ✅ Fall back to default avatars if none uploaded

Enjoy your new avatar upload feature! 📸

