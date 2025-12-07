# Resend API Setup Instructions

This application uses **Resend** API for sending password reset verification codes and confirmation emails.

## Why Resend?

- ✅ **Free tier**: 3,000 emails/month free
- ✅ **Developer-friendly**: Simple API, great documentation
- ✅ **Fast setup**: Get started in minutes
- ✅ **Reliable**: High deliverability rates
- ✅ **GitHub-friendly**: Popular with developers

## Setup Steps

### 1. Create a Resend Account
- Go to [https://resend.com](https://resend.com)
- Sign up for a free account (3,000 emails/month)
- Verify your email address

### 2. Get Your API Key
1. Log in to your Resend dashboard
2. Go to **API Keys** in the sidebar
3. Click **Create API Key**
4. Name it (e.g., "Peace App")
5. Select **Full Access** or **Sending Access**
6. Click **Create**
7. **Copy the API key immediately** (you won't be able to see it again)

### 3. Verify Your Domain (Required to Send to Any Email)

**⚠️ Important:** Without a verified domain, you can only send emails to the address you used to create your Resend account. To send to any email address, you must verify a domain.

**📖 For detailed instructions on getting a FREE domain and setting it up, see: [FREE_DOMAIN_SETUP.md](./FREE_DOMAIN_SETUP.md)**

**Quick Steps:**
1. Get a free domain (see FREE_DOMAIN_SETUP.md for options)
2. In Resend dashboard, go to **Domains**
3. Click **Add Domain**
4. Add the DNS records Resend provides to your domain
5. Wait for verification (usually 15-30 minutes)
6. Once verified, you can use emails like `noreply@yourdomain.com`

### 4. Configure the Application

You have two options to set the API key:

#### Option A: Environment Variables (Recommended for Production)
Set environment variables on your server:
```bash
export RESEND_API_KEY="re_FVwpvyFX_PmjtLw1TsgsVAjJDvwPykNwZ"
export FROM_EMAIL="noreply@yourdomain.com"
```

For XAMPP on Windows, you can set them in:
- System Environment Variables, or
- Create a `.env` file (if using a library to load it)

#### Option B: Direct Configuration (For Development)
Edit `config.php` and replace the placeholder:
```php
'api_key' => 're_your_actual_api_key_here',
'from_email' => 'noreply@yourdomain.com',
```

**Note**: For testing, you can use Resend's test domain: `onboarding@resend.dev`

### 5. Test the Integration
1. Try the "Forgot Password" feature on the login page
2. Check if the verification email is received
3. Complete the password reset flow
4. Check if the confirmation email is received

## How It Works

1. **User requests password reset** → Enters email on login page
2. **System generates 6-digit code** → Stores in session (15 min expiry)
3. **Email sent via Resend API** → User receives verification code
4. **User enters code** → On reset_password.php page
5. **Code verified** → User can set new password
6. **Password updated** → Confirmation email sent via Resend API
7. **User redirected** → Back to login page

## Troubleshooting

### Emails not sending
- ✅ Check API key is correct (starts with `re_`)
- ✅ Verify API key has proper permissions
- ✅ Check server error logs for API responses
- ✅ Ensure `FROM_EMAIL` is verified in Resend

### 403 Forbidden Error
- Verify your sender email/domain in Resend dashboard
- **Without a verified domain, you can only send to your account email**
- For testing without domain, use `onboarding@resend.dev` (but this only works for your account email)
- **To send to any email:** You must verify a domain (see FREE_DOMAIN_SETUP.md)

### Rate Limits
- Free tier: 3,000 emails/month
- Upgrade if you need more

### Check Logs
- PHP error logs will show API errors
- Resend dashboard shows email delivery status

## Security Best Practices

- ✅ Never commit API keys to version control
- ✅ Use environment variables in production
- ✅ Rotate API keys periodically
- ✅ Monitor email sending activity
- ✅ Use verified domains for better deliverability

## Alternative: Using Resend's Test Mode

For development, you can use Resend's test mode which doesn't actually send emails but returns success responses. This is useful for testing without using your email quota.

## Support

- Resend Documentation: [https://resend.com/docs](https://resend.com/docs)
- Resend API Reference: [https://resend.com/docs/api-reference](https://resend.com/docs/api-reference)

