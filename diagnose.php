<?php
/**
 * SpotTracker System Diagnostic
 * Run this at: http://localhost/spottracker/diagnose.php
 */

$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php' => [
        'version' => phpversion(),
        'sapi' => php_sapi_name(),
    ],
    'extensions' => [],
    'database' => [],
    'files' => [],
    'folders' => [],
];

// Check PHP Extensions
$required_extensions = ['mysqli', 'pdo', 'json', 'session'];
foreach ($required_extensions as $ext) {
    $diagnostics['extensions'][$ext] = extension_loaded($ext) ? '✓ Loaded' : '✗ Missing';
}

// Check Database Connection
$diagnostics['database']['host'] = DB_HOST;
$diagnostics['database']['port'] = DB_PORT ?? 'Not defined';
$diagnostics['database']['database'] = DB_NAME;
$diagnostics['database']['user'] = DB_USER;

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    $diagnostics['database']['status'] = '✗ Connection Failed: ' . $conn->connect_error;
    $diagnostics['database']['connection'] = false;
} else {
    $diagnostics['database']['status'] = '✓ Connected';
    $diagnostics['database']['connection'] = true;
    
    // Check tables
    $tables = ['users', 'parking_rows', 'announcements', 'simulation_settings', 'parking_history'];
    $result = $conn->query("SHOW TABLES");
    $existing_tables = [];
    
    while ($row = $result->fetch_array()) {
        $existing_tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $diagnostics['database']['tables'][$table] = in_array($table, $existing_tables) ? '✓ Exists' : '✗ Missing';
    }
    
    // Check admin user
    $admin_result = $conn->query("SELECT id, username FROM users WHERE username = 'admin'");
    if ($admin_result->num_rows > 0) {
        $diagnostics['database']['admin_user'] = '✓ Found';
    } else {
        $diagnostics['database']['admin_user'] = '✗ Not Found';
    }
    
    $conn->close();
}

// Check Required Files
$required_files = [
    'config/db.php',
    'api/login.php',
    'api/auth_check.php',
    'api/logout.php',
    'api/get_rows.php',
    'api/get_popup.php',
    'api/save_announcement.php',
    'api/save_simulation.php',
    'login.html',
    'controlpanel.html',
    'index.html',
    'script.js',
    'styles.css',
    'sql/spottracker.sql'
];

$base_path = __DIR__;
foreach ($required_files as $file) {
    $full_path = $base_path . '/' . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $exists = file_exists($full_path);
    $diagnostics['files'][$file] = $exists ? '✓ Found' : '✗ Missing';
}

// Check Folders
$required_folders = ['api', 'config', 'sql', 'pics'];
foreach ($required_folders as $folder) {
    $full_path = $base_path . DIRECTORY_SEPARATOR . $folder;
    $exists = is_dir($full_path);
    $diagnostics['folders'][$folder] = $exists ? '✓ Exists' : '✗ Missing';
}

// Check PHP Configuration
$diagnostics['php_config'] = [
    'error_reporting' => ini_get('error_reporting'),
    'display_errors' => ini_get('display_errors') ? 'On' : 'Off',
    'session.save_path' => ini_get('session.save_path'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
];

// Check Permissions (Windows)
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $diagnostics['os'] = 'Windows';
    $diagnostics['file_permissions'] = [];
    
    foreach (['config/db.php', 'api/login.php'] as $file) {
        $full_path = $base_path . '/' . str_replace('/', DIRECTORY_SEPARATOR, $file);
        if (file_exists($full_path)) {
            $is_readable = is_readable($full_path) ? '✓ Readable' : '✗ Not Readable';
            $is_writable = is_writable($full_path) ? '✓ Writable' : '✗ Not Writable';
            $diagnostics['file_permissions'][$file] = "$is_readable, $is_writable";
        }
    }
} else {
    $diagnostics['os'] = 'Linux/Unix';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpotTracker Diagnostic</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #0f172a;
            color: #e5e7eb;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 8px;
            padding: 20px;
        }
        h1 {
            color: #60a5fa;
            margin-top: 0;
        }
        h2 {
            color: #34d399;
            margin-top: 30px;
            border-bottom: 1px solid #1f2937;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
        }
        .item {
            padding: 8px 0;
            border-bottom: 1px solid #1f2937;
        }
        .label {
            color: #9ca3af;
            display: inline-block;
            width: 250px;
        }
        .value {
            color: #60a5fa;
        }
        .ok {
            color: #34d399;
        }
        .error {
            color: #f87171;
        }
        .warning {
            color: #f59e0b;
        }
        code {
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 3px;
        }
        .timestamp {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SpotTracker System Diagnostic</h1>
        
        <h2>PHP Information</h2>
        <div class="section">
            <div class="item">
                <span class="label">PHP Version:</span>
                <span class="value"><?php echo $diagnostics['php']['version']; ?></span>
            </div>
            <div class="item">
                <span class="label">SAPI:</span>
                <span class="value"><?php echo $diagnostics['php']['sapi']; ?></span>
            </div>
            <div class="item">
                <span class="label">OS:</span>
                <span class="value"><?php echo $diagnostics['os']; ?></span>
            </div>
        </div>

        <h2>Required PHP Extensions</h2>
        <div class="section">
            <?php foreach ($diagnostics['extensions'] as $ext => $status): ?>
            <div class="item">
                <span class="label"><?php echo ucfirst($ext); ?>:</span>
                <span class="<?php echo strpos($status, '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $status; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>Database Connection</h2>
        <div class="section">
            <div class="item">
                <span class="label">Host:</span>
                <span class="value"><?php echo $diagnostics['database']['host']; ?></span>
            </div>
            <div class="item">
                <span class="label">Port:</span>
                <span class="value"><?php echo $diagnostics['database']['port']; ?></span>
            </div>
            <div class="item">
                <span class="label">Database:</span>
                <span class="value"><?php echo $diagnostics['database']['database']; ?></span>
            </div>
            <div class="item">
                <span class="label">User:</span>
                <span class="value"><?php echo $diagnostics['database']['user']; ?></span>
            </div>
            <div class="item">
                <span class="label">Status:</span>
                <span class="<?php echo strpos($diagnostics['database']['status'], '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $diagnostics['database']['status']; ?></span>
            </div>
        </div>

        <?php if ($diagnostics['database']['connection']): ?>
        <h2>Database Tables</h2>
        <div class="section">
            <?php foreach ($diagnostics['database']['tables'] as $table => $status): ?>
            <div class="item">
                <span class="label"><?php echo ucfirst($table); ?>:</span>
                <span class="<?php echo strpos($status, '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $status; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>Database Content</h2>
        <div class="section">
            <div class="item">
                <span class="label">Admin User:</span>
                <span class="<?php echo strpos($diagnostics['database']['admin_user'], '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $diagnostics['database']['admin_user']; ?></span>
            </div>
        </div>
        <?php endif; ?>

        <h2>Required Files</h2>
        <div class="section">
            <?php foreach ($diagnostics['files'] as $file => $status): ?>
            <div class="item">
                <span class="label"><?php echo $file; ?>:</span>
                <span class="<?php echo strpos($status, '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $status; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>Directories</h2>
        <div class="section">
            <?php foreach ($diagnostics['folders'] as $folder => $status): ?>
            <div class="item">
                <span class="label"><?php echo $folder . '/'; ?>:</span>
                <span class="<?php echo strpos($status, '✓') !== false ? 'ok' : 'error'; ?>"><?php echo $status; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>PHP Configuration</h2>
        <div class="section">
            <?php foreach ($diagnostics['php_config'] as $key => $value): ?>
            <div class="item">
                <span class="label"><?php echo str_replace('_', ' ', ucfirst($key)); ?>:</span>
                <span class="value"><?php echo $value; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($diagnostics['file_permissions'])): ?>
        <h2>File Permissions</h2>
        <div class="section">
            <?php foreach ($diagnostics['file_permissions'] as $file => $status): ?>
            <div class="item">
                <span class="label"><?php echo $file; ?>:</span>
                <span class="value"><?php echo $status; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="timestamp">Generated: <?php echo $diagnostics['timestamp']; ?></div>
    </div>
</body>
</html>
