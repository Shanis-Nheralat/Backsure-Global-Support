<?php
/**
 * Admin Login Diagnostic Tool
 * This file helps diagnose issues with admin login redirects
 */

// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to display formatted messages
function display_message($message, $type = 'info') {
    $bg_color = '#d1ecf1';
    $text_color = '#0c5460';
    $icon = 'ℹ️';
    
    switch ($type) {
        case 'success':
            $bg_color = '#d4edda';
            $text_color = '#155724';
            $icon = '✅';
            break;
        case 'warning':
            $bg_color = '#fff3cd';
            $text_color = '#856404';
            $icon = '⚠️';
            break;
        case 'error':
            $bg_color = '#f8d7da';
            $text_color = '#721c24';
            $icon = '❌';
            break;
    }
    
    echo "<div style='margin: 10px 0; padding: 15px; border-radius: 4px; background-color: {$bg_color}; color: {$text_color};'>{$icon} {$message}</div>";
}

// Function to check server info
function check_server_info() {
    $results = [];
    
    // Check PHP version
    $results[] = [
        'name' => 'PHP Version',
        'value' => phpversion(),
        'status' => version_compare(phpversion(), '7.0.0', '>=') ? 'success' : 'warning',
        'message' => version_compare(phpversion(), '7.0.0', '>=') ? 'OK' : 'PHP 7.0 or higher recommended'
    ];
    
    // Check session support
    $results[] = [
        'name' => 'Session Support',
        'value' => function_exists('session_start') ? 'Enabled' : 'Disabled',
        'status' => function_exists('session_start') ? 'success' : 'error',
        'message' => function_exists('session_start') ? 'OK' : 'Sessions are required for login'
    ];
    
    // Check session path
    $session_path = session_save_path();
    $session_path_writable = !empty($session_path) && is_writable($session_path);
    $results[] = [
        'name' => 'Session Save Path',
        'value' => empty($session_path) ? 'Default' : $session_path,
        'status' => $session_path_writable ? 'success' : 'warning',
        'message' => $session_path_writable ? 'Writable' : 'May not be writable'
    ];
    
    // Check cookie settings
    $results[] = [
        'name' => 'Session Cookie Path',
        'value' => ini_get('session.cookie_path'),
        'status' => 'info',
        'message' => 'Default is /'
    ];
    
    $results[] = [
        'name' => 'Session Cookie Domain',
        'value' => ini_get('session.cookie_domain') ?: 'Not set',
        'status' => 'info',
        'message' => 'Default is empty'
    ];
    
    $results[] = [
        'name' => 'Session Cookie Secure',
        'value' => ini_get('session.cookie_secure') ? 'Yes' : 'No',
        'status' => 'info',
        'message' => 'Only sent over HTTPS if enabled'
    ];
    
    $results[] = [
        'name' => 'Session Cookie HttpOnly',
        'value' => ini_get('session.cookie_httponly') ? 'Yes' : 'No',
        'status' => 'info',
        'message' => 'Prevents JavaScript access if enabled'
    ];
    
    // Check server software
    $results[] = [
        'name' => 'Server Software',
        'value' => $_SERVER['SERVER_SOFTWARE'],
        'status' => 'info',
        'message' => 'For informational purposes'
    ];
    
    // Check for mod_rewrite if Apache
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
        $mod_rewrite = in_array('mod_rewrite', apache_get_modules());
        $results[] = [
            'name' => 'Apache mod_rewrite',
            'value' => $mod_rewrite ? 'Enabled' : 'Disabled',
            'status' => $mod_rewrite ? 'success' : 'warning',
            'message' => $mod_rewrite ? 'OK' : 'May be needed for clean URLs'
        ];
    }
    
    return $results;
}

// Function to check session handling
function check_session_handling() {
    // Clear previous test value if any
    if (isset($_SESSION['login_test'])) {
        unset($_SESSION['login_test']);
    }
    
    // Set a test value
    $_SESSION['login_test'] = 'test_value_' . time();
    
    // Get the value back
    $test_value = isset($_SESSION['login_test']) ? $_SESSION['login_test'] : null;
    
    if ($test_value) {
        return [
            'success' => true,
            'message' => 'Session is working correctly.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Session is not working. Session data could not be saved.'
        ];
    }
}

// Function to check for common redirect issues
function check_redirect_issues() {
    $issues = [];
    
    // Check for infinite redirect via PHP
    if (isset($_SESSION['redirect_count'])) {
        $_SESSION['redirect_count']++;
        if ($_SESSION['redirect_count'] > 10) {
            $issues[] = [
                'name' => 'Excessive Redirects',
                'status' => 'error',
                'message' => 'Detected ' . $_SESSION['redirect_count'] . ' redirects. This indicates a redirect loop in PHP code.'
            ];
        }
    } else {
        $_SESSION['redirect_count'] = 1;
    }
    
    // Check for .htaccess presence
    if (file_exists('.htaccess')) {
        $htaccess_content = file_get_contents('.htaccess');
        if (strpos($htaccess_content, 'RewriteEngine') !== false) {
            if (strpos($htaccess_content, 'RewriteRule') !== false) {
                $issues[] = [
                    'name' => '.htaccess Redirects',
                    'status' => 'warning',
                    'message' => '.htaccess contains RewriteRule directives that might cause redirects.'
                ];
            }
        }
    }
    
    // Check for potential header issues in common files
    $files_to_check = ['index.php', 'admin/index.php', 'admin-login.php', 'admin/login.php'];
    foreach ($files_to_check as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            // Check for redirects with header() function
            if (preg_match('/header\s*\(\s*[\'"]Location:/i', $content)) {
                $issues[] = [
                    'name' => 'Redirect in ' . $file,
                    'status' => 'warning',
                    'message' => 'Found header("Location:...") in ' . $file . ' which might cause redirects.'
                ];
            }
        }
    }
    
    // Check current headers
    $headers = [];
    foreach (headers_list() as $header) {
        $headers[] = $header;
        if (stripos($header, 'location:') === 0) {
            $issues[] = [
                'name' => 'Active Redirect',
                'status' => 'error',
                'message' => 'This page is actively redirecting: ' . $header
            ];
        }
    }
    
    // If no issues found, but we're diagnosing redirects
    if (empty($issues) && isset($_GET['check_redirects'])) {
        $issues[] = [
            'name' => 'No Redirect Issues Found',
            'status' => 'info',
            'message' => 'No obvious redirect issues detected on this page.'
        ];
    }
    
    return $issues;
}

// Function to check for cookie issues
function check_cookie_issues() {
    // Try to set a test cookie
    $cookie_name = 'login_test_cookie';
    $cookie_value = 'test_value_' . time();
    
    setcookie($cookie_name, $cookie_value, time() + 3600, '/');
    
    // Check if cookies are already disabled
    if (count($_COOKIE) == 0) {
        return [
            'success' => false,
            'message' => 'No cookies detected. Cookies might be disabled in your browser.'
        ];
    }
    
    // Check if a previous test cookie exists
    if (isset($_COOKIE[$cookie_name])) {
        return [
            'success' => true,
            'message' => 'Cookies are working. Found previous test cookie.'
        ];
    } else {
        return [
            'status' => 'info',
            'message' => 'Cookie test started. Refresh the page to check if cookies are working.'
        ];
    }
}

// Function to check auth_cookie settings when using WordPress
function check_wp_auth_cookie() {
    // Check if this is a WordPress installation
    if (file_exists('wp-config.php') || file_exists('../wp-config.php')) {
        // Try to load WordPress configuration
        $wp_config_path = file_exists('wp-config.php') ? 'wp-config.php' : '../wp-config.php';
        $wp_config = file_get_contents($wp_config_path);
        
        $issues = [];
        
        // Check for COOKIE_DOMAIN definition
        if (preg_match('/define\s*\(\s*[\'"]COOKIE_DOMAIN[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)/i', $wp_config, $matches)) {
            $cookie_domain = $matches[1];
            $server_name = $_SERVER['SERVER_NAME'];
            
            if ($cookie_domain != $server_name && $cookie_domain != '.' . $server_name) {
                $issues[] = [
                    'name' => 'WordPress COOKIE_DOMAIN',
                    'status' => 'error',
                    'message' => "COOKIE_DOMAIN set to '{$cookie_domain}' but server name is '{$server_name}'. This mismatch can cause login issues."
                ];
            }
        }
        
        // Check for non-standard COOKIEPATH
        if (preg_match('/define\s*\(\s*[\'"]COOKIEPATH[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)/i', $wp_config, $matches)) {
            $cookie_path = $matches[1];
            if ($cookie_path != '/' && $cookie_path != get_site_url_path()) {
                $issues[] = [
                    'name' => 'WordPress COOKIEPATH',
                    'status' => 'warning',
                    'message' => "COOKIEPATH set to '{$cookie_path}' which may cause login issues if your admin page is outside this path."
                ];
            }
        }
        
        return $issues;
    }
    
    return [];
}

// Helper function to get site URL path
function get_site_url_path() {
    $path = parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH);
    $path = preg_replace('#/[^/]*$#', '/', $path);
    return $path;
}

// Function to check login form
function check_login_form() {
    // Find common login form files
    $login_files = [
        'admin-login.php',
        'admin/login.php',
        'login.php',
        'wp-login.php',
        'admin/index.php',
        'admin-auth.php'
    ];
    
    $found_files = [];
    
    foreach ($login_files as $file) {
        if (file_exists($file)) {
            $found_files[] = [
                'name' => $file,
                'path' => realpath($file),
                'size' => filesize($file),
                'modified' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
    }
    
    return $found_files;
}

// Function to examine admin-auth.php content
function examine_auth_file($file = 'admin-auth.php') {
    if (!file_exists($file)) {
        return [
            'success' => false,
            'message' => "The file {$file} does not exist."
        ];
    }
    
    $content = file_get_contents($file);
    $issues = [];
    
    // Check for redirect loops
    $redirect_count = substr_count($content, 'header("Location:');
    if ($redirect_count > 1) {
        $issues[] = "Multiple redirects ({$redirect_count}) found in the file. This may cause redirect loops.";
    }
    
    // Check for session usage
    if (strpos($content, 'session_start') === false) {
        $issues[] = "No session_start() found. Sessions must be started to maintain login state.";
    }
    
    // Check for cookie settings
    if (strpos($content, 'setcookie') !== false) {
        $issues[] = "Cookie manipulation found. Check cookie parameters for proper path/domain.";
    }
    
    // Check for authentication methods
    $auth_methods = [];
    if (strpos($content, 'password_verify') !== false) {
        $auth_methods[] = "password_verify() (secure)";
    }
    if (strpos($content, 'md5') !== false) {
        $auth_methods[] = "md5() (insecure)";
    }
    if (strpos($content, 'sha1') !== false) {
        $auth_methods[] = "sha1() (insecure)";
    }
    
    if (empty($issues) && empty($auth_methods)) {
        $issues[] = "No obvious issues found, but couldn't identify authentication method.";
    }
    
    return [
        'success' => empty($issues),
        'auth_methods' => $auth_methods,
        'issues' => $issues,
        'lines' => count(explode("\n", $content))
    ];
}

// Process file uploads if any
$uploaded_file_result = null;
if ($_FILES && isset($_FILES['auth_file']) && $_FILES['auth_file']['error'] === UPLOAD_ERR_OK) {
    $temp_file = $_FILES['auth_file']['tmp_name'];
    $uploaded_filename = $_FILES['auth_file']['name'];
    
    // Only allow PHP files
    $file_ext = strtolower(pathinfo($uploaded_filename, PATHINFO_EXTENSION));
    if ($file_ext === 'php') {
        $content = file_get_contents($temp_file);
        
        // Basic security check - make sure it's a PHP file
        if (strpos($content, '<?php') !== false) {
            // Save to a temporary file
            $temp_analysis_file = 'temp_analysis_' . time() . '.php';
            file_put_contents($temp_analysis_file, $content);
            
            // Analyze the file
            $analysis = examine_auth_file($temp_analysis_file);
            $analysis['filename'] = $uploaded_filename;
            $uploaded_file_result = $analysis;
            
            // Delete the temporary file
            unlink($temp_analysis_file);
        } else {
            $uploaded_file_result = [
                'success' => false,
                'message' => 'The uploaded file does not appear to be a valid PHP file.'
            ];
        }
    } else {
        $uploaded_file_result = [
            'success' => false,
            'message' => 'Only PHP files can be analyzed.'
        ];
    }
}

// Run tests based on request
$server_info = check_server_info();
$session_test = check_session_handling();
$redirect_issues = check_redirect_issues();
$cookie_test = check_cookie_issues();
$wp_cookie_issues = check_wp_auth_cookie();
$login_files = check_login_form();
$auth_file_analysis = null;

if (file_exists('admin-auth.php')) {
    $auth_file_analysis = examine_auth_file();
}

// Function to render status badge
function status_badge($status) {
    $colors = [
        'success' => '#28a745',
        'warning' => '#ffc107',
        'error' => '#dc3545',
        'info' => '#17a2b8'
    ];
    
    $bg_color = $colors[$status] ?? $colors['info'];
    
    return "<span style='display: inline-block; padding: 3px 6px; border-radius: 3px; background-color: {$bg_color}; color: white; font-size: 12px;'>" . ucfirst($status) . "</span>";
}

// Reset session counter when explicitly requested
if (isset($_GET['reset'])) {
    unset($_SESSION['redirect_count']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Diagnostic Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-success {
            background-color: #2ecc71;
        }
        .btn-success:hover {
            background-color: #27ae60;
        }
        .btn-warning {
            background-color: #f39c12;
        }
        .btn-warning:hover {
            background-color: #e67e22;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 10px;
        }
        .code-block {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            margin: 15px 0;
        }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Login Diagnostic Tool</h1>
        <p>This tool helps diagnose redirect loops and login issues with your admin panel.</p>
        
        <div class="card">
            <h2>1. Current Session Status</h2>
            <?php display_message($session_test['message'], $session_test['success'] ? 'success' : 'error'); ?>
            
            <p>Session ID: <code><?php echo session_id(); ?></code></p>
            <p>Redirect Counter: <code><?php echo $_SESSION['redirect_count'] ?? 'Not set'; ?></code></p>
            
            <?php if (isset($_SESSION['redirect_count']) && $_SESSION['redirect_count'] > 1): ?>
                <p><a href="?reset=1" class="btn btn-warning">Reset Redirect Counter</a></p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>2. Cookie Test</h2>
            <?php 
            if (isset($cookie_test['success'])) {
                display_message($cookie_test['message'], $cookie_test['success'] ? 'success' : 'error');
            } else {
                display_message($cookie_test['message'], 'info');
            }
            ?>
            
            <h3>Current Cookies:</h3>
            <?php if (empty($_COOKIE)): ?>
                <p class="text-danger">No cookies found. This may indicate cookies are disabled in your browser.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                    </tr>
                    <?php foreach ($_COOKIE as $name => $value): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($name); ?></td>
                        <td><?php echo htmlspecialchars(substr($value, 0, 30)) . (strlen($value) > 30 ? '...' : ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>3. Redirect Analysis</h2>
            
            <?php if (empty($redirect_issues)): ?>
                <p>No redirect issues detected. <a href="?check_redirects=1" class="btn">Run Detailed Redirect Check</a></p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                    <?php foreach ($redirect_issues as $issue): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($issue['name']); ?></td>
                        <td><?php echo status_badge($issue['status']); ?></td>
                        <td><?php echo htmlspecialchars($issue['message']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            
            <h3>Common Causes of Redirect Loops:</h3>
            <ul>
                <li>Session issues (not starting session or session path not writable)</li>
                <li>Cookie domain or path mismatch</li>
                <li>Authentication code redirecting back to login page</li>
                <li>Redirects in .htaccess files</li>
                <li>SSL/HTTPS redirects conflicting with login page</li>
            </ul>
        </div>
        
        <div class="card">
            <h2>4. Login Files Analysis</h2>
            
            <?php if (empty($login_files)): ?>
                <p class="text-warning">No common login files found.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>File</th>
                        <th>Path</th>
                        <th>Size</th>
                        <th>Last Modified</th>
                    </tr>
                    <?php foreach ($login_files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['name']); ?></td>
                        <td><?php echo htmlspecialchars($file['path']); ?></td>
                        <td><?php echo number_format($file['size']) . ' bytes'; ?></td>
                        <td><?php echo htmlspecialchars($file['modified']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            
            <?php if ($auth_file_analysis): ?>
                <h3>Authentication File Analysis (admin-auth.php)</h3>
                
                <?php if (!empty($auth_file_analysis['issues'])): ?>
                    <div class="code-block">
                        <?php foreach ($auth_file_analysis['issues'] as $issue): ?>
                            <div class="text-warning">⚠️ <?php echo htmlspecialchars($issue); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($auth_file_analysis['auth_methods'])): ?>
                    <p>Authentication methods detected:</p>
                    <ul>
                        <?php foreach ($auth_file_analysis['auth_methods'] as $method): ?>
                            <li><?php echo htmlspecialchars($method); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <p>File size: <?php echo $auth_file_analysis['lines']; ?> lines</p>
            <?php endif; ?>
            
            <h3>Upload Authentication File for Analysis</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="auth_file">Select your login/authentication file:</label>
                    <input type="file" id="auth_file" name="auth_file" accept=".php">
                </div>
                <button type="submit" class="btn">Analyze File</button>
            </form>
            
            <?php if ($uploaded_file_result): ?>
                <h3>Analysis Results for <?php echo htmlspecialchars($uploaded_file_result['filename']); ?></h3>
                
                <?php if (isset($uploaded_file_result['message'])): ?>
                    <?php display_message($uploaded_file_result['message'], $uploaded_file_result['success'] ? 'success' : 'error'); ?>
                <?php else: ?>
                    <?php if (!empty($uploaded_file_result['issues'])): ?>
                        <div class="code-block">
                            <?php foreach ($uploaded_file_result['issues'] as $issue): ?>
                                <div class="text-warning">⚠️ <?php echo htmlspecialchars($issue); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-success">No issues found in the uploaded file.</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($uploaded_file_result['auth_methods'])): ?>
                        <p>Authentication methods detected:</p>
                        <ul>
                            <?php foreach ($uploaded_file_result['auth_methods'] as $method): ?>
                                <li><?php echo htmlspecialchars($method); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <p>File size: <?php echo $uploaded_file_result['lines']; ?> lines</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>5. Server Information</h2>
            
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
                <?php foreach ($server_info as $info): ?>
                <tr>
                    <td><?php echo htmlspecialchars($info['name']); ?></td>
                    <td><code><?php echo htmlspecialchars($info['value']); ?></code></td>
                    <td><?php echo status_badge($info['status']); ?></td>
                    <td><?php echo htmlspecialchars($info['message']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <?php if (!empty($wp_cookie_issues)): ?>
                <h3>WordPress Cookie Issues</h3>
                
                <table>
                    <tr>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                    <?php foreach ($wp_cookie_issues as $issue): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($issue['name']); ?></td>
                        <td><?php echo status_badge($issue['status']); ?></td>
                        <td><?php echo htmlspecialchars($issue['message']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>6. Recommended Fixes</h2>
            
            <h3>1. Fix Session Issues</h3>
            <div class="code-block">
// Make sure session_start() is called at the beginning of admin-auth.php
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Make sure session variables are properly set after login
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_user_id'] = $user_id; // from database
$_SESSION['admin_username'] = $username; // from form

// Make sure session is checked correctly
function require_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Redirect to login page
        header('Location: admin-login.php');
        exit; // Important - prevents further code execution
    }
}
