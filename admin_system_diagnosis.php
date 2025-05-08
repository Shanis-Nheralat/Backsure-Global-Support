<?php
/**
 * Comprehensive Admin System Diagnostic Test
 * 
 * This script checks all components of the authentication system
 */

// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Admin System Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1 { color: #0b2e59; }
        h2 { color: #0b2e59; margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
        .test-section { margin-bottom: 30px; border: 1px solid #e0e0e0; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Admin System Diagnostic Results</h1>
    <p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Function to display test result
function display_result($test_name, $result, $details = '') {
    $status_class = $result ? 'success' : 'error';
    $status_text = $result ? 'PASSED' : 'FAILED';
    
    echo "<div class='test-section'>";
    echo "<h3>$test_name: <span class='$status_class'>$status_text</span></h3>";
    
    if (!empty($details)) {
        echo "<pre>$details</pre>";
    }
    
    echo "</div>";
    
    return $result;
}

// Store test results
$tests_passed = 0;
$tests_failed = 0;

// 1. PHP Environment
echo "<h2>1. PHP Environment</h2>";

$php_version = phpversion();
$php_version_ok = version_compare($php_version, '7.4.0', '>=');
display_result(
    "PHP Version Check (7.4+ required)", 
    $php_version_ok, 
    "Current PHP version: $php_version"
);
$php_version_ok ? $tests_passed++ : $tests_failed++;

$extensions = ['pdo', 'pdo_mysql', 'json', 'session'];
$missing_extensions = [];

foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

$extensions_ok = empty($missing_extensions);
display_result(
    "Required PHP Extensions", 
    $extensions_ok, 
    $extensions_ok ? "All required extensions are installed" : "Missing extensions: " . implode(', ', $missing_extensions)
);
$extensions_ok ? $tests_passed++ : $tests_failed++;

// 2. File System Check
echo "<h2>2. File System Check</h2>";

$required_files = [
    'db.php' => 'Database connection',
    'admin-auth.php' => 'Authentication system',
    'login.php' => 'Login page',
    'forgot-password.php' => 'Forgot password page',
    'reset-password.php' => 'Reset password page',
    'logout.php' => 'Logout processing',
    'admin-dashboard.php' => 'Admin dashboard'
];

echo "<table>
        <tr>
            <th>File</th>
            <th>Description</th>
            <th>Status</th>
            <th>Last Modified</th>
            <th>Size</th>
        </tr>";

$all_files_exist = true;
foreach ($required_files as $file => $description) {
    $exists = file_exists($file);
    if (!$exists) {
        $all_files_exist = false;
    }
    
    $last_modified = $exists ? date("Y-m-d H:i:s", filemtime($file)) : 'N/A';
    $size = $exists ? filesize($file) . ' bytes' : 'N/A';
    
    echo "<tr>
            <td>$file</td>
            <td>$description</td>
            <td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? 'Found' : 'Missing') . "</td>
            <td>$last_modified</td>
            <td>$size</td>
          </tr>";
}
echo "</table>";

display_result("Required Files Check", $all_files_exist);
$all_files_exist ? $tests_passed++ : $tests_failed++;

// 3. Database Connection Test
echo "<h2>3. Database Connection Test</h2>";

// Try to include db.php if it exists
$db_test_result = false;
$db_error_message = '';

if (file_exists('db.php')) {
    try {
        // Include the file in a controlled way to capture errors
        ob_start();
        include 'db.php';
        ob_end_clean();
        
        // Check if $pdo exists and is a PDO instance
        if (isset($pdo) && $pdo instanceof PDO) {
            // Test the connection
            $stmt = $pdo->query("SELECT 'Connection working' AS test");
            $result = $stmt->fetch();
            
            if ($result && $result['test'] === 'Connection working') {
                $db_test_result = true;
                
                // Get database information
                $db_info = $pdo->query("SELECT version() AS version, database() AS db_name")->fetch();
                $db_error_message = "Connected to database: " . $db_info['db_name'] . 
                                   "\nDatabase version: " . $db_info['version'];
                
                // Check tables
                $tables_query = $pdo->query("SHOW TABLES");
                $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
                
                $db_error_message .= "\n\nDatabase tables found: " . count($tables) . 
                                    "\n" . implode(", ", $tables);
            }
        } else {
            $db_error_message = "db.php was included but no valid PDO connection was established.";
        }
    } catch (Exception $e) {
        $db_error_message = "Error connecting to database: " . $e->getMessage();
    }
} else {
    $db_error_message = "db.php file not found. Cannot test database connection.";
}

display_result("Database Connection", $db_test_result, $db_error_message);
$db_test_result ? $tests_passed++ : $tests_failed++;

// 4. Authentication System Test
echo "<h2>4. Authentication System Test</h2>";

$auth_test_result = false;
$auth_error_message = '';

if (file_exists('admin-auth.php') && isset($pdo) && $pdo instanceof PDO) {
    try {
        // Test if auth functions exist without actually including the file again
        // (it might have already been included by db.php)
        if (!function_exists('is_admin_logged_in')) {
            ob_start();
            include_once 'admin-auth.php';
            ob_end_clean();
        }
        
        // Check if key auth functions exist
        $required_functions = [
            'is_admin_logged_in',
            'require_admin_auth',
            'require_admin_role',
            'admin_login',
            'admin_logout',
            'get_admin_user',
            'check_session_validity'
        ];
        
        $missing_functions = [];
        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                $missing_functions[] = $function;
            }
        }
        
        if (empty($missing_functions)) {
            $auth_test_result = true;
            $auth_error_message = "Authentication system looks good. All required functions found.";
            
            // Check users table
            try {
                $users_query = $pdo->query("SELECT COUNT(*) as count FROM users");
                $users_count = $users_query->fetch()['count'];
                $auth_error_message .= "\n\nUsers in database: $users_count";
                
                // Check admin user
                $admin_query = $pdo->query("SELECT * FROM users WHERE username = 'shanisbsg' LIMIT 1");
                $admin_user = $admin_query->fetch();
                
                if ($admin_user) {
                    $auth_error_message .= "\nAdmin user 'shanisbsg' found with role: " . $admin_user['role'];
                } else {
                    $auth_error_message .= "\nWARNING: Admin user 'shanisbsg' not found.";
                }
            } catch (Exception $e) {
                $auth_error_message .= "\n\nError checking users table: " . $e->getMessage();
            }
        } else {
            $auth_error_message = "Missing authentication functions: " . implode(', ', $missing_functions);
        }
    } catch (Exception $e) {
        $auth_error_message = "Error testing authentication system: " . $e->getMessage();
    }
} else {
    $auth_error_message = "Cannot test authentication system. Missing required files or database connection.";
}

display_result("Authentication System", $auth_test_result, $auth_error_message);
$auth_test_result ? $tests_passed++ : $tests_failed++;

// 5. Session Management Test
echo "<h2>5. Session Management Test</h2>";

$session_test_result = false;
$session_error_message = '';

try {
    // Check session status
    $session_status = session_status();
    
    switch ($session_status) {
        case PHP_SESSION_DISABLED:
            $session_error_message = "Sessions are disabled in PHP configuration.";
            break;
        case PHP_SESSION_NONE:
            // Try to start a session
            session_start();
            $session_test_result = true;
            $session_error_message = "Session started successfully.";
            break;
        case PHP_SESSION_ACTIVE:
            $session_test_result = true;
            $session_error_message = "Session already active.";
            break;
    }
    
    // Check session configuration
    $session_error_message .= "\n\nSession configuration:";
    $session_error_message .= "\nSession name: " . session_name();
    $session_error_message .= "\nSession ID: " . session_id();
    $session_error_message .= "\nSession cookie parameters: " . print_r(session_get_cookie_params(), true);
    
    // Check current session data
    $session_error_message .= "\n\nCurrent session data:";
    $session_error_message .= "\n" . print_r($_SESSION, true);
    
} catch (Exception $e) {
    $session_error_message = "Error testing sessions: " . $e->getMessage();
}

display_result("Session Management", $session_test_result, $session_error_message);
$session_test_result ? $tests_passed++ : $tests_failed++;

// 6. Login Form Check
echo "<h2>6. Login Form Check</h2>";

$login_form_test_result = false;
$login_form_error_message = '';

if (file_exists('login.php')) {
    try {
        // Read the login.php file
        $login_content = file_get_contents('login.php');
        
        // Check for form elements
        $has_form = strpos($login_content, '<form') !== false;
        $has_username = strpos($login_content, 'name="username"') !== false;
        $has_password = strpos($login_content, 'name="password"') !== false;
        $has_submit = strpos($login_content, 'type="submit"') !== false;
        
        // Check for required PHP logic
        $has_post_check = strpos($login_content, '$_SERVER[\'REQUEST_METHOD\'] === \'POST\'') !== false;
        $has_admin_login = strpos($login_content, 'admin_login') !== false;
        
        if ($has_form && $has_username && $has_password && $has_submit && $has_post_check) {
            $login_form_test_result = true;
            $login_form_error_message = "Login form appears to be correctly implemented.";
            
            // Additional details
            $login_form_error_message .= "\n\nForm elements found:";
            $login_form_error_message .= "\n- Form tag: " . ($has_form ? 'Yes' : 'No');
            $login_form_error_message .= "\n- Username field: " . ($has_username ? 'Yes' : 'No');
            $login_form_error_message .= "\n- Password field: " . ($has_password ? 'Yes' : 'No');
            $login_form_error_message .= "\n- Submit button: " . ($has_submit ? 'Yes' : 'No');
            $login_form_error_message .= "\n- POST method check: " . ($has_post_check ? 'Yes' : 'No');
            $login_form_error_message .= "\n- Admin login function: " . ($has_admin_login ? 'Yes' : 'No');
        } else {
            $login_form_error_message = "Login form is missing required elements:";
            if (!$has_form) $login_form_error_message .= "\n- Missing form tag";
            if (!$has_username) $login_form_error_message .= "\n- Missing username field";
            if (!$has_password) $login_form_error_message .= "\n- Missing password field";
            if (!$has_submit) $login_form_error_message .= "\n- Missing submit button";
            if (!$has_post_check) $login_form_error_message .= "\n- Missing POST method check";
            if (!$has_admin_login) $login_form_error_message .= "\n- Missing admin_login function call";
        }
    } catch (Exception $e) {
        $login_form_error_message = "Error checking login form: " . $e->getMessage();
    }
} else {
    $login_form_error_message = "login.php file not found. Cannot check login form.";
}

display_result("Login Form", $login_form_test_result, $login_form_error_message);
$login_form_test_result ? $tests_passed++ : $tests_failed++;

// 7. Security Features Check
echo "<h2>7. Security Features Check</h2>";

$security_test_result = false;
$security_error_message = '';

try {
    // Read auth and login files
    $auth_content = file_exists('admin-auth.php') ? file_get_contents('admin-auth.php') : '';
    $login_content = file_exists('login.php') ? file_get_contents('login.php') : '';
    
    // Check for security features
    $has_password_hash = strpos($auth_content, 'password_hash') !== false || 
                         strpos($login_content, 'password_hash') !== false;
    
    $has_password_verify = strpos($auth_content, 'password_verify') !== false || 
                           strpos($login_content, 'password_verify') !== false;
    
    $has_prepared_statements = (strpos($auth_content, '->prepare') !== false || 
                               strpos($login_content, '->prepare') !== false);
    
    $has_csrf_protection = (strpos($auth_content, 'csrf_token') !== false || 
                           strpos($login_content, 'csrf_token') !== false);
    
    $has_session_timeout = strpos($auth_content, 'check_session_validity') !== false || 
                          strpos($auth_content, 'session_timeout') !== false;
    
    $has_brute_force = (strpos($auth_content, 'login_attempts') !== false || 
                       strpos($login_content, 'login_attempts') !== false);
    
    $has_secure_cookies = (strpos($auth_content, 'httponly') !== false || 
                          strpos($auth_content, 'secure') !== false);
    
    // Overall security assessment
    $security_score = 0;
    if ($has_password_hash) $security_score++;
    if ($has_password_verify) $security_score++;
    if ($has_prepared_statements) $security_score++;
    if ($has_csrf_protection) $security_score++;
    if ($has_session_timeout) $security_score++;
    if ($has_brute_force) $security_score++;
    if ($has_secure_cookies) $security_score++;
    
    $security_test_result = $security_score >= 5; // At least 5 security features
    
    $security_error_message = "Security features detected:";
    $security_error_message .= "\n- Password hashing: " . ($has_password_hash ? 'Yes' : 'No');
    $security_error_message .= "\n- Password verification: " . ($has_password_verify ? 'Yes' : 'No');
    $security_error_message .= "\n- Prepared statements: " . ($has_prepared_statements ? 'Yes' : 'No');
    $security_error_message .= "\n- CSRF protection: " . ($has_csrf_protection ? 'Yes' : 'No');
    $security_error_message .= "\n- Session timeout: " . ($has_session_timeout ? 'Yes' : 'No');
    $security_error_message .= "\n- Brute force protection: " . ($has_brute_force ? 'Yes' : 'No');
    $security_error_message .= "\n- Secure cookies: " . ($has_secure_cookies ? 'Yes' : 'No');
    
    $security_error_message .= "\n\nSecurity score: $security_score/7";
    
    if ($security_score < 5) {
        $security_error_message .= "\n\nWARNING: Some important security features are missing.";
    }
    
} catch (Exception $e) {
    $security_error_message = "Error checking security features: " . $e->getMessage();
}

display_result("Security Features", $security_test_result, $security_error_message);
$security_test_result ? $tests_passed++ : $tests_failed++;

// 8. Overall Summary
echo "<h2>8. Summary</h2>";

$total_tests = $tests_passed + $tests_failed;
$pass_percentage = ($total_tests > 0) ? round(($tests_passed / $total_tests) * 100) : 0;

echo "<p>Total tests: $total_tests</p>";
echo "<p>Tests passed: <span class='success'>$tests_passed</span></p>";
echo "<p>Tests failed: <span class='error'>$tests_failed</span></p>";
echo "<p>Success rate: $pass_percentage%</p>";

// Add troubleshooting recommendations based on failed tests
echo "<h2>9. Recommendations</h2>";

if ($tests_failed == 0) {
    echo "<p class='success'>All systems are working correctly! No fixes needed.</p>";
} else {
    echo "<p>Based on the diagnostic results, here are some recommendations:</p>";
    echo "<ul>";
    
    if (!$db_test_result) {
        echo "<li><strong>Database Connection Issue:</strong> 
              Check your db.php file to ensure it has the correct credentials. Currently, the system 
              cannot connect to the database. Verify that the database exists and the user has proper permissions.</li>";
    }
    
    if (!$auth_test_result) {
        echo "<li><strong>Authentication System Issue:</strong> 
              The authentication system is not functioning correctly. Check if admin-auth.php 
              is properly implemented and includes all required functions.</li>";
    }
    
    if (!$session_test_result) {
        echo "<li><strong>Session Management Issue:</strong> 
              Sessions are not working correctly. Check your PHP configuration and ensure 
              session handling is enabled.</li>";
    }
    
    if (!$login_form_test_result) {
        echo "<li><strong>Login Form Issue:</strong> 
              The login form is missing required elements. Check login.php to ensure it has 
              all necessary form fields and processing logic.</li>";
    }
    
    if (!$security_test_result) {
        echo "<li><strong>Security Features Issue:</strong> 
              Some important security features are missing. Consider implementing password hashing, 
              prepared statements, CSRF protection, session timeout checks, and brute force protection.</li>";
    }
    
    if (!$all_files_exist) {
        echo "<li><strong>Missing Files:</strong> 
              Some required files are missing. Ensure all core files (db.php, admin-auth.php, login.php, etc.) 
              are present in the correct location.</li>";
    }
    
    echo "</ul>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Address the database connection issue first, as most other functions depend on this.</li>";
    echo "<li>Ensure all required files are present and contain the necessary code.</li>";
    echo "<li>Check the authentication system implementation.</li>";
    echo "<li>Verify session management and security features.</li>";
    echo "<li>Test the login form.</li>";
    echo "</ol>";
}

echo "</body></html>";
?>
