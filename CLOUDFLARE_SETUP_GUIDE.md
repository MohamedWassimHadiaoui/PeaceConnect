# 🚀 Cloudflare Setup Guide - Add TXT Records for Resend

Since No-IP only allows MX records, we'll use **Cloudflare** (completely free) to manage your DNS and add the TXT records Resend needs.

---

## ✅ Step-by-Step Instructions

### Step 1: Sign Up for Cloudflare (Free) - 2 minutes

1. Go to **[https://www.cloudflare.com](https://www.cloudflare.com)**
2. Click **"Sign Up"** (top right)
3. Enter your email and create a password
4. Verify your email address
5. ✅ You're in!

---

### Step 2: Add Your Domain to Cloudflare - 3 minutes

1. In Cloudflare dashboard, click **"Add a Site"** or **"Add Site"**
2. Enter your domain: `peaceapp.ddns.net`
3. Click **"Add site"**
4. Select the **Free** plan (it's selected by default)
5. Click **"Continue"**

---

### Step 3: Cloudflare Scans Your Domain - 1 minute

1. Cloudflare will automatically scan your domain
2. It will detect your existing A record (`peaceapp` → `41.225.155.142`)
3. You'll see a list of existing DNS records
4. Click **"Continue"**

---

### Step 4: Get Cloudflare Nameservers - 1 minute

1. Cloudflare will show you **2 nameservers**, for example:
   ```
   ns1.cloudflare.com
   ns2.cloudflare.com
   ```
   (Your actual nameservers will be different - copy them exactly!)

2. **IMPORTANT:** Copy these nameservers - you'll need them!

---

### Step 5: Update Nameservers in No-IP - 5 minutes

**Option A: If No-IP Allows Nameserver Changes**

1. Go back to **No-IP dashboard**
2. Go to **"Domains"** → **"Manage Domains"** (or similar)
3. Find your domain `ddns.net` or `peaceapp.ddns.net`
4. Look for **"Nameservers"** or **"DNS Settings"** or **"Nameserver Settings"**
5. Change from No-IP nameservers to Cloudflare's nameservers:
   - Replace with: `ns1.cloudflare.com` (or whatever Cloudflare gave you)
   - Replace with: `ns2.cloudflare.com` (or whatever Cloudflare gave you)
6. Save changes
7. ⏳ Wait 5-10 minutes for nameservers to update

**Option B: If No-IP Doesn't Allow Nameserver Changes**

If No-IP doesn't let you change nameservers, you have two options:

**B1: Use a Different Free Domain (Recommended)**
- Get a free domain from Freenom (`.tk`, `.ml`, `.ga`, `.cf`)
- Point it to Cloudflare
- Add all DNS records there
- Update `config.php` to use the new domain

**B2: Keep No-IP for Dynamic DNS, Use Cloudflare for Email DNS**
- Keep your A record in No-IP (for dynamic IP updates)
- Use Cloudflare for email-related records
- This is more complex - not recommended for beginners

---

### Step 6: Add DNS Records in Cloudflare - 5 minutes

Once nameservers are updated (or if you're using a new domain):

1. Go to **Cloudflare dashboard**
2. Click on your domain `peaceapp.ddns.net`
3. Go to **"DNS"** → **"Records"** (in the left sidebar)
4. You should see your A record already there

5. **Add Record 1: DKIM (TXT)**
   - Click **"Add record"**
   - **Type:** Select `TXT`
   - **Name:** `resend._domainkey.peaceapp` (copy exactly from Resend)
   - **Content:** [Paste the full long key from Resend - starts with `p=MIGfMA0GCSq...`]
   - **TTL:** `Auto`
   - Click **"Save"**

6. **Add Record 2: SPF MX (MX)**
   - Click **"Add record"**
   - **Type:** Select `MX`
   - **Name:** `send.peaceapp` (copy exactly from Resend)
   - **Mail server:** `feedback-smtp.eu-west-1.resend.com` (or as shown in Resend)
   - **Priority:** `10`
   - **TTL:** `Auto`
   - Click **"Save"**

7. **Add Record 3: SPF TXT (TXT)**
   - Click **"Add record"**
   - **Type:** Select `TXT`
   - **Name:** `send.peaceapp` (copy exactly from Resend)
   - **Content:** `v=spf1 include:amazonses.com ~all` (or exactly as shown in Resend)
   - **TTL:** `Auto`
   - Click **"Save"**

8. **Add Record 4: DMARC (TXT) - Optional**
   - Click **"Add record"**
   - **Type:** Select `TXT`
   - **Name:** `_dmarc`
   - **Content:** `v=DMARC1; p=none;`
   - **TTL:** `Auto`
   - Click **"Save"**

9. **Keep Your A Record**
   - Make sure your A record (`peaceapp` → `41.225.155.142`) is still there
   - If it's missing, add it:
     - **Type:** `A`
     - **Name:** `peaceapp`
     - **IPv4 address:** `41.225.155.142`
     - **TTL:** `Auto`
     - Click **"Save"**

---

### Step 7: Verify in Resend - 15-30 minutes

1. **Wait 15-30 minutes** for DNS propagation
2. Go to **Resend dashboard**
3. Find your domain `peaceapp.ddns.net`
4. Click **"I've added the records"** button
5. Resend will verify all records
6. When you see ✅ **green checkmarks** next to all records, you're done!

---

### Step 8: Update Your Code

Edit `config.php`:
```php
'from_email' => 'noreply@peaceapp.ddns.net',
```

---

### Step 9: Test!

1. Run `test_email.php`
2. Send a test email to any email address
3. Check if it arrives! 🎉

---

## 🆘 Troubleshooting

### "Domain already exists" Error in Cloudflare
- This means someone else already added this domain
- Solution: Use a different subdomain or get a new domain

### Nameservers Not Updating
- Wait longer (can take up to 24 hours, but usually 5-10 minutes)
- Double-check you entered them correctly
- Make sure you saved changes in No-IP

### Records Not Verifying in Resend
- Wait longer (DNS propagation can take up to 48 hours)
- Use [dnschecker.org](https://dnschecker.org) to verify records are visible globally
- Double-check record names and content match exactly from Resend
- Make sure nameservers are pointing to Cloudflare

### Can't Change Nameservers in No-IP
- Get a free domain from Freenom instead
- Or contact No-IP support (they might allow it for email purposes)

---

## ✅ Final Checklist

- [ ] Cloudflare account created
- [ ] Domain added to Cloudflare
- [ ] Nameservers copied from Cloudflare
- [ ] Nameservers updated in No-IP (or using new domain)
- [ ] All 4 DNS records added in Cloudflare:
  - [ ] DKIM (TXT): `resend._domainkey.peaceapp`
  - [ ] SPF MX (MX): `send.peaceapp`
  - [ ] SPF TXT (TXT): `send.peaceapp`
  - [ ] DMARC (TXT): `_dmarc`
- [ ] A record still exists in Cloudflare
- [ ] Waited 15-30 minutes
- [ ] Clicked "I've added the records" in Resend
- [ ] All records verified ✅ in Resend
- [ ] Updated `config.php`
- [ ] Tested email sending

---

## 🎯 Quick Summary

1. **Sign up Cloudflare** (free)
2. **Add domain** `peaceapp.ddns.net`
3. **Get nameservers** from Cloudflare
4. **Update nameservers** in No-IP (if possible)
5. **Add DNS records** in Cloudflare
6. **Verify** in Resend
7. **Test!**

**Total time:** 20-40 minutes (mostly waiting for DNS propagation)

Good luck! 🚀

