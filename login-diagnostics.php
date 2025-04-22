<?php
/**
 * Login Diagnostics Script
 * This script will help diagnose login problems
 */

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

echo "<html><head><title>Login Diagnostics</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1, h2 { color: #333; }
    .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
    .success { color: green; }
    .error { color: red; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";
echo "</head><body>";
echo "<h1>Login System Diagnostics</h1>";

// Function to output a section header
function outputSection($title) {
    echo "<div class='section'>";
    echo "<h2>$title</h2>";
}

function closeSectionWithEndTable() {
    echo "</table></div>";
}

function closeSection() {
    echo "</div>";
}

// Check PHP version
outputSection("PHP Environment");
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "</p>";
closeSection();

// Check for required files
outputSection("Required Files");
$requiredFiles = ['admin-login.php', 'admin-login-process.php', 'admin-dashboard.php', 'db_config.php'];
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file does not exist</p>";
    }
}
closeSection();

// Test database connection
outputSection("Database Connection Test");
try {
    require_once 'db_config.php';
    echo "<p class='success'>✓ Successfully included db_config.php</p>";
    
    try {
        $pdo = get_db_connection();
        echo "<p class='success'>✓ Successfully connected to database</p>";
        
        // Check tables
        $tables_stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
        $adminsExists = $tables_stmt->fetchColumn();
        
        if ($adminsExists) {
            echo "<p class='success'>✓ 'admins' table exists</p>";
            
            // Show columns
            $columns_stmt = $pdo->query("DESCRIBE admins");
            $columns = $columns_stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Columns in 'admins' table: " . implode(", ", $columns) . "</p>";
            
            // Count users
            $count_stmt = $pdo->query("SELECT COUNT(*) FROM admins");
            $userCount = $count_stmt->fetchColumn();
            echo "<p>Number of users in 'admins' table: $userCount</p>";
            
            if ($userCount > 0) {
                // Show one user (hide password)
                $user_stmt = $pdo->query("SELECT id, username, email, role FROM admins LIMIT 1");
                $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>Sample user:</p>";
                echo "<pre>" . print_r($user, true) . "</pre>";
                
                // Check password hash format
                $hash_stmt = $pdo->query("SELECT password FROM admins LIMIT 1");
                $hash = $hash_stmt->fetchColumn();
                echo "<p>Password hash starts with: " . substr($hash, 0, 10) . "...</p>";
                echo "<p>Password hash format looks " . (strpos($hash, '$2y$') === 0 ? "correct (bcrypt)" : "incorrect (not bcrypt)") . "</p>";
            }
        } else {
            echo "<p class='error'>✗ 'admins' table does not exist</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Failed to include db_config.php: " . $e->getMessage() . "</p>";
}
closeSection();

// Session variables
outputSection("Current Session Variables");
if (count($_SESSION) > 0) {
    echo "<table><tr><th>Key</th><th>Value</th></tr>";
    foreach ($_SESSION as $key => $value) {
        echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . (is_array($value) ? "Array" : htmlspecialchars($value)) . "</td></tr>";
    }
    closeSectionWithEndTable();
} else {
    echo "<p>No session variables set.</p>";
    closeSection();
}

// POST data (if any)
outputSection("Current POST Data");
if (count($_POST) > 0) {
    echo "<table><tr><th>Field</th><th>Value</th></tr>";
    foreach ($_POST as $key => $value) {
        // Don't show passwords
        if ($key === 'password') {
            $value = '***********';
        }
        echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    closeSectionWithEndTable();
} else {
    echo "<p>No POST data received.</p>";
    closeSection();
}

// Check form action in admin-login.php
outputSection("Login Form Analysis");
if (file_exists('admin-login.php')) {
    $loginContent = file_get_contents('admin-login.php');
    
    if (preg_match('/<form.*?action="(.*?)".*?>/i', $loginContent, $matches)) {
        $action = $matches[1];
        if (empty($action)) {
            echo "<p>Form action is empty (submits to self) in admin-login.php</p>";
        } else {
            echo "<p>Form action is set to: " . htmlspecialchars($action) . "</p>";
        }
    } else {
        echo "<p class='error'>Could not determine form action in admin-login.php</p>";
    }
    
    // Check if there's login processing code
    if (strpos($loginContent, 'Process login form submission') !== false) {
        echo "<p>admin-login.php contains its own login processing code.</p>";
    }
    
    // Check if admin-login-process.php returns JSON
    if (file_exists('admin-login-process.php')) {
        $processContent = file_get_contents('admin-login-process.php');
        if (strpos($processContent, "json_encode") !== false) {
            echo "<p>admin-login-process.php returns JSON responses.</p>";
        }
        if (strpos($processContent, "header('Content-Type: application/json')") !== false) {
            echo "<p>admin-login-process.php sets Content-Type to application/json.</p>";
        }
    }
} else {
    echo "<p class='error'>Cannot analyze admin-login.php (file not found)</p>";
}
closeSection();

// Dashboard checks
outputSection("Dashboard Authentication Check");
if (file_exists('admin-dashboard.php')) {
    $dashboardContent = file_get_contents('admin-dashboard.php');
    
    // Check which session variables it's looking for
    if (strpos($dashboardContent, '$_SESSION[\'admin_logged_in\']') !== false) {
        echo "<p>Dashboard checks for \$_SESSION['admin_logged_in'].</p>";
    }
    if (strpos($dashboardContent, '$_SESSION[\'admin_id\']') !== false) {
        echo "<p>Dashboard checks for \$_SESSION['admin_id'].</p>";
    }
    if (strpos($dashboardContent, '$_SESSION[\'user_id\']') !== false) {
        echo "<p class='error'>Dashboard checks for \$_SESSION['user_id'] but login sets 'admin_id'.</p>";
    }
    if (strpos($dashboardContent, '$_SESSION[\'user_role\']') !== false) {
        echo "<p class='error'>Dashboard checks for \$_SESSION['user_role'] but login sets 'admin_role'.</p>";
    }
} else {
    echo "<p class='error'>Cannot analyze admin-dashboard.php (file not found)</p>";
}
closeSection();

// Recommendations
outputSection("Recommendations");
echo "<ol>";
echo "<li>Ensure that admin-login.php and admin-dashboard.php use the same session variable names.</li>";
echo "<li>Either use admin-login.php's built-in processing OR modify it to submit to admin-login-process.php, not both.</li>";
echo "<li>If using admin-login-process.php, make sure it sets the same session variables that admin-dashboard.php checks for.</li>";
echo "<li>If seeing raw JSON responses, the form is submitting to admin-login-process.php directly without AJAX handling.</li>";
echo "</ol>";
closeSection();

// Login test form
outputSection("Test Login Form");
echo "<p>Use this form to test login directly (will bypass any JavaScript handling):</p>";
echo "<form action='admin-login-process.php' method='post' style='margin: 20px 0;'>
    <div style='margin-bottom: 10px;'>
        <label style='display: block; margin-bottom: 5px;'>Username:</label>
        <input type='text' name='username' style='padding: 5px;'>
    </div>
    <div style='margin-bottom: 10px;'>
        <label style='display: block; margin-bottom: 5px;'>Password:</label>
        <input type='password' name='password' style='padding: 5px;'>
    </div>
    <button type='submit' style='padding: 5px 10px; background: #4c70af; color: white; border: none; cursor: pointer;'>
        Test Login Process
    </button>
</form>";
closeSection();

echo "<p><strong>After using this diagnostic tool, delete this file for security reasons.</strong></p>";
echo "</body></html>";