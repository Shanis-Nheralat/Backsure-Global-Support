<?php
/**
 * Login System Analyzer
 * This script analyzes your login files to determine the exact flow and relationships
 * without modifying any existing files
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Login System Analysis</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .highlight { background-color: #ffffcc; padding: 2px; }
    .code { font-family: monospace; background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 4px; }
    .flow-diagram { font-family: monospace; white-space: pre; margin: 20px 0; }
    h2, h3 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 30px; }
    .file-box { border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 4px; }
    .file-header { background: #f5f5f5; padding: 5px; margin: -10px -10px 10px -10px; border-bottom: 1px solid #ddd; }
</style>";
echo "</head><body>";

echo "<h1>Login System Analysis</h1>";
echo "<p>This tool analyzes your login files to determine the authentication flow without modifying any files.</p>";

// Define key files to analyze
$login_files = [
    'admin-login.php',
    'admin-login.html',
    'admin-login-process.php',
    'admin-auth.php',
    'admin-dashboard.php',
    'playground/admin-login.php',
    'playground/admin-login.html',
    'playground/admin-login-process.php',
    'playground/admin-auth.php',
    'playground/admin-dashboard.php',
    'playground/complete-admin-login.php'
];

// Analysis results
$file_contents = [];
$file_exists = [];
$login_forms = [];
$session_variables = [];
$redirects = [];
$includes = [];
$function_defs = [];
$login_flow = [];

// Function to safely get file content
function get_file_content($file) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    $full_path = $root . '/' . $file;
    
    if (file_exists($full_path)) {
        return [
            'exists' => true,
            'content' => file_get_contents($full_path),
            'size' => filesize($full_path),
            'modified' => date('Y-m-d H:i:s', filemtime($full_path))
        ];
    }
    
    return ['exists' => false];
}

// Function to extract login forms
function extract_login_form($content) {
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>.*?<\/form>/is', $content, $matches)) {
        $form_html = $matches[0];
        $action = $matches[1];
        
        // Extract input fields
        preg_match_all('/<input[^>]*name=["\']([^"\']*)["\'][^>]*>/i', $form_html, $input_matches);
        $inputs = $input_matches[1] ?? [];
        
        return [
            'action' => $action,
            'inputs' => $inputs
        ];
    }
    return null;
}

// Function to extract session variables
function extract_session_vars($content) {
    $vars = [];
    if (preg_match_all('/\$_SESSION\[[\'"]([^\'"]+)[\'"]\]\s*=\s*([^;]+);/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $vars[$match[1]] = trim($match[2]);
        }
    }
    return $vars;
}

// Function to extract redirects
function extract_redirects($content) {
    $redirects = [];
    if (preg_match_all('/header\s*\(\s*[\'"](Location|Refresh):\s*([^;]+)/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $redirects[] = trim($match[2], '\'" ');
        }
    }
    return $redirects;
}

// Function to extract includes and requires
function extract_includes($content) {
    $includes = [];
    if (preg_match_all('/(include|require)(?:_once)?\s*\(?\s*[\'"]([^\'"]+)[\'"]\s*\)?;/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $includes[] = $match[2];
        }
    }
    return $includes;
}

// Function to extract function definitions
function extract_functions($content) {
    $functions = [];
    if (preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $matches)) {
        $functions = $matches[1];
    }
    return $functions;
}

// Analyze each file
foreach ($login_files as $file) {
    $file_data = get_file_content($file);
    $file_exists[$file] = $file_data['exists'];
    
    if ($file_data['exists']) {
        $file_contents[$file] = $file_data;
        
        // Extract important information
        $login_forms[$file] = extract_login_form($file_data['content']);
        $session_variables[$file] = extract_session_vars($file_data['content']);
        $redirects[$file] = extract_redirects($file_data['content']);
        $includes[$file] = extract_includes($file_data['content']);
        $function_defs[$file] = extract_functions($file_data['content']);
    }
}

// Display file existence results
echo "<h2>1. File Existence Check</h2>";
echo "<ul>";
foreach ($file_exists as $file => $exists) {
    if ($exists) {
        echo "<li class='success'>$file exists (Size: " . number_format($file_contents[$file]['size']/1024, 2) . " KB, Modified: " . $file_contents[$file]['modified'] . ")</li>";
    } else {
        echo "<li class='error'>$file does not exist</li>";
    }
}
echo "</ul>";

// Display login forms
echo "<h2>2. Login Forms Analysis</h2>";
foreach ($login_forms as $file => $form) {
    if ($form) {
        echo "<div class='file-box'>";
        echo "<div class='file-header'>$file</div>";
        echo "<p>Form Action: <span class='highlight'>" . htmlspecialchars($form['action']) . "</span></p>";
        echo "<p>Input Fields: " . implode(', ', $form['inputs']) . "</p>";
        echo "</div>";
    }
}

// Display session variables
echo "<h2>3. Session Variables Set</h2>";
foreach ($session_variables as $file => $vars) {
    if (!empty($vars)) {
        echo "<div class='file-box'>";
        echo "<div class='file-header'>$file</div>";
        echo "<ul>";
        foreach ($vars as $key => $value) {
            echo "<li><strong>$key</strong> = " . htmlspecialchars($value) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

// Display redirects
echo "<h2>4. Redirect Analysis</h2>";
foreach ($redirects as $file => $redirect_list) {
    if (!empty($redirect_list)) {
        echo "<div class='file-box'>";
        echo "<div class='file-header'>$file</div>";
        echo "<ul>";
        foreach ($redirect_list as $redirect) {
            echo "<li>Redirects to: <span class='highlight'>" . htmlspecialchars($redirect) . "</span></li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

// Display includes
echo "<h2>5. File Dependencies</h2>";
foreach ($includes as $file => $include_list) {
    if (!empty($include_list)) {
        echo "<div class='file-box'>";
        echo "<div class='file-header'>$file</div>";
        echo "<ul>";
        foreach ($include_list as $include) {
            echo "<li>Includes: <span class='highlight'>" . htmlspecialchars($include) . "</span></li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

// Display function definitions
echo "<h2>6. Authentication Functions</h2>";
foreach ($function_defs as $file => $functions) {
    if (!empty($functions)) {
        echo "<div class='file-box'>";
        echo "<div class='file-header'>$file</div>";
        echo "<ul>";
        foreach ($functions as $function) {
            if (strpos($function, 'login') !== false || 
                strpos($function, 'auth') !== false || 
                strpos($function, 'session') !== false) {
                echo "<li class='highlight'>" . htmlspecialchars($function) . "()</li>";
            } else {
                echo "<li>" . htmlspecialchars($function) . "()</li>";
            }
        }
        echo "</ul>";
        echo "</div>";
    }
}

// Determine primary login flow
echo "<h2>7. Authentication Flow Map</h2>";

// Start with login HTML files
$login_html_files = [];
foreach ($login_forms as $file => $form) {
    if ($form) {
        $login_html_files[$file] = $form['action'];
    }
}

echo "<div class='flow-diagram'>";

// Create a flow diagram
foreach ($login_html_files as $file => $action) {
    echo "$file → $action";
    
    // Follow the process file
    if (isset($file_exists[$action]) && $file_exists[$action]) {
        echo " (exists)\n";
        
        // Check for redirects from the process file
        if (!empty($redirects[$action])) {
            foreach ($redirects[$action] as $redirect) {
                echo "    └── $action → $redirect\n";
                
                // Check if the redirect target exists
                if (isset($file_exists[$redirect]) && $file_exists[$redirect]) {
                    echo "        └── $redirect (exists)\n";
                } else {
                    echo "        └── $redirect (not found)\n";
                }
            }
        }
    } else {
        echo " (not found)\n";
    }
}

echo "</div>";

// Critical Auth Functions
echo "<h2>8. Critical Authentication Functions</h2>";

// Look for admin_login, require_admin_auth, and is_admin_logged_in functions
$auth_functions = [];
foreach ($function_defs as $file => $functions) {
    foreach ($functions as $function) {
        if ($function === 'admin_login' || 
            $function === 'require_admin_auth' || 
            $function === 'is_admin_logged_in' || 
            $function === 'require_admin_role') {
            if (!isset($auth_functions[$function])) {
                $auth_functions[$function] = [];
            }
            $auth_functions[$function][] = $file;
        }
    }
}

echo "<ul>";
foreach ($auth_functions as $function => $files) {
    echo "<li><strong>$function()</strong> found in: " . implode(', ', $files) . "</li>";
}
echo "</ul>";

// Function Usage Analysis
echo "<h2>9. Function Usage Analysis</h2>";

// Find where auth functions are called
$function_usage = [];
foreach ($auth_functions as $function => $files) {
    $function_usage[$function] = [];
    
    foreach ($file_contents as $file => $data) {
        if (preg_match_all('/\b' . preg_quote($function) . '\s*\([^\)]*\)/i', $data['content'], $matches)) {
            if (!in_array($file, $files)) { // Don't include definitions
                $function_usage[$function][] = $file;
            }
        }
    }
}

echo "<ul>";
foreach ($function_usage as $function => $files) {
    if (!empty($files)) {
        echo "<li><strong>$function()</strong> is called in: " . implode(', ', $files) . "</li>";
    } else {
        echo "<li><strong>$function()</strong> is not called in any of the analyzed files</li>";
    }
}
echo "</ul>";

// Complete-admin-login.php Analysis
echo "<h2>10. All-in-One Solution Analysis</h2>";

if ($file_exists['playground/complete-admin-login.php']) {
    // Check if it contains all necessary functionality
    $all_in_one = $file_contents['playground/complete-admin-login.php'];
    $missing_features = [];
    
    // Check for form processing
    if (strpos($all_in_one['content'], 'REQUEST_METHOD') === false) {
        $missing_features[] = "Form processing (REQUEST_METHOD check)";
    }
    
    // Check for database connection
    if (strpos($all_in_one['content'], 'PDO') === false && 
        strpos($all_in_one['content'], 'mysqli') === false) {
        $missing_features[] = "Database connection";
    }
    
    // Check for session initialization
    if (strpos($all_in_one['content'], 'session_start') === false) {
        $missing_features[] = "Session initialization";
    }
    
    // Check for redirect to dashboard
    if (strpos($all_in_one['content'], 'admin-dashboard.php') === false) {
        $missing_features[] = "Redirect to dashboard";
    }
    
    // Check for session variables
    $aio_session_vars = extract_session_vars($all_in_one['content']);
    $required_vars = ['admin_logged_in', 'admin_id', 'admin_username', 'admin_role'];
    foreach ($required_vars as $var) {
        if (!isset($aio_session_vars[$var])) {
            $missing_features[] = "Session variable: $var";
        }
    }
    
    if (empty($missing_features)) {
        echo "<p class='success'>playground/complete-admin-login.php contains all necessary login functionality!</p>";
    } else {
        echo "<p class='warning'>playground/complete-admin-login.php is missing some features:</p>";
        echo "<ul>";
        foreach ($missing_features as $feature) {
            echo "<li>$feature</li>";
        }
        echo "</ul>";
    }
    
    // Show key parts of the file
    echo "<h3>Key Components:</h3>";
    
    // Extract form processing code
    if (preg_match('/if\s*\(\$_SERVER\[\'REQUEST_METHOD\'\]\s*===\s*\'POST\'\)\s*{.*?}/is', $all_in_one['content'], $matches)) {
        echo "<div class='code'>";
        echo htmlspecialchars(substr($matches[0], 0, 500) . (strlen($matches[0]) > 500 ? '...' : ''));
        echo "</div>";
    }
    
    // Extract session variables being set
    echo "<h4>Session Variables Set:</h4>";
    echo "<ul>";
    foreach ($aio_session_vars as $key => $value) {
        echo "<li><strong>$key</strong> = " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p class='error'>playground/complete-admin-login.php does not exist or couldn't be accessed.</p>";
}

// Recommendations
echo "<h2>11. Implementation Recommendations</h2>";

echo "<ol>";
echo "<li><strong>Primary Login Flow:</strong> ";

// Determine which login form/process is actually used
$primary_login = "unknown";
foreach ($login_html_files as $file => $action) {
    if (isset($file_exists[$action]) && $file_exists[$action]) {
        $primary_login = "$file → $action";
        break;
    }
}

echo "From analysis, your primary login flow appears to be: <span class='highlight'>$primary_login</span></li>";

// Check if complete-admin-login.php has all needed functionality
if ($file_exists['playground/complete-admin-login.php'] && empty($missing_features)) {
    echo "<li class='success'><strong>Use All-in-One Solution:</strong> Your complete-admin-login.php contains all necessary functionality!</li>";
} else {
    echo "<li class='warning'><strong>Enhance All-in-One Solution:</strong> Your complete-admin-login.php needs some enhancements.</li>";
}

echo "<li><strong>Implementation Steps:</strong>
    <ol>
        <li>Update database credentials in your all-in-one solution</li>
        <li>Replace the current admin-login.php with your enhanced solution</li>
        <li>Rename admin-login.html to admin-login.html.bak</li>
        <li>Rename admin-login-process.php to admin-login-process.php.bak</li>
        <li>Test the new login system thoroughly</li>
    </ol>
</li>";

echo "</ol>";

// Database credentials found
$db_credentials = [];
foreach ($file_contents as $file => $data) {
    if (preg_match('/\$db_host\s*=\s*[\'"]([^\'"]+)[\'"]/i', $data['content'], $matches)) {
        $db_credentials['host'] = $matches[1];
    }
    
    if (preg_match('/\$db_name\s*=\s*[\'"]([^\'"]+)[\'"]/i', $data['content'], $matches)) {
        $db_credentials['name'] = $matches[1];
    }
    
    if (preg_match('/\$db_user\s*=\s*[\'"]([^\'"]+)[\'"]/i', $data['content'], $matches)) {
        $db_credentials['user'] = $matches[1];
    }
    
    if (preg_match('/\$db_pass\s*=\s*[\'"]([^\'"]+)[\'"]/i', $data['content'], $matches)) {
        $db_credentials['pass'] = $matches[1];
    }
    
    // If we have all credentials, break
    if (count($db_credentials) == 4) {
        break;
    }
}

if (!empty($db_credentials)) {
    echo "<h3>Database Credentials Found:</h3>";
    echo "<pre class='code'>";
    echo "\$db_host = '" . ($db_credentials['host'] ?? 'localhost') . "';\n";
    echo "\$db_name = '" . ($db_credentials['name'] ?? '') . "';\n";
    echo "\$db_user = '" . ($db_credentials['user'] ?? '') . "';\n";
    echo "\$db_pass = '" . ($db_credentials['pass'] ?? '') . "';";
    echo "</pre>";
}

echo "</body></html>";
?>
