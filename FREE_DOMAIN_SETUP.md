# Free Domain Setup Guide for Resend Email

This guide will help you get a **free domain** and verify it with Resend so you can send emails to **any email address**, not just your account email.

## 🎯 Why You Need a Domain

Without a verified domain, Resend can only send emails to the email address you used to create your Resend account. Once you verify a domain, you can send emails to **any email address** using that domain.

---

## 📋 Step 1: Get a Free Domain

### Option A: Freenom (Free .tk, .ml, .ga, .cf domains) ⚠️
**Note:** Freenom has had reliability issues, but it's still free.

1. Go to [https://www.freenom.com](https://www.freenom.com)
2. Create a free account
3. Search for an available domain (e.g., `peaceapp.tk`, `mypeace.ml`)
4. Select a free TLD (.tk, .ml, .ga, .cf)
5. Complete registration (free for 1 year, renewable)

**Pros:** Completely free  
**Cons:** Less reliable, some email providers may block these domains

### Option B: No-IP Free Subdomain (Recommended for Testing) ✅
1. Go to [https://www.noip.com](https://www.noip.com)
2. Create a free account
3. Go to "Dynamic DNS" → "Hostnames"
4. Create a free hostname (e.g., `peaceapp.ddns.net`)
5. This gives you a subdomain you can use

**Pros:** Reliable, free, easy setup  
**Cons:** It's a subdomain, not a full domain

### Option C: Cloudflare + Free Domain Registrar (Best Long-term) ⭐
1. Get a free domain from Freenom or other free registrar
2. Transfer DNS management to Cloudflare (free)
3. Use Cloudflare's free DNS service
4. This gives you professional DNS management for free

**Pros:** Best reliability and features  
**Cons:** Slightly more complex setup

### Option D: GitHub Student Pack (If You're a Student) 🎓
If you're a student, you can get free domains through:
- [GitHub Student Developer Pack](https://education.github.com/pack)
- Includes free domain credits from various providers

---

## 🔧 Step 2: Set Up DNS Records

Once you have a domain, you need to add DNS records to verify it with Resend.

### 2.1 Get DNS Records from Resend

1. Log in to your [Resend Dashboard](https://resend.com)
2. Go to **Domains** in the sidebar
3. Click **Add Domain**
4. Enter your domain (e.g., `peaceapp.tk` or `peaceapp.ddns.net`)
5. Resend will show you the DNS records you need to add

You'll typically need to add these records:

#### **SPF Record (TXT)**
```
Type: TXT
Name: @ (or leave blank, or use your domain)
Value: v=spf1 include:resend.com ~all
TTL: 3600 (or default)
```

#### **DKIM Record (TXT)**
```
Type: TXT
Name: resend._domainkey (or similar, Resend will tell you)
Value: [Resend will provide this - a long string]
TTL: 3600 (or default)
```

#### **DMARC Record (TXT)** - Optional but Recommended
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=none;
TTL: 3600 (or default)
```

### 2.2 Add DNS Records to Your Domain

The process depends on where your domain DNS is managed:

#### **If using Freenom:**
1. Log in to Freenom
2. Go to **Services** → **My Domains**
3. Click **Manage Domain** next to your domain
4. Go to **Management Tools** → **Nameservers**
5. You can either:
   - Use Freenom's nameservers and add records in Freenom
   - OR use Cloudflare's free nameservers (recommended)

#### **If using Cloudflare (Recommended):**
1. Sign up for free account at [cloudflare.com](https://cloudflare.com)
2. Add your domain to Cloudflare
3. Cloudflare will give you nameservers (e.g., `ns1.cloudflare.com`)
4. Update nameservers in your domain registrar
5. In Cloudflare dashboard, go to **DNS** → **Records**
6. Add the TXT records provided by Resend

#### **If using No-IP:**
1. Log in to No-IP
2. Go to **Dynamic DNS** → **Hostnames**
3. Click **Modify** on your hostname
4. Go to **DNS Settings** or use their DNS management
5. Add the TXT records from Resend

---

## ✅ Step 3: Verify Domain in Resend

1. After adding DNS records, go back to Resend dashboard
2. In the **Domains** section, find your domain
3. Click **Verify** or wait for automatic verification
4. DNS propagation can take **5 minutes to 48 hours** (usually 15-30 minutes)
5. Once verified, you'll see a green checkmark ✅

**Tip:** You can check DNS propagation using:
- [https://dnschecker.org](https://dnschecker.org)
- Enter your domain and check if TXT records are visible

---

## 🔄 Step 4: Update Your Application Configuration

Once your domain is verified, update `config.php`:

```php
'from_email' => 'noreply@yourdomain.tk',  // Use your verified domain
```

Or if using a subdomain:
```php
'from_email' => 'noreply@peaceapp.ddns.net',
```

---

## 🧪 Step 5: Test Email Sending

1. Use the test script: `test_email.php`
2. Try sending to different email addresses (not just your account email)
3. Check if emails are received
4. Verify emails aren't going to spam

---

## 🚨 Troubleshooting

### Domain Not Verifying?

1. **Check DNS Propagation:**
   - Use [dnschecker.org](https://dnschecker.org) to see if records are visible globally
   - Wait up to 48 hours for full propagation

2. **Verify Record Format:**
   - Make sure TXT records are exactly as Resend provided
   - No extra spaces or quotes
   - Check TTL values

3. **Check Nameservers:**
   - Make sure your domain is using the correct nameservers
   - If using Cloudflare, ensure nameservers are updated in your registrar

4. **Common Mistakes:**
   - ❌ Adding records in wrong DNS provider
   - ❌ Typos in record values
   - ❌ Using wrong record name (e.g., `@` vs domain name)
   - ❌ Not waiting for DNS propagation

### Emails Going to Spam?

1. **Add DMARC Record** (see Step 2.1)
2. **Use a professional domain** (avoid .tk, .ml if possible)
3. **Warm up your domain** by sending a few test emails first
4. **Check Resend's deliverability dashboard**

### Still Having Issues?

1. Check Resend's domain verification status
2. Review DNS records in your DNS provider
3. Check PHP error logs for detailed error messages
4. Contact Resend support if domain verification fails

---

## 💡 Quick Start: Fastest Free Solution

**For immediate testing, here's the fastest path:**

1. **Get a free subdomain from No-IP** (5 minutes)
   - Sign up at noip.com
   - Create hostname: `peaceapp.ddns.net`

2. **Add DNS records** (if No-IP supports it, or use Cloudflare)
   - Add Resend's TXT records

3. **Verify in Resend** (15-30 minutes)
   - Add domain in Resend
   - Wait for verification

4. **Update config.php**
   - Change `from_email` to your verified domain/subdomain

5. **Test!**
   - Use `test_email.php` to verify it works

---

## 📚 Additional Resources

- **Resend Domain Setup:** [https://resend.com/docs/dashboard/domains/introduction](https://resend.com/docs/dashboard/domains/introduction)
- **DNS Record Types Explained:** [https://www.cloudflare.com/learning/dns/dns-records/](https://www.cloudflare.com/learning/dns/dns-records/)
- **Cloudflare Free DNS:** [https://www.cloudflare.com/dns/](https://www.cloudflare.com/dns/)

---

## 🎉 Success Checklist

- [ ] Free domain/subdomain obtained
- [ ] DNS records added to domain
- [ ] Domain verified in Resend dashboard
- [ ] `config.php` updated with verified domain email
- [ ] Test email sent successfully to any email address
- [ ] Emails not going to spam folder

Once all checked, you're ready to send emails to any address! 🚀

