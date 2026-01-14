<?php
/**
 * COMPREHENSIVE 405 DIAGNOSTIC
 * This script will identify exactly why 405 errors are occurring
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>405 Error Diagnostic</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #2a2a2a;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        h1 {
            color: #ff6b6b;
            margin-top: 0;
            border-bottom: 2px solid #ff6b6b;
            padding-bottom: 15px;
        }
        h2 {
            color: #4ecdc4;
            margin-top: 30px;
        }
        .test-result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }
        .pass {
            border-left-color: #51cf66;
            background: #1b5e20;
        }
        .fail {
            border-left-color: #ff6b6b;
            background: #b71c1c;
        }
        .warn {
            border-left-color: #ffa726;
            background: #e65100;
        }
        .info {
            border-left-color: #42a5f5;
            background: #0d47a1;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            background: #4ecdc4;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #45b7aa;
        }
        button.danger {
            background: #ff6b6b;
        }
        button.danger:hover {
            background: #ff5252;
        }
        .code {
            background: #1a1a1a;
            padding: 10px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            margin: 5px 0;
            overflow-x: auto;
        }
        .log-output {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
            margin: 10px 0;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔴 405 Error - Comprehensive Diagnostic</h1>
        
        <h2>Step 1: Check Current Server Status</h2>
        <?php
        echo "<div class='test-result info'>";
        echo "<strong>Request Information:</strong><br>";
        echo "Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
        echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
        echo "PHP Version: " . phpversion() . "<br>";
        echo "SAPI: " . php_sapi_name() . "<br>";
        echo "</div>";
        
        // Check if we can get Apache version
        if (function_exists('apache_get_version')) {
            echo "<div class='test-result info'>";
            echo "Apache Version: " . apache_get_version();
            echo "</div>";
        }
        ?>

        <h2>Step 2: Check Apache Modules</h2>
        <?php
        $required_modules = [
            'mod_rewrite' => 'URL rewriting',
            'mod_headers' => 'HTTP headers',
            'mod_version' => 'Version checking',
            'mod_authz_core' => 'Authorization (Apache 2.4)',
            'mod_access_compat' => 'Legacy access control (Apache 2.2)',
        ];

        if (function_exists('apache_get_modules')) {
            $loaded = apache_get_modules();
            
            foreach ($required_modules as $module => $desc) {
                $is_loaded = in_array($module, $loaded);
                $class = $is_loaded ? 'pass' : ($module === 'mod_authz_core' || $module === 'mod_access_compat' ? 'warn' : 'fail');
                $status = $is_loaded ? '✓ LOADED' : '✗ NOT LOADED';
                
                echo "<div class='test-result $class'>";
                echo "<strong>$module:</strong> $status<br>";
                echo "<small>$desc</small>";
                echo "</div>";
            }
        } else {
            echo "<div class='test-result fail'>";
            echo "⚠ Cannot check Apache modules (running as FastCGI)<br>";
            echo "You may need to check httpd.conf manually";
            echo "</div>";
        }
        ?>

        <h2>Step 3: Check .htaccess File</h2>
        <?php
        $htaccess_path = __DIR__ . '/.htaccess';
        $htaccess_exists = file_exists($htaccess_path);
        $htaccess_readable = $htaccess_exists ? is_readable($htaccess_path) : false;
        
        echo "<div class='test-result " . ($htaccess_exists ? 'pass' : 'fail') . "'>";
        echo "<strong>.htaccess File:</strong><br>";
        echo "Path: " . $htaccess_path . "<br>";
        echo "Exists: " . ($htaccess_exists ? '✓ YES' : '✗ NO') . "<br>";
        echo "Readable: " . ($htaccess_readable ? '✓ YES' : '✗ NO') . "<br>";
        
        if ($htaccess_exists && $htaccess_readable) {
            echo "<br><strong>File Content (first 20 lines):</strong><br>";
            echo "<div class='code'>";
            $lines = file($htaccess_path, FILE_IGNORE_NEW_LINES);
            for ($i = 0; $i < min(20, count($lines)); $i++) {
                echo htmlspecialchars($lines[$i]) . "<br>";
            }
            if (count($lines) > 20) {
                echo "<em>... (" . (count($lines) - 20) . " more lines)</em>";
            }
            echo "</div>";
        }
        echo "</div>";
        ?>

        <h2>Step 4: Test Different API Access Methods</h2>
        <div style="background: #1a1a1a; padding: 15px; border-radius: 5px; margin: 10px 0;">
            <p><strong>Click buttons to test if API endpoints are accessible:</strong></p>
            
            <button onclick="testAPI('api/login.php')">Test api/login.php (POST)</button>
            <button onclick="testAPI('api/test_post.php')">Test api/test_post.php (POST)</button>
            <button onclick="testAPI('api/get_rows.php')">Test api/get_rows.php (GET)</button>
            <button onclick="testAPI('diagnose.php')">Test diagnose.php (GET)</button>
            
            <div id="testOutput"></div>
        </div>

        <h2>Step 5: What .htaccess Directives Do</h2>
        <div class="test-result info">
            <strong>Current Configuration Enables:</strong>
            <ul>
                <li><code>&lt;IfModule mod_version.c&gt;</code> - Detects Apache version</li>
                <li><code>Require all granted</code> - Apache 2.4 syntax to allow all access</li>
                <li><code>Order allow,deny / Allow from all</code> - Apache 2.2 syntax</li>
                <li><code>&lt;IfModule mod_rewrite.c&gt;</code> - URL rewriting</li>
                <li><code>&lt;FilesMatch "\.php$"&gt;</code> - Process PHP files</li>
            </ul>
        </div>

        <h2>Step 6: Common Causes of 405 Errors</h2>
        <div class="test-result warn">
            <strong>Most Common Causes (in order):</strong><br>
            1. <strong>Apache modules not loaded</strong> - Check if mod_rewrite exists above<br>
            2. <strong>.htaccess not processed</strong> - Check if .htaccess file exists<br>
            3. <strong>FastCGI mode</strong> - .htaccess doesn't work with FastCGI<br>
            4. <strong>AllowOverride disabled</strong> - Apache config doesn't allow .htaccess<br>
            5. <strong>.htaccess syntax error</strong> - Invalid directives block requests<br>
        </div>

        <h2>Step 7: Quick Fixes to Try</h2>
        <div class="test-result info">
            <h3>Option 1: Disable and Rebuild .htaccess</h3>
            <button class="danger" onclick="if(confirm('This will remove .htaccess. Use the builder below to recreate it.')) { alert('Use the button below to rebuild .htaccess'); }">Remove .htaccess (Not Recommended)</button>
            
            <h3>Option 2: Use PHP to Handle Requests</h3>
            <p>Create an <code>index.php</code> router in the api folder to bypass .htaccess issues:</p>
            <button onclick="showRouterCode()">Show PHP Router Code</button>
            <div id="routerCode"></div>
            
            <h3>Option 3: Check Apache Configuration</h3>
            <p>Ensure these lines are in <code>C:\xampp\apache\conf\httpd.conf</code></p>
            <div class="code">
LoadModule rewrite_module modules/mod_rewrite.so<br>
LoadModule headers_module modules/mod_headers.so<br>
...<br>
&lt;Directory "C:/xampp/htdocs"&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;AllowOverride All<br>
&lt;/Directory&gt;
            </div>
        </div>

        <h2>Step 8: Nuclear Option - Direct Test</h2>
        <div class="test-result warn">
            <p>If all above fails, we need to bypass .htaccess completely.</p>
            <button onclick="showNuclearFix()">Show Nuclear Fix</button>
            <div id="nuclearFix"></div>
        </div>

        <h2>Step 9: Check Error Logs</h2>
        <div class="test-result info">
            <p><strong>Check these log files for clues:</strong></p>
            <div class="code">
C:\xampp\apache\logs\error.log<br>
C:\xampp\apache\logs\access.log<br>
C:\xampp\php\logs\php_errors.log
            </div>
            <p>Look for any lines containing:</p>
            <ul>
                <li><code>405</code> - Method not allowed</li>
                <li><code>.htaccess</code> - Configuration issues</li>
                <li><code>syntax error</code> - File syntax problems</li>
                <li><code>Permission denied</code> - File access issues</li>
            </ul>
        </div>
    </div>

    <script>
        async function testAPI(endpoint) {
            const output = document.getElementById('testOutput');
            output.innerHTML = '<div class="log-output">Testing ' + endpoint + '...<br>';
            
            try {
                // Try POST first
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'test=1'
                });
                
                let responseText = '';
                try {
                    responseText = await response.clone().text();
                } catch (e) {
                    responseText = '[Unable to read response]';
                }
                
                output.innerHTML += `<span style="color: #f0f;">Status: ${response.status} ${response.statusText}</span><br>`;
                output.innerHTML += `<span style="color: #0f0;">Headers OK</span><br>`;
                output.innerHTML += `Response (first 200 chars):<br><span style="color: #0ff;">${responseText.substring(0, 200)}</span><br>`;
                output.innerHTML += '</div>';
                
                if (response.status === 405) {
                    output.innerHTML += '<div class="test-result fail"><strong>✗ Still Getting 405!</strong><br>This confirms .htaccess is not working properly.</div>';
                } else if (response.ok || response.status === 400 || response.status === 401) {
                    output.innerHTML += '<div class="test-result pass"><strong>✓ Endpoint Accessible!</strong><br>Status ' + response.status + ' is OK (not 405).</div>';
                } else {
                    output.innerHTML += '<div class="test-result warn"><strong>? Different Error:</strong><br>Status ' + response.status + ' - See above for details.</div>';
                }
            } catch (error) {
                output.innerHTML += `<span style="color: #f00;">Error: ${error.message}</span></div>`;
                output.innerHTML += '<div class="test-result fail"><strong>✗ Connection Failed</strong><br>Network error or endpoint not found.</div>';
            }
        }

        function showRouterCode() {
            const code = document.getElementById('routerCode');
            code.innerHTML = `
            <div class="test-result info">
                <strong>Create: api/index.php</strong><br>
                This router will handle all requests, bypassing .htaccess:<br>
                <div class="code" style="margin-top: 10px;">
&lt;?php<br>
header('Content-Type: application/json');<br>
header('Access-Control-Allow-Origin: *');<br>
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');<br>
<br>
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {<br>
&nbsp;&nbsp;&nbsp;&nbsp;http_response_code(200);<br>
&nbsp;&nbsp;&nbsp;&nbsp;exit;<br>
}<br>
<br>
// Route requests<br>
$request = basename($_SERVER['REQUEST_URI'], '.php');<br>
<br>
switch ($request) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;case 'login':<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;require 'login.php';<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;break;<br>
&nbsp;&nbsp;&nbsp;&nbsp;case 'test_post':<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;require 'test_post.php';<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;break;<br>
&nbsp;&nbsp;&nbsp;&nbsp;// Add other routes...<br>
&nbsp;&nbsp;&nbsp;&nbsp;default:<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;http_response_code(404);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo json_encode(['error' => 'Endpoint not found']);<br>
}<br>
?&gt;
                </div>
                <p>Then update login.html to use: <code>api/index.php?action=login</code></p>
            </div>
            `;
        }

        function showNuclearFix() {
            const fix = document.getElementById('nuclearFix');
            fix.innerHTML = `
            <div class="test-result fail">
                <strong>⚠ NUCLEAR FIX - Last Resort</strong><br><br>
                
                <strong>Step 1: Disable .htaccess</strong><br>
                Rename: <code>C:\\xampp\\htdocs\\spottracker\\.htaccess</code><br>
                To: <code>.htaccess.bak</code><br><br>
                
                <strong>Step 2: Restart Apache</strong><br>
                Open XAMPP Control Panel → Stop Apache → Start Apache<br><br>
                
                <strong>Step 3: Test Again</strong><br>
                Try login at: <code>http://localhost/spottracker/login.html</code><br><br>
                
                <strong>If 405 DISAPPEARS:</strong><br>
                Problem is .htaccess ← Need to fix syntax or enable AllowOverride<br><br>
                
                <strong>If 405 STILL SHOWS:</strong><br>
                Problem is elsewhere (maybe PHP configuration or Apache modules)
            </div>
            `;
        }
    </script>
</body>
</html>
