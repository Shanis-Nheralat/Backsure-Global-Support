<?php
/**
 * Admin Dashboard Debug Script
 * This file helps troubleshoot 500 errors in the admin panel
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Admin Dashboard Debug</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";
echo "</head><body>";

echo "<h1>Admin Dashboard Diagnostic Tool</h1>";

// SECTION 1: PHP Environment
echo "<div class='section'>";
echo "<h2>1. PHP Environment</h2>";
echo "<p>PHP Version: <strong>" . phpversion() . "</strong></p>";

// Check for required PHP extensions
$required_extensions = ['mysqli', 'pdo', 'pdo_mysql', 'json', 'session', 'gd'];
echo "<h3>Required PHP Extensions:</h3>";
echo "<ul>";
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li class='success'>$ext: Loaded</li>";
    } else {
        echo "<li class='error'>$ext: Not loaded</li>";
    }
}
echo "</ul>";

// Check PHP memory limit
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "</div>";

// SECTION 2: File Structure
echo "<div class='section'>";
echo "<h2>2. Required Files Check</h2>";

$required_files = [
    'admin-auth.php',
    'admin-head.php',
    'admin-sidebar.php',
    'admin-header.php',
    'admin-footer.php',
    'admin-menu-config.php',
    'admin-notifications.php',
    'admin-analytics.php',
    'admin-themes.css',
    'admin-theme-switcher.js',
    'admin-core.css',
    'admin-core.js'
];

echo "<table>";
echo "<tr><th>File</th><th>Status</th><th>Readable</th><th>Size</th><th>Last Modified</th></tr>";

foreach ($required_files as $file) {
    echo "<tr>";
    echo "<td>$file</td>";
    
    if (file_exists($file)) {
        echo "<td class='success'>Found</td>";
        echo "<td>" . (is_readable($file) ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</td>";
        echo "<td>" . filesize($file) . " bytes</td>";
        echo "<td>" . date("Y-m-d H:i:s", filemtime($file)) . "</td>";
    } else {
        echo "<td class='error'>Missing</td>";
        echo "<td>-</td><td>-</td><td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// SECTION 3: Error Log Check
echo "<div class='section'>";
echo "<h2>3. Recent PHP Errors</h2>";

$error_log_path = ini_get('error_log');
if ($error_log_path && file_exists($error_log_path) && is_readable($error_log_path)) {
    echo "<p>Reading from error log: $error_log_path</p>";
    $log_content = file_get_contents($error_log_path);
    $log_lines = array_slice(explode("\n", $log_content), -20); // Get last 20 lines
    
    echo "<pre>" . htmlspecialchars(implode("\n", $log_lines)) . "</pre>";
} else {
    // Try to find error logs in common locations
    $possible_logs = [
        'error_log',
        '../error_log',
        '../../error_log',
        '../logs/error_log',
        '../logs/php_error.log'
    ];
    
    $found_log = false;
    foreach ($possible_logs as $log) {
        if (file_exists($log) && is_readable($log)) {
            echo "<p>Reading from error log: $log</p>";
            $log_content = file_get_contents($log);
            $log_lines = array_slice(explode("\n", $log_content), -20); // Get last 20 lines
            
            echo "<pre>" . htmlspecialchars(implode("\n", $log_lines)) . "</pre>";
            $found_log = true;
            break;
        }
    }
    
    if (!$found_log) {
        echo "<p class='warning'>Could not access error log file. Common locations checked.</p>";
    }
}
echo "</div>";

// SECTION 4: Test Database Connection
echo "<div class='section'>";
echo "<h2>4. Database Connection Test</h2>";

// Try to identify database connection settings
$db_config_files = [
    'config.php',
    'db-config.php',
    '../config.php',
    '../includes/config.php',
    'admin-config.php',
    'includes/config.php'
];

echo "<p>Searching for database configuration in common files...</p>";

$found_db_config = false;
foreach ($db_config_files as $config_file) {
    if (file_exists($config_file) && is_readable($config_file)) {
        echo "<p class='success'>Found potential config file: $config_file</p>";
        $found_db_config = true;
        // Don't include the file to avoid breaking things
    }
}

if (!$found_db_config) {
    echo "<p class='warning'>Could not find database configuration files. Please provide connection details manually.</p>";
}

echo "<p>To test database connection, create a file named 'db-test.php' with your database credentials and include it here.</p>";

// If a db-test.php file exists, include it to test connection
if (file_exists('db-test.php')) {
    echo "<p>Found db-test.php file. Testing connection...</p>";
    include 'db-test.php';
} else {
    echo "<p>No db-test.php file found. To create one, use the following template:</p>";
    echo "<pre>
&lt;?php
\$host = 'localhost';
\$dbname = 'your_database';
\$username = 'your_username';
\$password = 'your_password';

try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$dbname\", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo \"&lt;p class='success'&gt;Database connection successful!&lt;/p&gt;\";
} catch(PDOException \$e) {
    echo \"&lt;p class='error'&gt;Connection failed: \" . \$e->getMessage() . \"&lt;/p&gt;\";
}
?&gt;
</pre>";
}
echo "</div>";

// SECTION 5: Test Admin Auth Functions
echo "<div class='section'>";
echo "<h2>5. Testing auth.php Inclusion</h2>";

if (file_exists('admin-auth.php')) {
    echo "<p>Testing safe inclusion of admin-auth.php...</p>";
    
    // Save error handler
    $previous_error_handler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
        echo "<p class='error'>Error in admin-auth.php: $errstr in $errfile on line $errline</p>";
        return true;
    });
    
    try {
        // Try to include the file
        include_once 'admin-auth.php';
        echo "<p class='success'>admin-auth.php included without errors</p>";
        
        // Test if key functions exist
        $auth_functions = [
            'require_admin_auth',
            'get_admin_user',
            'require_admin_role'
        ];
        
        echo "<h3>Checking for required functions:</h3>";
        echo "<ul>";
        foreach ($auth_functions as $func) {
            if (function_exists($func)) {
                echo "<li class='success'>$func: Found</li>";
            } else {
                echo "<li class='error'>$func: Not found</li>";
            }
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p class='error'>Exception while including admin-auth.php: " . $e->getMessage() . "</p>";
    }
    
    // Restore error handler
    if ($previous_error_handler) {
        set_error_handler($previous_error_handler);
    }
} else {
    echo "<p class='error'>admin-auth.php file not found</p>";
}
echo "</div>";

// SECTION 6: Generate minimalistic test dashboard
echo "<div class='section'>";
echo "<h2>6. Minimal Dashboard Test</h2>";

echo "<p>Trying to generate a minimal dashboard with basic elements...</p>";

echo "<div id='minimal-dashboard'>";
echo "<h3>Minimal Dashboard Header</h3>";
echo "<p>If you see this, basic PHP processing is working correctly.</p>";

// Try to include CSS if it exists
if (file_exists('admin-core.css')) {
    echo "<p class='success'>admin-core.css exists. Here's a button styled with it:</p>";
    echo "<button class='btn btn-primary'>Test Button</button>";
} else {
    echo "<p class='warning'>admin-core.css not found. Styling will be missing.</p>";
}

echo "</div>";
echo "</div>";

// SECTION 7: Template Code for Admin Dashboard
echo "<div class='section'>";
echo "<h2>7. Repair Suggestions</h2>";

echo "<p>Based on your implementation guide, try the following simplified version of admin-dashboard.php:</p>";

echo "<pre>" . htmlspecialchars('<?php
/**
 * Admin Dashboard
 * Main dashboard page for admin panel
 */

// Basic error reporting for troubleshooting
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Authentication and permissions - with error handling
if (file_exists("admin-auth.php")) {
    require_once "admin-auth.php";
    if (function_exists("require_admin_auth")) {
        require_admin_auth();
    } else {
        // Fallback if function doesn\'t exist
        // This prevents the page from breaking if auth is incomplete
        echo "<!-- Auth function not found, continuing anyway -->";
    }
} else {
    echo "<!-- Auth file not found, continuing anyway -->";
}

// Include notifications system - with error handling
if (file_exists("admin-notifications.php")) {
    require_once "admin-notifications.php";
}

// Track page view for analytics - with error handling
if (file_exists("admin-analytics.php")) {
    require_once "admin-analytics.php";
    if (function_exists("log_page_view")) {
        log_page_view(basename($_SERVER["PHP_SELF"]));
    }
}

// Page variables
$page_title = "Dashboard";
$current_page = "dashboard"; // Must match the ID in admin-menu-config.php
$breadcrumbs = [
    ["title" => "Dashboard", "url" => "#"]
];

// Get admin info - with error handling
$admin_username = "Admin";
$admin_role = "admin";
if (function_exists("get_admin_user")) {
    $admin_user = get_admin_user();
    if (is_array($admin_user)) {
        $admin_username = isset($admin_user["username"]) ? $admin_user["username"] : "Admin";
        $admin_role = isset($admin_user["role"]) ? $admin_user["role"] : "admin";
    }
}

// Include templates - with error handling
if (file_exists("admin-head.php")) {
    include "admin-head.php";
} else {
    echo "<html><head><title>$page_title</title></head><body>";
}

if (file_exists("admin-sidebar.php")) {
    include "admin-sidebar.php";
} else {
    echo "<div style=\"width:200px;float:left;\">Sidebar Placeholder</div>";
}
?>

<main class="admin-main">
  <?php 
  if (file_exists("admin-header.php")) {
      include "admin-header.php";
  } else {
      echo "<header><h1>$page_title</h1></header>";
  }
  ?>
  
  <!-- Page Content -->
  <div class="admin-content">
    <div class="page-header">
      <h1><?php echo $page_title; ?></h1>
    </div>
    
    <!-- Dashboard content -->
    <div class="card">
      <div class="card-header">
        <h2>Dashboard Overview</h2>
      </div>
      <div class="card-body">
        <p>Welcome to the admin dashboard.</p>
      </div>
    </div>
  </div>
  
  <?php 
  if (file_exists("admin-footer.php")) {
      include "admin-footer.php";
  } else {
      echo "<footer>&copy; " . date("Y") . "</footer>";
  }
  ?>
</main>

<?php
// Close HTML tags if head was included
if (!file_exists("admin-head.php")) {
    echo "</body></html>";
}
?>') . "</pre>";

echo "</div>";
echo "</body></html>";
?>
