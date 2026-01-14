# Fixing 405 Error - Method Not Allowed

## What is a 405 Error?

A 405 error means "Method Not Allowed" - the server is rejecting the HTTP method (typically POST) being used to access a PHP file.

## Solutions Applied

### ✅ 1. Updated `.htaccess` File
- Added proper CORS headers support
- Allowed all HTTP methods (GET, POST, PUT, DELETE, OPTIONS)
- Compatible with both Apache 2.2 and 2.4
- Enables preflight OPTIONS requests

### ✅ 2. Added CORS Headers to All API Files
- All files in `/api/` now include:
  - `Access-Control-Allow-Origin: *`
  - `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
  - `Access-Control-Allow-Headers: Content-Type`
  - Proper OPTIONS request handling

### ✅ 3. Fixed Files
The following API files have been updated:
- `api/login.php` ✓
- `api/logout.php` ✓
- `api/auth_check.php` ✓
- `api/save_announcement.php` ✓
- `api/save_simulation.php` ✓
- `api/get_rows.php` ✓
- `api/get_popup.php` ✓

## Testing the Fix

### Step 1: Restart Apache
1. Open XAMPP Control Panel (`C:\xampp\xampp-control.exe`)
2. Stop Apache (if running)
3. Wait 3 seconds
4. Click "Start" to restart Apache
5. Wait for it to show "Running"

### Step 2: Test POST Requests
Go to: `http://localhost/spottracker/api/test_post.php`

You should see:
```json
{
  "method": "POST",
  "success": true,
  "message": "POST request received successfully!",
  ...
}
```

If you see:
```json
{
  "method": "OPTIONS",
  "success": false,
  "message": "This endpoint only accepts POST requests."
}
```
This is normal - browsers send OPTIONS preflight before POST.

### Step 3: Test Login
1. Go to `http://localhost/spottracker/login.html`
2. Enter:
   - Username: `admin`
   - Password: `admin123`
3. Click Login
4. Should redirect to Control Panel without 405 error

## If Still Getting 405 Error

### Check Apache Modules
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Search for: `mod_rewrite.c`
3. Should be: `LoadModule rewrite_module modules/mod_rewrite.so` (without `#`)
4. Also check for `mod_headers.c` is uncommented

### Check Apache Error Log
1. Open `C:\xampp\apache\logs\error.log`
2. Look for recent errors related to the login attempt
3. Check for permission issues or configuration problems

### Try Alternative Paths
If the issue persists, the installation path might be causing issues:

**Option 1: Use Different Port**
1. Edit `C:\xampp\apache\conf\httpd.conf`
2. Change `Listen 80` to `Listen 8080`
3. Restart Apache
4. Access at `http://localhost:8080/spottracker/login.html`

**Option 2: Rename Application**
1. Move folder from `C:\xampp\htdocs\spottracker` to `C:\xampp\htdocs\st`
2. Access at `http://localhost/st/login.html`
3. Update `RewriteBase` in `.htaccess` from `/spottracker/` to `/st/`

### Check File Permissions
Windows may require permission adjustments:
1. Right-click `C:\xampp\htdocs\spottracker`
2. Properties → Security → Edit
3. Select your user or "Everyone"
4. Check "Modify" and "Write" permissions
5. Click Apply

### Check PHP Configuration
1. Go to `http://localhost/phpinfo.php` (or create it)
2. Look for `allow_url_fopen` - should be "On"
3. Look for `post_max_size` and `upload_max_filesize` - should be reasonable

## Browser Developer Tools

Open Developer Tools (F12) and check:

### Console Tab
- Look for JavaScript errors
- Check if fetch request shows 405 error

### Network Tab
1. Open Network tab
2. Attempt login
3. Click on `login.php` request
4. Check:
   - **Method**: Should be `POST`
   - **Status**: Should be `200` or `401` (not 405)
   - **Response**: Should be valid JSON

### Example Correct Response:
```json
{
  "success": false,
  "message": "User not found."
}
```
(Message content may vary, but status should be 200)

## Advanced Diagnostics

Create `C:\xampp\htdocs\spottracker\check_methods.php`:

```php
<?php
echo "Allowed Methods:<br>";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "PHP: " . phpversion() . "<br>";

// Check if module is loaded
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? 'YES' : 'NO') . "<br>";
    echo "mod_headers: " . (in_array('mod_headers', $modules) ? 'YES' : 'NO') . "<br>";
}
?>
```

Access at: `http://localhost/spottracker/check_methods.php`

## Additional Resources

- XAMPP Troubleshooting: https://www.apachefriends.org/faq.html
- Apache Docs: https://httpd.apache.org/docs/2.4/
- Check XAMPP log files in `C:\xampp\apache\logs\`

---

**If the problem persists after these steps, check the Apache error log and share the error message for more specific help.**
