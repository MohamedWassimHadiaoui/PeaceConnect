# 🔐 Two-Factor Authentication (2FA) Setup Instructions

## ✅ What's Been Implemented

Your application now has full Google Authenticator 2FA support! Here's what was added:

1. **2FA Service Class** (`controller/TwoFactorAuth.php`)
   - Generates TOTP secrets
   - Creates QR codes for setup
   - Verifies authentication codes

2. **Database Support**
   - Added `two_factor_secret` column
   - Added `two_factor_enabled` flag

3. **Setup Page** (`view/frontoffice/setup_2fa.php`)
   - QR code generation
   - Step-by-step setup wizard
   - Enable/disable 2FA

4. **Login Integration**
   - Automatic 2FA check on login
   - Code verification required if enabled

---

## 🚀 Quick Start

### Step 1: Update Database

Run this SQL in phpMyAdmin:

```sql
ALTER TABLE `user` 
ADD COLUMN `two_factor_secret` VARCHAR(32) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `two_factor_secret`;
```

Or use the file: `model/add_2fa_column.sql`

### Step 2: Test the Setup

1. **Log in** to your account
2. Go to **Profile** page
3. Click **"Setup 2FA"** button
4. Follow the on-screen instructions:
   - Install Google Authenticator app on your phone
   - Scan the QR code
   - Enter the verification code
   - Done! ✅

### Step 3: Test Login with 2FA

1. **Log out**
2. **Log in** with your email and password
3. You'll be prompted for the 6-digit code
4. Open Google Authenticator app
5. Enter the code
6. You're logged in! 🎉

---

## 📱 Supported Apps

Users can use any TOTP-compatible authenticator app:

- ✅ **Google Authenticator** (iOS/Android)
- ✅ **Microsoft Authenticator** (iOS/Android)
- ✅ **Authy** (iOS/Android/Desktop)
- ✅ **1Password** (with TOTP support)
- ✅ **LastPass Authenticator**
- ✅ Any other TOTP app

---

## 🎯 How It Works

### For Users:

1. **Setup (One Time):**
   - Go to Profile → Setup 2FA
   - Scan QR code with authenticator app
   - Enter code to verify
   - 2FA is now enabled

2. **Login (Every Time):**
   - Enter email and password
   - Enter 6-digit code from app
   - Access granted!

### For Developers:

- **Secret Generation:** Uses cryptographically secure random bytes
- **TOTP Algorithm:** RFC 6238 compliant
- **Time Window:** 30-second time steps with ±1 step tolerance (accounts for clock skew)
- **Base32 Encoding:** Standard format for authenticator apps
- **QR Code:** Uses Google Charts API (free, no API key)

---

## 🔧 Features

- ✅ **QR Code Generation** - Easy setup
- ✅ **Manual Entry** - If QR code doesn't work
- ✅ **Enable/Disable** - Users can manage their own 2FA
- ✅ **Secure Storage** - Secrets stored in database
- ✅ **Time Tolerance** - Handles clock differences
- ✅ **User-Friendly** - Clear instructions and error messages

---

## 🛡️ Security Notes

1. **Secret Storage:** Secrets are stored in the database. In production, consider encrypting them.

2. **Backup Codes:** Consider adding backup/recovery codes for users who lose their device.

3. **Rate Limiting:** The login page should have rate limiting to prevent brute force attacks.

4. **Session Security:** Pending user IDs are stored in session - ensure sessions are secure.

---

## 📝 Files Created/Modified

### New Files:
- `controller/TwoFactorAuth.php` - 2FA service class
- `view/frontoffice/setup_2fa.php` - Setup page
- `model/add_2fa_column.sql` - Database migration

### Modified Files:
- `controller/userController.php` - Added 2FA methods
- `view/frontoffice/login_client.php` - Added 2FA verification
- `view/frontoffice/profile.php` - Added 2FA button

---

## 🧪 Testing Checklist

- [ ] Database columns added successfully
- [ ] Can access setup page from profile
- [ ] QR code displays correctly
- [ ] Can scan QR code with Google Authenticator
- [ ] Verification code works
- [ ] 2FA enables successfully
- [ ] Login requires 2FA code when enabled
- [ ] Can disable 2FA
- [ ] Login works without 2FA when disabled

---

## 🆘 Troubleshooting

### QR Code Not Displaying
- Check internet connection (uses external API)
- Check browser console for errors
- Try manual entry instead

### Code Not Working
- Make sure device time is synchronized
- Wait for new code (codes change every 30 seconds)
- Check that you're using the correct secret

### Can't Enable 2FA
- Check database columns exist
- Check PHP error logs
- Verify user has proper permissions

### Login Loop
- Clear browser session/cookies
- Check that 2FA verification is working
- Verify database has correct secret stored

---

## 🎉 You're All Set!

Your 2FA system is ready to use. Users can now secure their accounts with Google Authenticator!

**Next Steps (Optional):**
- Add backup codes feature
- Add email notification when 2FA is enabled/disabled
- Add admin panel to view 2FA status
- Add recovery options for lost devices

Enjoy your secure authentication system! 🔐

