# SpotTracker - Quick Start Guide (XAMPP on Windows Server)

## 📋 Quick Setup Checklist

### 1. **Install XAMPP**
   - [ ] Download XAMPP from https://www.apachefriends.org
   - [ ] Run installer with Administrator privileges
   - [ ] Install to default location: `C:\xampp`

### 2. **Start Services**
   - [ ] Open `C:\xampp\xampp-control.exe`
   - [ ] Start **Apache** (should show "Running")
   - [ ] Start **MySQL** (should show "Running")

### 3. **Deploy Project Files**
   - [ ] Copy entire `spottracker` folder to `C:\xampp\htdocs\`
   - [ ] Full path should be: `C:\xampp\htdocs\spottracker\`

### 4. **Create Database**
   - [ ] Open `http://localhost/phpmyadmin` in browser
   - [ ] Click **"Import"**
   - [ ] Select `sql/spottracker.sql` file
   - [ ] Click **"Import"** button
   - [ ] See success message "Import has been successfully finished"

### 5. **Verify Installation**
   - [ ] Open `http://localhost/spottracker/diagnose.php`
   - [ ] All items should show ✓ (green checkmarks)
   - [ ] If any errors, see **Troubleshooting** section below

### 6. **Access Application**
   - [ ] Open `http://localhost/spottracker/login.html`
   - [ ] Login with: **admin** / **admin123**
   - [ ] Should redirect to Control Panel

---

## 🔗 Important URLs

| URL | Purpose |
|-----|---------|
| `http://localhost/spottracker/login.html` | Login page |
| `http://localhost/spottracker/controlpanel.html` | Control panel (after login) |
| `http://localhost/spottracker/index.html` | Dashboard |
| `http://localhost/phpmyadmin` | Database management |
| `http://localhost/spottracker/diagnose.php` | System check |

---

## 📁 Project Structure in XAMPP

```
C:\xampp\htdocs\spottracker\
├── api/                          (PHP backend)
│   ├── login.php                (Authentication)
│   ├── auth_check.php           (Session check)
│   ├── logout.php               (Logout handler)
│   ├── get_rows.php             (Fetch parking data)
│   ├── get_popup.php            (Fetch announcements)
│   ├── save_announcement.php    (Save announcement)
│   └── save_simulation.php      (Save settings)
├── config/
│   └── db.php                   (Database configuration)
├── sql/
│   └── spottracker.sql          (Database schema)
├── pics/                        (Images folder)
├── login.html                   (Login page)
├── controlpanel.html            (Control panel page)
├── index.html                   (Dashboard)
├── script.js                    (Frontend JS)
├── styles.css                   (Styling)
├── extra.js                     (Extra functionality)
├── .htaccess                    (Apache config)
├── diagnose.php                 (System diagnostic)
├── SETUP_XAMPP.md               (Full setup guide)
└── README.md                    (This file)
```

---

## 🐛 Troubleshooting

### ❌ "Cannot connect to database"

**Check:**
1. MySQL service is running in XAMPP Control Panel
2. Database name is `spottracker` (see in phpMyAdmin)
3. Database credentials in `config/db.php`:
   - Host: `127.0.0.1`
   - User: `root`
   - Password: (empty)

**Fix:**
```
1. Open C:\xampp\xampp-control.exe
2. Click "Start" next to MySQL
3. Wait 10 seconds for it to fully start
4. Reload the page
```

### ❌ "Table 'spottracker.users' doesn't exist"

**Check:**
1. Database was imported successfully
2. All tables exist in phpMyAdmin

**Fix:**
```
1. Go to http://localhost/phpmyadmin
2. Click "Import" tab
3. Select sql/spottracker.sql
4. Click "Import"
5. You should see all tables in left sidebar
```

### ❌ "Login page appears but button doesn't work"

**Check:**
1. API files exist in `C:\xampp\htdocs\spottracker\api\`
2. `login.php` file exists
3. Check browser console (F12) for error messages

**Fix:**
```
1. Verify files are in correct location
2. Check that Apache is running
3. Clear browser cache (Ctrl+Shift+Delete)
4. Try in incognito/private window
```

### ❌ "Control Panel shows but no data loads"

**Check:**
1. Logged in as admin user
2. Parking rows table exists in database
3. API files have correct paths

**Fix:**
```
1. Verify database connection: http://localhost/spottracker/diagnose.php
2. Check that parking_rows table has data
3. Clear browser cache and reload
```

### ❌ "Permission denied" errors

**Windows specific fix:**
1. Right-click `C:\xampp\htdocs\spottracker` folder
2. Properties → Security
3. Add "Everyone" with "Full Control"
4. Apply and restart Apache

---

## 🔐 Security Notes

Before putting this in production:

1. **Change admin password:**
   ```php
   // In phpmyadmin or via script:
   UPDATE users SET password = PASSWORD('newpassword') WHERE username = 'admin';
   ```

2. **Change database credentials** in `config/db.php`

3. **Enable HTTPS** on your web server

4. **Backup database regularly:**
   ```
   C:\xampp\mysql\bin\mysqldump -u root spottracker > backup.sql
   ```

5. **Set strong MySQL password** in XAMPP

---

## 📞 Support Resources

- **XAMPP Docs:** https://www.apachefriends.org/documentation.html
- **phpMyAdmin:** Built-in at `http://localhost/phpmyadmin`
- **MySQL Logs:** `C:\xampp\mysql\data\`
- **Apache Logs:** `C:\xampp\apache\logs\`
- **PHP Error Log:** Check XAMPP Control Panel → Apache → Error Log

---

## ✅ Everything Working?

If you see ✓ on all items when running `diagnose.php`, your system is ready!

1. **Login:** `http://localhost/spottracker/login.html`
2. **Username:** `admin`
3. **Password:** `admin123`
4. **Dashboard:** `http://localhost/spottracker/index.html`

---

**Version:** 1.0  
**Last Updated:** January 14, 2026  
**Tested on:** XAMPP 8.x on Windows Server 2019+
