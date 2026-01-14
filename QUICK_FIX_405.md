# Quick Fix for 405 Error

## ⚡ 1-Minute Fix

### Step 1: Restart Apache
1. Open `C:\xampp\xampp-control.exe`
2. Click "Stop" next to Apache
3. Wait 3 seconds
4. Click "Start" next to Apache
5. Wait for it to show "Running"

### Step 2: Test the Fix
Go to: `http://localhost/spottracker/login.html`

Try logging in with:
- Username: **admin**
- Password: **admin123**

✓ **Success** = You're redirected to Control Panel (no error)
✗ **Still 405 error?** → Continue to Step 3

### Step 3: Run Diagnostics
Go to: `http://localhost/spottracker/test_methods.php`

Check:
- ✓ All Apache modules show green checkmarks
- ✓ POST test button works
- ✓ Fetch test returns 200 status

If any show ✗, see **Advanced Troubleshooting** below.

---

## 🔍 What Was Fixed

| Item | Status |
|------|--------|
| `.htaccess` | ✓ Updated - Now allows POST requests |
| API Headers | ✓ Added CORS headers to all PHP files |
| OPTIONS Support | ✓ Added preflight request handling |
| Error Messages | ✓ Enhanced login page error handling |
| Testing Tools | ✓ Added diagnostic scripts |

---

## 🛠️ Advanced Troubleshooting

### If test_methods.php shows ✗ on modules:

**Check Apache config:**
1. Edit: `C:\xampp\apache\conf\httpd.conf`
2. Search for these lines (should NOT have `#` at start):
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   LoadModule headers_module modules/mod_headers.so
   ```
3. If they have `#`, remove it
4. Save and restart Apache

### If test_methods.php POST test fails:

**The server might be running FastCGI instead of mod_php:**
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Search for `fcgid_module`
3. If found, disable it by adding `#` at the start:
   ```
   #LoadModule fcgid_module modules/mod_fcgid.so
   ```
4. Save and restart Apache

### If login still shows 405:

**Check browser console:**
1. Press **F12** on login page
2. Click **Console** tab
3. Try to login
4. Look for the error message in red
5. Share this message if you need help

---

## 📝 Files That Were Updated

✓ `.htaccess` - Apache permissions  
✓ `api/login.php` - Added headers  
✓ `api/logout.php` - Added headers  
✓ `api/auth_check.php` - Added headers  
✓ `api/save_announcement.php` - Added headers  
✓ `api/save_simulation.php` - Added headers  
✓ `api/get_rows.php` - Added headers  
✓ `api/get_popup.php` - Added headers  
✓ `login.html` - Better error messages  

## 📁 New Testing Tools

| Tool | URL | Purpose |
|------|-----|---------|
| test_methods.php | `/spottracker/test_methods.php` | Complete Apache diagnostics |
| test_post.php | `/spottracker/api/test_post.php` | Simple POST test |

---

## ❓ Still Not Working?

1. Did you restart Apache? (Most common issue)
2. Check: `http://localhost/spottracker/test_methods.php`
3. Open browser console: **F12 → Console**
4. Check Apache error log: `C:\xampp\apache\logs\error.log`

If still stuck, share the error from browser console or Apache log for specific help.

---

**Last Updated:** January 14, 2026
