# SpotTracker Setup Guide for XAMPP on Windows Server

## Prerequisites
- XAMPP installed on Windows Server (https://www.apachefriends.org)
- Administrator access to the server

## Step 1: Start XAMPP Services

1. Open **XAMPP Control Panel** (usually in `C:\xampp\xampp-control.exe`)
2. Start the following services:
   - **Apache** (Click "Start" next to Apache)
   - **MySQL** (Click "Start" next to MySQL)
3. Wait for both services to show "Running" status

## Step 2: Place Project Files in XAMPP

1. Copy all SpotTracker files to the XAMPP web root:
   ```
   C:\xampp\htdocs\spottracker\
   ```

2. Folder structure should look like:
   ```
   C:\xampp\htdocs\spottracker\
   ├── api/
   │   ├── auth_check.php
   │   ├── get_popup.php
   │   ├── get_rows.php
   │   ├── login.php
   │   ├── logout.php
   │   ├── save_announcement.php
   │   └── save_simulation.php
   ├── config/
   │   └── db.php
   ├── sql/
   │   └── spottracker.sql
   ├── pics/
   ├── bootstrap.css
   ├── controlpanel.html
   ├── extra.js
   ├── index.html
   ├── login.html
   ├── script.js
   ├── styles.css
   └── SETUP_XAMPP.md
   ```

## Step 3: Create the Database

### Method 1: Using phpMyAdmin (Recommended)

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"Import"** tab at the top
3. Click **"Choose File"** and select `sql/spottracker.sql`
4. Click **"Import"** button at the bottom
5. Wait for the message: "Import has been successfully finished"

### Method 2: Using MySQL Command Line

1. Open Command Prompt (cmd) as Administrator
2. Navigate to XAMPP MySQL directory:
   ```
   cd C:\xampp\mysql\bin
   ```

3. Run the SQL script:
   ```
   mysql -u root < "C:\xampp\htdocs\spottracker\sql\spottracker.sql"
   ```

4. Verify database creation:
   ```
   mysql -u root
   SHOW DATABASES;
   USE spottracker;
   SHOW TABLES;
   EXIT;
   ```

## Step 4: Verify Database Installation

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. In the left sidebar, you should see **spottracker** database
3. Expand it to verify these tables exist:
   - `users` (should contain admin user)
   - `parking_rows` (should contain rows A-Z)
   - `announcements`
   - `simulation_settings`
   - `parking_history`

## Step 5: Access the Application

1. Open your browser and go to:
   ```
   http://localhost/spottracker/login.html
   ```

2. Login with default credentials:
   - **Username:** admin
   - **Password:** admin123

3. You should be redirected to the Control Panel

## Step 6: Verify Functionality

### Login Page
- ✓ Form should load properly
- ✓ Login with admin/admin123 should work
- ✓ Invalid credentials should show error message

### Control Panel
- ✓ Should load only after successful login
- ✓ Should display current user in Database Info section
- ✓ Parking rows table should show data from database
- ✓ Announcement section should save and load from database
- ✓ Simulation settings should save to database
- ✓ Should update every 5 seconds with fresh data

### Dashboard
- ✓ Should display parking lot overview with real data
- ✓ Should show correct row counts and utilization percentages

## Troubleshooting

### "Database connection failed"
**Solution:**
1. Verify MySQL is running in XAMPP Control Panel
2. Check that database name in `config/db.php` matches created database
3. Default credentials are: user `root`, password empty
4. Use IP `127.0.0.1` instead of `localhost` if connection issues persist

### "Table doesn't exist"
**Solution:**
1. Re-import the SQL file using phpMyAdmin
2. Ensure the import completed successfully
3. Check in phpMyAdmin that all tables exist

### "Login page appears but won't submit"
**Solution:**
1. Check browser console for errors (F12 → Console tab)
2. Verify Apache is running
3. Check that `api/login.php` file exists in correct location
4. Check file permissions on php files

### "Control Panel won't load after login"
**Solution:**
1. Verify session folder permissions (usually `C:\xampp\tmp`)
2. Check that all API files are in `api/` folder
3. Clear browser cache and cookies
4. Try accessing in a private/incognito window

### Port Already in Use
If Apache or MySQL ports are in use:
1. Open XAMPP Control Panel
2. Click "Config" next to Apache → Edit `httpd.conf`
3. Search for `Listen` and change port (e.g., 8080)
4. Restart Apache
5. Access application at `http://localhost:8080/spottracker/login.html`

## Security Notes (For Production)

Before deploying to production:
1. Change default admin password
2. Create additional user accounts as needed
3. Configure strong database password in `config/db.php`
4. Use HTTPS/SSL
5. Add proper input validation and sanitization
6. Enable proper error logging without exposing errors to users
7. Set restrictive file permissions
8. Backup the database regularly

## Database Backup

To backup the database:
```
cd C:\xampp\mysql\bin
mysqldump -u root spottracker > "C:\backup\spottracker_backup.sql"
```

To restore from backup:
```
mysql -u root spottracker < "C:\backup\spottracker_backup.sql"
```

## Support

For XAMPP issues:
- XAMPP Documentation: https://www.apachefriends.org/documentation.html
- Check XAMPP logs in `C:\xampp\apache\logs\` and `C:\xampp\mysql\data\`

---

**Setup completed!** Your SpotTracker application is now running on Windows Server with XAMPP.
