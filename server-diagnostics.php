<?php
/**
 * Server Diagnostics Tool
 * Tests server configuration, permissions, and PHP settings
 */

// Ensure all errors are shown
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Server Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2 { color: #333; }
        .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow: auto; font-family: monospace; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Advanced Server Diagnostics</h1>";

function startSection($title) {
    echo "<div class='section'><h2>$title</h2>";
}

function endSection() {
    echo "</div>";
}

// Basic Server Information
startSection("Server Information");
echo "<table>
    <tr><th>Item</th><th>Value</th></tr>
    <tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>
    <tr><td>Server Software</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>
    <tr><td>Server OS</td><td>" . PHP_OS . "</td></tr>
    <tr><td>Server Time</td><td>" . date('Y-m-d H:i:s') . "</td></tr>
    <tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>
    <tr><td>Script Path</td><td>" . __FILE__ . "</td></tr>
</table>";
endSection();

// PHP Configuration
startSection("PHP Configuration");

// Important PHP settings
$phpSettings = [
    'allow_url_fopen', 'display_errors', 'file_uploads', 
    'max_execution_time', 'max_file_uploads', 'max_input_time',
    'memory_limit', 'post_max_size', 'upload_max_filesize',
    'session.save_path', 'session.use_cookies', 'session.use_only_cookies',
    'session.use_trans_sid'
];

echo "<table>
    <tr><th>Setting</th><th>Value</th></tr>";

foreach ($phpSettings as $setting) {
    echo "<tr><td>$setting</td><td>" . ini_get($setting) . "</td></tr>";
}

// Check for disabled functions
$disabledFunctions = ini_get('disable_functions');
echo "<tr><td>disabled_functions</td><td>" . ($disabledFunctions ? $disabledFunctions : "<span class='success'>None</span>") . "</td></tr>";

// Check for open_basedir restrictions
$openBasedir = ini_get('open_basedir');
echo "<tr><td>open_basedir</td><td>" . ($openBasedir ? $openBasedir : "<span class='success'>No restrictions</span>") . "</td></tr>";

echo "</table>";

// Check if important PHP extensions are loaded
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'session', 'mbstring'];
$loadedExtensions = get_loaded_extensions();

echo "<h3>PHP Extensions</h3><table>
    <tr><th>Extension</th><th>Status</th></tr>";

foreach ($requiredExtensions as $ext) {
    $loaded = in_array($ext, $loadedExtensions);
    echo "<tr><td>$ext</td><td>" . 
        ($loaded ? "<span class='success'>Loaded</span>" : "<span class='error'>Not loaded</span>") . 
        "</td></tr>";
}
echo "</table>";
endSection();

// File Permissions
startSection("File Permissions");

$filesToCheck = [
    '.' => 'Current directory',
    'admin-login.php' => 'Login page',
    'admin-login-process.php' => 'Login process',
    'admin-dashboard.php' => 'Dashboard page',
    'db_config.php' => 'Database config',
    '/tmp' => 'Temp directory'
];

echo "<table>
    <tr><th>File/Directory</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Executable</th><th>Permissions</th></tr>";

foreach ($filesToCheck as $file => $description) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    $writable = is_writable($file);
    $executable = is_executable($file);
    $perms = $exists ? substr(sprintf('%o', fileperms($file)), -4) : 'N/A';
    
    echo "<tr>
        <td>$file ($description)</td>
        <td>" . ($exists ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</td>
        <td>" . ($readable ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</td>
        <td>" . ($writable ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</td>
        <td>" . ($executable ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</td>
        <td>$perms</td>
    </tr>";
}
echo "</table>";
endSection();

// Session Testing
startSection("Session Functionality Test");
echo "<h3>Session Path Test</h3>";

$sessionPath = ini_get('session.save_path');
if (empty($sessionPath)) {
    $sessionPath = sys_get_temp_dir();
    echo "<p class='warning'>No explicit session.save_path set. Using system temp directory: $sessionPath</p>";
} else {
    echo "<p>Session save path: $sessionPath</p>";
}

if (file_exists($sessionPath)) {
    echo "<p class='success'>Session path exists</p>";
    
    if (is_writable($sessionPath)) {
        echo "<p class='success'>Session path is writable</p>";
    } else {
        echo "<p class='error'>Session path is not writable!</p>";
    }
} else {
    echo "<p class='error'>Session path does not exist!</p>";
}

// Test session saving
echo "<h3>Session Write Test</h3>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>Session was already started.</p>";
} else {
    echo "<p>Starting session now.</p>";
    session_start();
}

$testValue = 'test_' . time();
$_SESSION['diagnostics_test'] = $testValue;

if (isset($_SESSION['diagnostics_test']) && $_SESSION['diagnostics_test'] === $testValue) {
    echo "<p class='success'>Session data successfully written and read!</p>";
} else {
    echo "<p class='error'>Failed to write or read session data!</p>";
}

// Session ID info
echo "<p>Current Session ID: " . session_id() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";

// Check cookie settings
echo "<h3>Cookie Settings</h3>";
$cookieParams = session_get_cookie_params();
echo "<pre class='code'>";
print_r($cookieParams);
echo "</pre>";
endSection();

// HTTP Headers & Redirect Test
startSection("HTTP Headers & Redirect Test");
echo "<p>This section will test if the server can process redirects correctly.</p>";

// Get current URL to construct redirect
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

echo "<p>Your current URL: $currentUrl</p>";

// Test if we can set a cookie
echo "<h3>Cookie Test</h3>";
if (setcookie('diagnostic_test', 'test_value', time() + 300)) {
    echo "<p class='success'>Cookie set successfully (but we can't verify it was accepted by the browser in this script)</p>";
} else {
    echo "<p class='error'>Failed to set cookie!</p>";
}

// Redirect test button
echo "<p>Click the button below to test redirects. You should be redirected back to this page with a 'redirect=success' parameter.</p>";
echo "<form method='post'><input type='submit' name='test_redirect' value='Test Redirect'></form>";

// Check if we've been redirected back
if (isset($_GET['redirect']) && $_GET['redirect'] === 'success') {
    echo "<p class='success'>Redirect test successful! The server can process redirects.</p>";
}

// Process redirect test
if (isset($_POST['test_redirect'])) {
    $redirectUrl = strtok($currentUrl, '?') . '?redirect=success';
    echo "<p>Redirecting to: $redirectUrl</p>";
    header("Location: $redirectUrl");
    exit;
}

// Header info
echo "<h3>Available Server Headers</h3>";
echo "<pre class='code'>";
$headers = apache_request_headers();
foreach ($headers as $header => $value) {
    echo htmlspecialchars("$header: $value\n");
}
echo "</pre>";
endSection();

// Database Connection Test
startSection("Database Connection Test");
if (file_exists('db_config.php')) {
    echo "<p class='success'>Database config file exists</p>";
    
    // Test database connection
    try {
        require_once 'db_config.php';
        
        if (function_exists('get_db_connection')) {
            echo "<p class='success'>Database connection function found</p>";
            
            $db = get_db_connection();
            echo "<p class='success'>Database connection successful!</p>";
            
            // Test query execution
            $stmt = $db->query("SELECT 1");
            $result = $stmt->fetch();
            
            if ($result) {
                echo "<p class='success'>Successfully executed a test query</p>";
            }
        } else {
            echo "<p class='error'>Database connection function not found in db_config.php</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>Database config file not found</p>";
}
endSection();

// File Encoding Check
startSection("File Encoding Check");
$filesToCheck = ['admin-login.php', 'admin-login-process.php', 'admin-dashboard.php', 'db_config.php'];

echo "<table>
    <tr><th>File</th><th>Size</th><th>First 50 bytes (hex)</th><th>BOM Check</th><th>Line Endings</th></tr>";

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $size = filesize($file);
        
        // Get first 50 bytes for hex display
        $firstBytes = substr($content, 0, 50);
        $hex = bin2hex($firstBytes);
        $hexDisplay = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $hexDisplay .= substr($hex, $i, 2) . ' ';
        }
        
        // Check for BOM
        $hasBOM = false;
        $bomType = "None";
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $hasBOM = true;
            $bomType = "UTF-8 BOM";
        } elseif (substr($content, 0, 2) === "\xFE\xFF") {
            $hasBOM = true;
            $bomType = "UTF-16 BE BOM";
        } elseif (substr($content, 0, 2) === "\xFF\xFE") {
            $hasBOM = true;
            $bomType = "UTF-16 LE BOM";
        }
        
        // Check line endings
        $windowsEndings = substr_count($content, "\r\n");
        $unixEndings = substr_count($content, "\n") - $windowsEndings;
        $macEndings = substr_count($content, "\r") - $windowsEndings;
        
        $lineEndingType = "Mixed";
        if ($windowsEndings > 0 && $unixEndings == 0 && $macEndings == 0) {
            $lineEndingType = "Windows (CRLF)";
        } elseif ($windowsEndings == 0 && $unixEndings > 0 && $macEndings == 0) {
            $lineEndingType = "Unix (LF)";
        } elseif ($windowsEndings == 0 && $unixEndings == 0 && $macEndings > 0) {
            $lineEndingType = "Mac (CR)";
        }
        
        echo "<tr>
            <td>$file</td>
            <td>$size bytes</td>
            <td><code>$hexDisplay</code></td>
            <td>" . ($hasBOM ? "<span class='warning'>$bomType</span>" : "<span class='success'>No BOM</span>") . "</td>
            <td>$lineEndingType (Win: $windowsEndings, Unix: $unixEndings, Mac: $macEndings)</td>
        </tr>";
    } else {
        echo "<tr>
            <td>$file</td>
            <td colspan='4'><span class='error'>File not found</span></td>
        </tr>";
    }
}
echo "</table>";
endSection();

// htaccess check
startSection(".htaccess Check");
if (file_exists('.htaccess')) {
    echo "<p class='success'>.htaccess file exists</p>";
    
    $htaccess = file_get_contents('.htaccess');
    
    // Check for common restrictive directives
    $rewriteEngine = preg_match('/RewriteEngine\s+On/i', $htaccess);
    $rewriteRules = preg_match_all('/RewriteRule/i', $htaccess);
    $rewriteCond = preg_match_all('/RewriteCond/i', $htaccess);
    $deny = preg_match_all('/Deny\s+from/i', $htaccess);
    $allow = preg_match_all('/Allow\s+from/i', $htaccess);
    $redirect = preg_match_all('/Redirect/i', $htaccess);
    
    echo "<p>Found in .htaccess:</p>";
    echo "<ul>";
    echo "<li>RewriteEngine On: " . ($rewriteEngine ? "Yes" : "No") . "</li>";
    echo "<li>RewriteRule lines: $rewriteRules</li>";
    echo "<li>RewriteCond lines: $rewriteCond</li>";
    echo "<li>Deny from lines: $deny</li>";
    echo "<li>Allow from lines: $allow</li>";
    echo "<li>Redirect lines: $redirect</li>";
    echo "</ul>";
    
    if ($rewriteEngine && ($rewriteRules > 0 || $rewriteCond > 0)) {
        echo "<p class='warning'>Found rewrite rules that could potentially affect redirects</p>";
    }
    
    if ($deny > 0) {
        echo "<p class='warning'>Found access restrictions that could block certain requests</p>";
    }
    
    // Display the first 50 lines
    $lines = explode("\n", $htaccess);
    $first50 = array_slice($lines, 0, 50);
    
    echo "<p>First 50 lines of .htaccess (or fewer if the file is smaller):</p>";
    echo "<pre class='code'>";
    foreach ($first50 as $i => $line) {
        echo htmlspecialchars(($i + 1) . ": $line\n");
    }
    echo "</pre>";
} else {
    echo "<p class='warning'>No .htaccess file found in current directory</p>";
}
endSection();

// PHP Error Log
startSection("PHP Error Logs");
$errorLog = ini_get('error_log');
if (!empty($errorLog)) {
    echo "<p>PHP error log configured at: $errorLog</p>";
    
    if (file_exists($errorLog) && is_readable($errorLog)) {
        echo "<p class='success'>Error log exists and is readable</p>";
        
        // Get the last few lines of the error log
        $logContent = file_get_contents($errorLog);
        $lines = explode("\n", $logContent);
        $lastLines = array_slice($lines, -30);  // Get last 30 lines
        
        echo "<p>Last 30 lines of error log:</p>";
        echo "<pre class='code'>";
        foreach ($lastLines as $line) {
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<p class='warning'>Error log file doesn't exist or is not readable</p>";
    }
} else {
    echo "<p class='warning'>No error_log path configured in PHP</p>";
    
    // Try to find alternative error logs
    $possibleLogs = [
        '/var/log/php_errors.log',
        '/var/log/apache2/error.log',
        '/var/log/httpd/error_log',
        $_SERVER['DOCUMENT_ROOT'] . '/../logs/error_log'
    ];
    
    foreach ($possibleLogs as $log) {
        if (file_exists($log) && is_readable($log)) {
            echo "<p>Found a possible error log at: $log</p>";
        }
    }
}
endSection();

// Final Recommendations
startSection("Recommendations & Next Steps");
echo "<p>Based on the diagnostics, here are potential issues to check:</p>";
echo "<ol>";
echo "<li>Check if PHP version is compatible with your code (you're running " . phpversion() . ")</li>";
echo "<li>Look for file permission issues, especially on session save path and PHP files</li>";
echo "<li>Verify that your scripts don't have BOM markers or incorrect line endings</li>";
echo "<li>Check for PHP configuration issues that might restrict functionality</li>";
echo "<li>Look for .htaccess rules that might be blocking redirects</li>";
echo "<li>Ensure database connection parameters are correct</li>";
echo "</ol>";

echo "<p><strong>Security Notice:</strong> Once you've completed your diagnostics, please delete this file from your server as it contains sensitive system information.</p>";
endSection();

// Output final HTML
echo "</body></html>";
?>