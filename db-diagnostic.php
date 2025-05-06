<?php
/**
 * Database Connection Diagnostic Test
 * This file tests various aspects of your database connection
 */

// Enable error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Test 1: Direct Database Connection
function test_direct_connection() {
    echo "<h3>Test 1: Direct Database Connection</h3>";
    
    $db_host = 'localhost';
    $db_name = 'backzvsg_playground';
    $db_user = 'backzvsg_site';
    $db_pass = 'Pc*C^y]_ZnzU';
    
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p class='success'>✓ Direct connection successful!</p>";
        
        // Run a test query
        $stmt = $pdo->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Connected to database: <strong>" . $result['db_name'] . "</strong></p>";
        
        return ['success' => true, 'connection' => $pdo];
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Direct connection failed: " . $e->getMessage() . "</p>";
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Test 2: Connection via db_config.php
function test_db_config_connection() {
    echo "<h3>Test 2: Connection via db_config.php</h3>";
    
    if (!file_exists('db_config.php')) {
        echo "<p class='error'>✗ db_config.php file not found!</p>";
        return ['success' => false, 'error' => 'File not found'];
    }
    
    // Include the file and check for errors
    try {
        // Capture any output the file might generate
        ob_start();
        require_once 'db_config.php';
        $output = ob_get_clean();
        
        if ($output) {
            echo "<p class='warning'>⚠️ db_config.php produced output: " . htmlspecialchars($output) . "</p>";
        }
        
        echo "<p class='success'>✓ db_config.php included successfully</p>";
        
        // Check if global variables are set
        echo "<h4>Database Variables:</h4>";
        echo "<ul>";
        echo "<li>db_host: " . (isset($db_host) ? htmlspecialchars($db_host) : '<span class="error">Not set</span>') . "</li>";
        echo "<li>db_name: " . (isset($db_name) ? htmlspecialchars($db_name) : '<span class="error">Not set</span>') . "</li>";
        echo "<li>db_user: " . (isset($db_user) ? htmlspecialchars($db_user) : '<span class="error">Not set</span>') . "</li>";
        echo "<li>db_pass: " . (isset($db_pass) ? '********' : '<span class="error">Not set</span>') . "</li>";
        echo "</ul>";
        
        // Check if get_db_connection function exists
        if (function_exists('get_db_connection')) {
            echo "<p class='success'>✓ get_db_connection() function exists</p>";
            
            // Test the function
            try {
                $db = get_db_connection();
                echo "<p class='success'>✓ get_db_connection() returned a valid connection</p>";
                
                // Run a test query
                $stmt = $db->query("SELECT DATABASE() as db_name");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>Connected to database: <strong>" . $result['db_name'] . "</strong></p>";
                
                return ['success' => true, 'connection' => $db];
            } catch (PDOException $e) {
                echo "<p class='error'>✗ get_db_connection() failed: " . $e->getMessage() . "</p>";
                
                // Get the source code of the function to diagnose the issue
                $db_config_content = file_get_contents('db_config.php');
                if (preg_match('/function\s+get_db_connection\s*\(\s*\)\s*{(.+?)}/s', $db_config_content, $matches)) {
                    echo "<h4>get_db_connection() Source Code:</h4>";
                    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
                }
                
                return ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            echo "<p class='error'>✗ get_db_connection() function does not exist!</p>";
            return ['success' => false, 'error' => 'Function not found'];
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error including db_config.php: " . $e->getMessage() . "</p>";
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Test 3: Check config.php for overrides
function test_config_php() {
    echo "<h3>Test 3: Check config.php for Overrides</h3>";
    
    if (!file_exists('config.php')) {
        echo "<p class='error'>✗ config.php file not found!</p>";
        return ['success' => false, 'error' => 'File not found'];
    }
    
    // Read the file content
    $config_content = file_get_contents('config.php');
    
    // Look for database credentials
    $has_credentials = false;
    $credentials_commented = false;
    
    if (preg_match('/define\s*\(\s*[\'"]DB_USER[\'"]\s*,\s*[\'"]root[\'"]\s*\)/i', $config_content)) {
        $has_credentials = true;
        echo "<p class='error'>✗ Found active 'root' database user credential in config.php</p>";
    }
    
    if (preg_match('/\/\*.*define\s*\(\s*[\'"]DB_USER[\'"]\s*,.*\*\//is', $config_content)) {
        $credentials_commented = true;
        echo "<p class='success'>✓ Database credentials are properly commented out in config.php</p>";
    }
    
    // Display relevant code section
    $lines = explode("\n", $config_content);
    $relevant_lines = [];
    $in_db_section = false;
    
    foreach ($lines as $i => $line) {
        if (strpos($line, 'DB_') !== false || strpos($line, 'database') !== false) {
            $in_db_section = true;
            $relevant_lines[] = 'Line ' . ($i + 1) . ': ' . htmlspecialchars($line);
        } else if ($in_db_section && trim($line) === '') {
            $in_db_section = false;
        } else if ($in_db_section) {
            $relevant_lines[] = 'Line ' . ($i + 1) . ': ' . htmlspecialchars($line);
        }
    }
    
    if ($relevant_lines) {
        echo "<h4>Database Section in config.php:</h4>";
        echo "<pre>" . implode("\n", $relevant_lines) . "</pre>";
    }
    
    return [
        'success' => true,
        'has_credentials' => $has_credentials,
        'credentials_commented' => $credentials_commented
    ];
}

// Test 4: Check User Record
function test_user_record($pdo) {
    echo "<h3>Test 4: Check User Record</h3>";
    
    if (!$pdo) {
        echo "<p class='error'>✗ No database connection available to check user</p>";
        return ['success' => false, 'error' => 'No connection'];
    }
    
    try {
        // Check if admins table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
        $table_exists = $stmt->fetchColumn();
        
        if (!$table_exists) {
            echo "<p class='error'>✗ 'admins' table does not exist!</p>";
            return ['success' => false, 'error' => 'Table not found'];
        }
        
        echo "<p class='success'>✓ 'admins' table exists</p>";
        
        // Get user record
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute(['shanisbsg', 'shanis@backsureglobalsupport.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "<p class='error'>✗ User 'shanisbsg' not found in database</p>";
            return ['success' => false, 'error' => 'User not found'];
        }
        
        echo "<p class='success'>✓ Found user 'shanisbsg' in database</p>";
        
        // Display user information without the password
        $safe_user = $user;
        if (isset($safe_user['password'])) {
            $safe_user['password'] = substr($safe_user['password'], 0, 10) . '...';
        }
        
        echo "<h4>User Information:</h4>";
        echo "<pre>";
        print_r($safe_user);
        echo "</pre>";
        
        // Check role value
        if (empty($user['role'])) {
            echo "<p class='warning'>⚠️ User has no role assigned!</p>";
            
            // Suggest SQL to fix
            echo "<div class='fix-suggestion'>";
            echo "<h4>Fix Missing Role:</h4>";
            echo "<p>Run this SQL query to set the role:</p>";
            echo "<pre>UPDATE admins SET role = 'admin' WHERE username = 'shanisbsg';</pre>";
            echo "</div>";
        } else {
            echo "<p class='success'>✓ User has role: " . htmlspecialchars($user['role']) . "</p>";
        }
        
        // Verify password for the provided credentials
        if (isset($user['password'])) {
            $is_password_valid = password_verify('a14c65f3', $user['password']);
            if ($is_password_valid) {
                echo "<p class='success'>✓ Password 'a14c65f3' is valid for this user</p>";
            } else {
                echo "<p class='error'>✗ Password 'a14c65f3' is NOT valid for this user</p>";
                
                // Suggest SQL to fix if needed
                echo "<div class='fix-suggestion'>";
                echo "<h4>Fix Password:</h4>";
                echo "<p>Run this SQL query to reset the password:</p>";
                echo "<pre>UPDATE admins SET password = '" . password_hash('a14c65f3', PASSWORD_DEFAULT) . "' WHERE username = 'shanisbsg';</pre>";
                echo "</div>";
            }
        }
        
        return [
            'success' => true,
            'user' => $safe_user,
            'has_role' => !empty($user['role']),
            'valid_password' => $is_password_valid ?? false
        ];
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Error checking user record: " . $e->getMessage() . "</p>";
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Test 5: Check admin-auth.php
function test_admin_auth() {
    echo "<h3>Test 5: Check admin-auth.php</h3>";
    
    if (!file_exists('admin-auth.php')) {
        echo "<p class='error'>✗ admin-auth.php file not found!</p>";
        return ['success' => false, 'error' => 'File not found'];
    }
    
    // Read the file content
    $auth_content = file_get_contents('admin-auth.php');
    
    // Check for is_admin_logged_in function
    if (preg_match('/function\s+is_admin_logged_in\s*\(\s*\)\s*{(.+?)}/s', $auth_content, $matches)) {
        echo "<p class='success'>✓ Found is_admin_logged_in() function</p>";
        
        $function_code = $matches[1];
        
        // Check how it verifies login status
        $uses_strict_check = strpos($function_code, '===') !== false;
        $handles_multiple_values = strpos($function_code, '||') !== false;
        $checks_for_numeric_one = strpos($function_code, '1') !== false;
        
        echo "<h4>is_admin_logged_in() Details:</h4>";
        echo "<ul>";
        echo "<li>Uses strict equality (===): " . ($uses_strict_check ? 'Yes' : 'No') . "</li>";
        echo "<li>Handles multiple values (||): " . ($handles_multiple_values ? 'Yes' : 'No') . "</li>";
        echo "<li>Checks for numeric value (1): " . ($checks_for_numeric_one ? 'Yes' : 'No') . "</li>";
        echo "</ul>";
        
        // Show the function code
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
        
        // Check for potential issues
        if ($uses_strict_check && !$handles_multiple_values) {
            echo "<p class='warning'>⚠️ Function uses strict equality but doesn't handle multiple values</p>";
            echo "<div class='fix-suggestion'>";
            echo "<h4>Suggested Fix:</h4>";
            echo "<pre>function is_admin_logged_in() {
    return isset(\$_SESSION['admin_logged_in']) && 
           (\$_SESSION['admin_logged_in'] === true || 
            \$_SESSION['admin_logged_in'] === 1 || 
            \$_SESSION['admin_logged_in'] === '1');
}</pre>";
            echo "</div>";
        }
    } else {
        echo "<p class='error'>✗ Could not find is_admin_logged_in() function</p>";
    }
    
    return [
        'success' => true,
        'uses_strict_check' => $uses_strict_check ?? false,
        'handles_multiple_values' => $handles_multiple_values ?? false,
        'checks_for_numeric_one' => $checks_for_numeric_one ?? false
    ];
}

// Test 6: Current Session State
function test_session_state() {
    echo "<h3>Test 6: Current Session State</h3>";
    
    echo "<h4>Session Variables:</h4>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    // Check for key session variables
    $admin_logged_in = isset($_SESSION['admin_logged_in']);
    $admin_logged_in_type = gettype($_SESSION['admin_logged_in'] ?? null);
    $admin_logged_in_value = $_SESSION['admin_logged_in'] ?? null;
    
    $admin_role = isset($_SESSION['admin_role']);
    $admin_role_value = $_SESSION['admin_role'] ?? null;
    
    echo "<h4>Key Variables:</h4>";
    echo "<ul>";
    echo "<li>admin_logged_in: " . ($admin_logged_in ? 'Set' : 'Not set');
    if ($admin_logged_in) {
        echo " (Type: " . $admin_logged_in_type . ", Value: " . var_export($admin_logged_in_value, true) . ")";
    }
    echo "</li>";
    
    echo "<li>admin_role: " . ($admin_role ? 'Set' : 'Not set');
    if ($admin_role) {
        echo " (Value: " . var_export($admin_role_value, true) . ")";
    }
    echo "</li>";
    echo "</ul>";
    
    return [
        'admin_logged_in' => $admin_logged_in,
        'admin_logged_in_type' => $admin_logged_in_type,
        'admin_logged_in_value' => $admin_logged_in_value,
        'admin_role' => $admin_role,
        'admin_role_value' => $admin_role_value
    ];
}

// Function to generate solutions based on test results
function generate_solutions($results) {
    echo "<h2>Recommended Solutions</h2>";
    
    $has_issues = false;
    
    // Issue 1: db_config connection failure
    if (!$results['db_config']['success']) {
        $has_issues = true;
        echo "<div class='fix-solution'>";
        echo "<h3>1. Fix Database Connection in db_config.php</h3>";
        echo "<p>Your db_config.php file is failing to establish a database connection.</p>";
        echo "<pre>// Replace your current db_config.php with this:
&lt;?php
/**
 * Database Configuration
 * Central configuration file for database connections.
 */

// Define database credentials directly
\$db_host = 'localhost';
\$db_name = 'backzvsg_playground';
\$db_user = 'backzvsg_site';
\$db_pass = 'Pc*C^y]_ZnzU';

/**
 * Get database connection
 * 
 * @return PDO Database connection
 */
function get_db_connection() {
    global \$db_host, \$db_name, \$db_user, \$db_pass;
    
    try {
        \$dsn = \"mysql:host=\$db_host;dbname=\$db_name;charset=utf8\";
        \$options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        return new PDO(\$dsn, \$db_user, \$db_pass, \$options);
    } catch (PDOException \$e) {
        error_log(\"Database connection error: \" . \$e->getMessage());
        throw new PDOException(\"Database connection failed: \" . \$e->getMessage());
    }
}

// Create PDO instance for backward compatibility
try {
    \$pdo = get_db_connection();
} catch (PDOException \$e) {
    error_log(\"Database connection error in db_config.php: \" . \$e->getMessage());
    \$pdo = null;
}
</pre>";
        echo "</div>";
    }
    
    // Issue 2: Config.php with root credentials
    if ($results['config']['has_credentials'] && !$results['config']['credentials_commented']) {
        $has_issues = true;
        echo "<div class='fix-solution'>";
        echo "<h3>2. Fix Database Credentials in config.php</h3>";
        echo "<p>Comment out the database credentials in config.php:</p>";
        echo "<pre>// Database configuration - COMMENTED OUT since we're using db_config.php
// These are kept as reference only
/*
define('DB_HOST', 'localhost');
define('DB_NAME', 'backzvsg_playground');
define('DB_USER', 'root');
define('DB_PASSWORD', 'password');
*/</pre>";
        echo "</div>";
    }
    
    // Issue 3: Missing or empty role
    if (isset($results['user']['has_role']) && !$results['user']['has_role']) {
        $has_issues = true;
        echo "<div class='fix-solution'>";
        echo "<h3>3. Set Admin Role in Database</h3>";
        echo "<p>Your user record has an empty role, which might be causing permission issues.</p>";
        echo "<p>Run this SQL query to set the admin role:</p>";
        echo "<pre>UPDATE admins SET role = 'admin' WHERE username = 'shanisbsg';</pre>";
        echo "</div>";
    }
    
    // Issue 4: Authentication function issues
    if (isset($results['auth']['uses_strict_check']) && 
        $results['auth']['uses_strict_check'] && 
        !$results['auth']['handles_multiple_values']) {
        $has_issues = true;
        echo "<div class='fix-solution'>";
        echo "<h3>4. Fix is_admin_logged_in() Function</h3>";
        echo "<p>Your is_admin_logged_in() function only checks for boolean true, but your session has a numeric value.</p>";
        echo "<p>Update the function in admin-auth.php:</p>";
        echo "<pre>function is_admin_logged_in() {
    return isset(\$_SESSION['admin_logged_in']) && 
           (\$_SESSION['admin_logged_in'] === true || 
            \$_SESSION['admin_logged_in'] === 1 || 
            \$_SESSION['admin_logged_in'] === '1');
}</pre>";
        echo "</div>";
    }
    
    // Issue 5: Empty admin_role in session
    if (isset($results['session']['admin_role']) && 
        $results['session']['admin_role'] && 
        empty($results['session']['admin_role_value'])) {
        $has_issues = true;
        echo "<div class='fix-solution'>";
        echo "<h3>5. Set Admin Role in Session</h3>";
        echo "<p>Your session has an empty admin_role value, which might be causing permission issues.</p>";
        echo "<p>Create a temporary file called set-role.php with this content:</p>";
        echo "<pre>&lt;?php
session_start();
\$_SESSION['admin_role'] = 'admin';
echo \"Role set to 'admin'. &lt;a href='admin-dashboard.php'&gt;Go to Dashboard&lt;/a&gt;\";
</pre>";
        echo "<p>Access this file in your browser, then click the link to go to the dashboard.</p>";
        echo "<p>Delete this file after successfully accessing the dashboard.</p>";
        echo "</div>";
    }
    
    if (!$has_issues) {
        echo "<p class='success'>No issues found! All tests passed successfully.</p>";
    }
}

// Execute tests and collect results
$direct_result = test_direct_connection();
$db_config_result = test_db_config_connection();
$config_result = test_config_php();
$user_result = ($direct_result['success']) ? test_user_record($direct_result['connection']) : ['success' => false];
$auth_result = test_admin_auth();
$session_result = test_session_state();

// Collect all results
$all_results = [
    'direct' => $direct_result,
    'db_config' => $db_config_result,
    'config' => $config_result,
    'user' => $user_result,
    'auth' => $auth_result,
    'session' => $session_result
];

// Generate solutions
generate_solutions($all_results);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Diagnostic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3, h4 {
            color: #333;
        }
        h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        h3 {
            margin-top: 25px;
            background: #f5f5f5;
            padding: 8px;
            border-left: 4px solid #333;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow: auto;
            border: 1px solid #ddd;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .warning {
            color: orange;
            font-weight: bold;
        }
        .fix-suggestion, .fix-solution {
            background: #e6f7ff;
            border: 1px solid #91d5ff;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .fix-solution {
            background: #f6ffed;
            border-color: #b7eb8f;
        }
        ul li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <h1>Database Connection Diagnostic</h1>
    <p>This script diagnoses issues with your database connection configuration.</p>
    
    <div style="margin-top: 30px; text-align: center;">
        <p><strong>Important:</strong> After fixing the issues, delete this diagnostic file for security reasons.</p>
    </div>
</body>
</html>
