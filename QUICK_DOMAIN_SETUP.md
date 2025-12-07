# ⚡ Quick Domain Setup - 5 Minute Guide

## The Problem
Without a verified domain, Resend can only send emails to **your account email**. You need a domain to send to **any email address**.

## ⚡ Fastest Solution: No-IP Free Subdomain

### Step 1: Get Free Subdomain (2 minutes)
1. Go to [https://www.noip.com](https://www.noip.com)
2. Click **Sign Up** (free)
3. Create account
4. Go to **Dynamic DNS** → **Hostnames**
5. Click **Create Hostname**
6. Choose a name: `peaceapp` (or your choice)
7. Select domain: `.ddns.net` (free)
8. Click **Create Hostname**
9. ✅ You now have: `peaceapp.ddns.net`

### Step 2: Add Domain to Resend (1 minute)
1. Go to [https://resend.com/dashboard](https://resend.com/dashboard)
2. Click **Domains** → **Add Domain**
3. Enter: `peaceapp.ddns.net`
4. Click **Add Domain**
5. Resend will show you DNS records to add

### Step 3: Add DNS Records (2 minutes)

**Option A: If No-IP supports DNS management:**
1. In No-IP, go to your hostname settings
2. Look for DNS/DNS Records section
3. Add the TXT records Resend provided

**Option B: Use Cloudflare (Recommended - More Reliable):**
1. Sign up free at [cloudflare.com](https://cloudflare.com)
2. Add your domain `peaceapp.ddns.net`
3. Cloudflare gives you nameservers
4. Update nameservers in No-IP (if possible) OR
5. Add DNS records in Cloudflare dashboard

**The DNS records you need:**
- **SPF Record (TXT):** `v=spf1 include:resend.com ~all`
- **DKIM Record (TXT):** [Resend provides this]
- **DMARC Record (TXT):** `v=DMARC1; p=none;` (optional)

### Step 4: Wait & Verify (15-30 minutes)
1. Wait for DNS propagation (15-30 minutes)
2. Check status in Resend dashboard
3. When verified ✅, you're done!

### Step 5: Update Your Code
Edit `config.php`:
```php
'from_email' => 'noreply@peaceapp.ddns.net',
```

### Step 6: Test!
Run `test_email.php` and send to any email address! 🎉

---

## 🆓 Alternative: Freenom Free Domain

If you want a "real" domain (not subdomain):

1. Go to [https://www.freenom.com](https://www.freenom.com)
2. Search for available domain (e.g., `peaceapp`)
3. Select free TLD: `.tk`, `.ml`, `.ga`, or `.cf`
4. Register (free for 1 year)
5. Follow Steps 2-6 above

**Note:** Freenom domains are less reliable but completely free.

---

## ❓ Need More Help?

See [FREE_DOMAIN_SETUP.md](./FREE_DOMAIN_SETUP.md) for detailed instructions and troubleshooting.

---

## ✅ Checklist

- [ ] Free subdomain/domain obtained
- [ ] Domain added to Resend
- [ ] DNS records added
- [ ] Domain verified in Resend (green checkmark)
- [ ] `config.php` updated
- [ ] Test email sent successfully

**Time needed:** 5-30 minutes (mostly waiting for DNS)

