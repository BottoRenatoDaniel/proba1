<?php
/**
 * Apache Method Test - Test if Apache allows POST requests
 */

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Apache Method Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        h2 { color: #0066cc; margin-top: 30px; }
        .result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #999;
        }
        .success { border-left-color: #28a745; background: #f0f7f0; color: #155724; }
        .error { border-left-color: #dc3545; background: #fff0f0; color: #721c24; }
        .info { border-left-color: #0066cc; background: #f0f8ff; color: #004085; }
        .test-form {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        button {
            padding: 8px 16px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #0052a3; }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .log {
            background: #000;
            color: #0f0;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Apache Method Test</h1>
        
        <h2>Current Request Information</h2>
        <div class="result info">
            <p><strong>Current Method:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
            <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
        </div>

        <h2>Apache Modules Check</h2>
        <?php
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $required = ['mod_rewrite', 'mod_headers', 'mod_version', 'mod_mime'];
            
            foreach ($required as $module) {
                $loaded = in_array($module, $modules);
                $class = $loaded ? 'success' : 'error';
                echo "<div class='result $class'>";
                echo "<strong>" . ($loaded ? '✓' : '✗') . " $module:</strong> ";
                echo $loaded ? "Loaded" : "NOT LOADED";
                echo "</div>";
            }
        } else {
            echo "<div class='result error'><strong>⚠ Apache modules check unavailable</strong><br>Cannot use apache_get_modules(). You may be running PHP as CGI/FastCGI instead of mod_php.</div>";
        }
        ?>

        <h2>Test POST Request</h2>
        <div class="test-form">
            <p>This form tests if POST requests work properly:</p>
            <form method="POST" id="testForm">
                <input type="text" name="test_input" value="Test Data" placeholder="Test input">
                <button type="submit">Send POST Request</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<div class='result success'>";
            echo "<strong>✓ POST Request Successful!</strong><br>";
            echo "Received data: <code>" . htmlspecialchars($_POST['test_input'] ?? 'No data') . "</code>";
            echo "</div>";
        }
        ?>

        <h2>Test JavaScript Fetch to API</h2>
        <div class="test-form">
            <p>This tests if JavaScript can POST to the login API:</p>
            <button id="fetchBtn">Test Fetch to api/test_post.php</button>
            <div id="fetchResult"></div>
        </div>

        <h2>Troubleshooting Steps</h2>
        <ol>
            <li><strong>If all Apache modules show ✓:</strong> The issue might be in .htaccess file syntax</li>
            <li><strong>If mod_rewrite is missing ✗:</strong> Enable it in httpd.conf:
                <div style="background: #f4f4f4; padding: 10px; margin: 5px 0; border-radius: 4px;">
                    <code>LoadModule rewrite_module modules/mod_rewrite.so</code>
                </div>
            </li>
            <li><strong>If running as FastCGI:</strong> .htaccess might not work. Use PHP configuration instead</li>
            <li><strong>Check .htaccess syntax:</strong> Invalid syntax can block all requests</li>
            <li><strong>Review Apache error log:</strong> <code>C:\xampp\apache\logs\error.log</code></li>
        </ol>

        <h2>Common Fixes</h2>
        <div class="result info">
            <h3>Fix 1: Restart Apache</h3>
            <p>Open XAMPP Control Panel and click "Restart" next to Apache.</p>
        </div>
        <div class="result info">
            <h3>Fix 2: Check .htaccess Permissions</h3>
            <p>Ensure .htaccess file exists in the spottracker folder and is readable by Apache.</p>
        </div>
        <div class="result info">
            <h3>Fix 3: Test Individual API Files</h3>
            <p>Go to <code>http://localhost/spottracker/api/test_post.php</code> directly and submit test data.</p>
        </div>

        <h2>Check Apache Configuration</h2>
        <p>Click to view key Apache settings (if you have admin access):</p>
        <div style="background: #f9f9f9; padding: 10px; border-radius: 4px; margin: 10px 0;">
            <code>LoadModule php7_module modules/php7apache2_4.so</code> (enables PHP)<br>
            <code>LoadModule rewrite_module modules/mod_rewrite.so</code> (enables .htaccess)<br>
            <code>LoadModule headers_module modules/mod_headers.so</code> (enables Header directives)
        </div>

        <h2>Browser Console Logging</h2>
        <p>The login page now logs detailed information. To view it:</p>
        <ol>
            <li>Open login.html in browser</li>
            <li>Press <strong>F12</strong> to open Developer Tools</li>
            <li>Click <strong>Console</strong> tab</li>
            <li>Try to login</li>
            <li>Check the console for detailed error messages and response status</li>
        </ol>
    </div>

    <script>
        document.getElementById('fetchBtn').addEventListener('click', async function() {
            const resultDiv = document.getElementById('fetchResult');
            resultDiv.innerHTML = '<p style="color: #666;">Testing...</p>';
            
            try {
                const response = await fetch('api/test_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'test=true'
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    resultDiv.innerHTML = `<div class="result success">
                        <strong>✓ Fetch Successful (HTTP ${response.status})</strong><br>
                        <code>${JSON.stringify(data, null, 2)}</code>
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="result error">
                        <strong>✗ Fetch Failed (HTTP ${response.status})</strong><br>
                        <code>${JSON.stringify(data, null, 2)}</code>
                    </div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="result error">
                    <strong>✗ Request Error</strong><br>
                    ${error.message}
                </div>`;
            }
        });
    </script>
</body>
</html>
