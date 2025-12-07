# ✅ DNS Records Checklist for Resend

## Current Status

Looking at your No-IP dashboard, you have:
- ✅ **A Record:** `peaceapp` → `41.225.155.142` (Good!)
- ⚠️ **MX Record:** `peaceapp` → `feedback-smtp.eu-west-1.amazonses.com` (Wrong - needs to be fixed)

## ❌ Missing Records (Required by Resend)

You need to add these records from your Resend dashboard:

### 1. **DKIM Record (TXT) - REQUIRED** ❌
- **Type:** `TXT`
- **Name:** `resend._domainkey.peaceapp` (or exactly as shown in Resend)
- **Content:** [The long key from Resend - starts with `p=MIGfMA0GCSq...`]
- **TTL:** `60` (or Auto)
- **Status:** ❌ **MISSING**

### 2. **SPF MX Record (MX) - REQUIRED** ⚠️
- **Type:** `MX`
- **Name:** `send.peaceapp` (NOT just `peaceapp`)
- **Content:** `feedback-smtp.eu-west-1.resend.com` (NOT amazonses.com)
- **Priority:** `10`
- **TTL:** `60` (or Auto)
- **Status:** ⚠️ **WRONG** - You have it for `peaceapp`, but it should be for `send.peaceapp` and point to `resend.com`

### 3. **SPF TXT Record (TXT) - REQUIRED** ❌
- **Type:** `TXT`
- **Name:** `send.peaceapp`
- **Content:** `v=spf1 include:amazonses.com ~all` (or exactly as shown in Resend)
- **TTL:** `60` (or Auto)
- **Status:** ❌ **MISSING**

### 4. **DMARC Record (TXT) - OPTIONAL but Recommended** ❌
- **Type:** `TXT`
- **Name:** `_dmarc`
- **Content:** `v=DMARC1; p=none;`
- **TTL:** `60` (or Auto)
- **Status:** ❌ **MISSING**

---

## 🔧 What You Need to Do

### Step 1: Fix/Update the MX Record
1. In No-IP, find your current MX record for `peaceapp`
2. Either:
   - **Delete it** (if you can't edit it)
   - **Edit it** to change:
     - Name: `send.peaceapp` (instead of `peaceapp`)
     - Content: `feedback-smtp.eu-west-1.resend.com` (instead of amazonses.com)
     - Priority: `10`

### Step 2: Add the Missing TXT Records
In No-IP DNS Records section, click to add new records:

1. **Add DKIM (TXT):**
   - Click "Add Record" or similar button
   - Type: `TXT`
   - Name: `resend._domainkey.peaceapp` (copy exactly from Resend)
   - Content: [Copy the full long key from Resend]
   - TTL: `60`
   - Save

2. **Add SPF TXT (TXT):**
   - Click "Add Record"
   - Type: `TXT`
   - Name: `send.peaceapp`
   - Content: `v=spf1 include:amazonses.com ~all` (or as shown in Resend)
   - TTL: `60`
   - Save

3. **Add DMARC (TXT) - Optional:**
   - Click "Add Record"
   - Type: `TXT`
   - Name: `_dmarc`
   - Content: `v=DMARC1; p=none;`
   - TTL: `60`
   - Save

---

## ⚠️ Important Notes

### If No-IP Doesn't Allow Adding TXT Records:
No-IP's free Dynamic DNS service **may not support** adding custom TXT records. If you can't add them:

**Solution: Use Cloudflare (Free)**
1. Sign up at [cloudflare.com](https://cloudflare.com) (free)
2. Add your domain `peaceapp.ddns.net`
3. Get Cloudflare nameservers
4. Update nameservers in No-IP (if possible)
5. Add all DNS records in Cloudflare instead

### Verify Record Names Match Exactly
- The names must match **exactly** as shown in Resend dashboard
- Case-sensitive: `send.peaceapp` not `Send.peaceapp`
- Include the full subdomain: `resend._domainkey.peaceapp` not just `resend._domainkey`

---

## ✅ Final Checklist

After adding records, you should have:

- [x] A Record: `peaceapp` → `41.225.155.142` ✅
- [ ] MX Record: `send.peaceapp` → `feedback-smtp.eu-west-1.resend.com` (Priority: 10)
- [ ] TXT Record: `resend._domainkey.peaceapp` → [DKIM key from Resend]
- [ ] TXT Record: `send.peaceapp` → `v=spf1 include:amazonses.com ~all`
- [ ] TXT Record: `_dmarc` → `v=DMARC1; p=none;` (optional)

---

## 🧪 After Adding Records

1. **Wait 15-30 minutes** for DNS propagation
2. Go to **Resend dashboard**
3. Click **"I've added the records"** button
4. Resend will verify the records
5. When you see ✅ green checkmark, you're done!

---

## 🆘 If You Can't Add Records in No-IP

If No-IP's free plan doesn't allow adding TXT records, you have two options:

### Option A: Use Cloudflare (Recommended)
- Free DNS management
- Supports all record types
- More reliable

### Option B: Get a Different Free Domain
- Use Freenom to get a free `.tk`, `.ml`, `.ga`, or `.cf` domain
- Point it to Cloudflare
- Add records there

---

**Current Status:** You're about 20% done. You need to add 3-4 more records! 🚀

