<?php
/**
 * Admin Authentication Diagnostic Tool
 * This script will help identify authentication and permission issues
 */

// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Admin Auth Diagnostic</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    pre { background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 4px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";
echo "</head><body>";

echo "<h1>Admin Authentication Diagnostic Report</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// SECTION 1: Check Session Data
echo "<div class='section'>";
echo "<h2>1. Session Information</h2>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "<p class='info'>Session was not started. Started now.</p>";
} else {
    echo "<p class='success'>Session already started.</p>";
}

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check for admin user in session
if (isset($_SESSION['admin_user'])) {
    echo "<p class='success'>Admin user found in session.</p>";
    
    // Check role
    if (isset($_SESSION['admin_user']['role'])) {
        echo "<p>User role in session: <strong>" . $_SESSION['admin_user']['role'] . "</strong></p>";
    } else {
        echo "<p class='error'>No role found in admin_user session data.</p>";
    }
} else {
    echo "<p class='error'>No admin_user found in session.</p>";
}
echo "</div>";

// SECTION 2: Include and test admin-auth.php functions
echo "<div class='section'>";
echo "<h2>2. Authentication Module Check</h2>";

// Try to include admin-auth.php safely
$auth_file = 'admin-auth.php';
if (file_exists($auth_file)) {
    echo "<p class='success'>Found admin-auth.php file.</p>";
    
    // Save error handler
    $previous_error_handler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
        echo "<p class='error'>Error including admin-auth.php: $errstr in $errfile on line $errline</p>";
        return true;
    });
    
    try {
        // Include the file
        include_once $auth_file;
        echo "<p class='success'>admin-auth.php included without errors.</p>";
        
        // Test key functions
        echo "<h3>Testing Authentication Functions:</h3>";
        echo "<ul>";
        
        // Check require_admin_auth function
        if (function_exists('require_admin_auth')) {
            echo "<li class='success'>require_admin_auth() function exists</li>";
            
            // Get its source code for analysis
            try {
                $func = new ReflectionFunction('require_admin_auth');
                $start_line = $func->getStartLine();
                $end_line = $func->getEndLine();
                $length = $end_line - $start_line;
                
                $file = file($func->getFileName());
                $code = implode("", array_slice($file, $start_line - 1, $length + 1));
                
                echo "<li>Function code:<pre>" . htmlspecialchars($code) . "</pre></li>";
            } catch (Exception $e) {
                echo "<li class='warning'>Could not retrieve function code: " . $e->getMessage() . "</li>";
            }
        } else {
            echo "<li class='error'>require_admin_auth() function does not exist</li>";
        }
        
        // Check require_admin_role function
        if (function_exists('require_admin_role')) {
            echo "<li class='success'>require_admin_role() function exists</li>";
            
            // Get its source code for analysis
            try {
                $func = new ReflectionFunction('require_admin_role');
                $start_line = $func->getStartLine();
                $end_line = $func->getEndLine();
                $length = $end_line - $start_line;
                
                $file = file($func->getFileName());
                $code = implode("", array_slice($file, $start_line - 1, $length + 1));
                
                echo "<li>Function code:<pre>" . htmlspecialchars($code) . "</pre></li>";
            } catch (Exception $e) {
                echo "<li class='warning'>Could not retrieve function code: " . $e->getMessage() . "</li>";
            }
        } else {
            echo "<li class='error'>require_admin_role() function does not exist</li>";
        }
        
        // Check get_admin_user function
        if (function_exists('get_admin_user')) {
            echo "<li class='success'>get_admin_user() function exists</li>";
            
            // Try to get current user data
            try {
                $user = get_admin_user();
                if ($user) {
                    echo "<li class='success'>get_admin_user() returned data</li>";
                    echo "<li>User data:<pre>" . print_r($user, true) . "</pre></li>";
                } else {
                    echo "<li class='warning'>get_admin_user() returned no data</li>";
                }
            } catch (Exception $e) {
                echo "<li class='error'>Error calling get_admin_user(): " . $e->getMessage() . "</li>";
            }
        } else {
            echo "<li class='error'>get_admin_user() function does not exist</li>";
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
    echo "<p class='error'>admin-auth.php file not found.</p>";
}
echo "</div>";

// SECTION 3: Database Configuration & User Data
echo "<div class='section'>";
echo "<h2>3. Database Connection & User Data</h2>";

// Try to identify database connection settings
$db_config_files = [
    'config.php',
    'db-config.php',
    '../config.php',
    '../includes/config.php',
    'admin-config.php',
    'includes/config.php'
];

echo "<p>Searching for database configuration...</p>";

$found_db_config = false;
foreach ($db_config_files as $config_file) {
    if (file_exists($config_file)) {
        echo "<p class='success'>Found potential config file: $config_file</p>";
        $found_db_config = true;
        
        // Don't include file to avoid breaking things
        // Just note that it exists
    }
}

if (!$found_db_config) {
    echo "<p class='warning'>Could not find database configuration files in common locations.</p>";
    echo "<p>Please enter your database credentials below to check user data:</p>";
    
    // Display a form to let the user enter database credentials
    echo "<form method='post' action=''>";
    echo "<table>";
    echo "<tr><td>DB Host:</td><td><input type='text' name='db_host' value='localhost'></td></tr>";
    echo "<tr><td>DB Name:</td><td><input type='text' name='db_name'></td></tr>";
    echo "<tr><td>DB User:</td><td><input type='text' name='db_user'></td></tr>";
    echo "<tr><td>DB Password:</td><td><input type='password' name='db_pass'></td></tr>";
    echo "<tr><td>User Table:</td><td><input type='text' name='user_table' value='users'></td></tr>";
    echo "</table>";
    echo "<button type='submit' name='check_db'>Check Database</button>";
    echo "</form>";
} else {
    echo "<p>Database configuration found. To check user data, create a temporary file with this code:</p>";
    echo "<pre>
&lt;?php
// Include your database configuration
require_once 'config.php'; // Update this path as needed

// Query to get roles from your users table
\$query = \"SELECT id, username, role FROM users ORDER BY id\";
\$result = \$pdo->query(\$query); // Adjust this based on your DB connection type (mysqli or PDO)

echo \"&lt;h2&gt;User Roles in Database&lt;/h2&gt;\";
echo \"&lt;table border='1'&gt;\";
echo \"&lt;tr&gt;&lt;th&gt;ID&lt;/th&gt;&lt;th&gt;Username&lt;/th&gt;&lt;th&gt;Role&lt;/th&gt;&lt;/tr&gt;\";

while (\$row = \$result->fetch(PDO::FETCH_ASSOC)) { // Adjust fetch method as needed
    echo \"&lt;tr&gt;\";
    echo \"&lt;td&gt;{\$row['id']}&lt;/td&gt;\";
    echo \"&lt;td&gt;{\$row['username']}&lt;/td&gt;\";
    echo \"&lt;td&gt;{\$row['role']}&lt;/td&gt;\";
    echo \"&lt;/tr&gt;\";
}

echo \"&lt;/table&gt;\";
?&gt;
</pre>";
}

// Process the form if submitted
if (isset($_POST['check_db'])) {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $user_table = $_POST['user_table'];
    
    try {
        // Try to connect to the database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p class='success'>Database connection successful!</p>";
        
        // Query users table to get user info
        $query = "SELECT * FROM $user_table";
        $stmt = $pdo->query($query);
        
        echo "<h3>Users in Database:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th></tr>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td>" . ($row['status'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Check DB schema for roles
        echo "<h3>Role Information in Database Schema:</h3>";
        try {
            // Check if the role column has ENUM constraint
            $query = "SHOW COLUMNS FROM $user_table WHERE Field = 'role'";
            $stmt = $pdo->query($query);
            $role_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($role_info) {
                echo "<p>Role column type: <strong>" . $role_info['Type'] . "</strong></p>";
                
                // If it's an ENUM, extract the values
                if (strpos($role_info['Type'], 'enum') === 0) {
                    preg_match("/enum\(\'(.*)\'\)/", $role_info['Type'], $matches);
                    if (isset($matches[1])) {
                        $enum_values = explode("','", $matches[1]);
                        echo "<p>Allowed role values:</p>";
                        echo "<ul>";
                        foreach ($enum_values as $value) {
                            if ($value == 'superadmin') {
                                echo "<li class='success'>$value (superadmin is allowed)</li>";
                            } else {
                                echo "<li>$value</li>";
                            }
                        }
                        echo "</ul>";
                        
                        if (!in_array('superadmin', $enum_values)) {
                            echo "<p class='error'>The 'superadmin' role is not in the ENUM values in the database schema. This could be the root cause of your issue.</p>";
                        }
                    }
                }
            } else {
                echo "<p class='warning'>Could not get information about the role column.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error checking role schema: " . $e->getMessage() . "</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='error'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

// SECTION 4: Request & Redirect Information
echo "<div class='section'>";
echo "<h2>4. Request & Redirect Analysis</h2>";

echo "<h3>Current Request Information:</h3>";
echo "<table>";
echo "<tr><th>Parameter</th><th>Value</th></tr>";
echo "<tr><td>REQUEST_URI</td><td>" . $_SERVER['REQUEST_URI'] . "</td></tr>";
echo "<tr><td>HTTP_REFERER</td><td>" . ($_SERVER['HTTP_REFERER'] ?? 'N/A') . "</td></tr>";
echo "<tr><td>QUERY_STRING</td><td>" . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "</td></tr>";
echo "</table>";

echo "<h3>Test for Redirect Loop:</h3>";
echo "<p>The URL in your browser shows: <code>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</code></p>";

if (strpos($_SERVER['REQUEST_URI'], 'error=unauthorized') !== false) {
    echo "<p class='error'>Detected 'unauthorized' error in URL. This suggests authorization is failing and causing a redirect.</p>";
} 

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin-dashboard.php') !== false) {
    echo "<p class='warning'>Referer contains admin-dashboard.php. This may indicate a redirect loop.</p>";
}

echo "</div>";

// SECTION 5: Environment Information
echo "<div class='section'>";
echo "<h2>5. Environment Information</h2>";

echo "<h3>PHP Version:</h3>";
echo "<p>" . phpversion() . "</p>";

echo "<h3>Loaded Extensions:</h3>";
echo "<ul>";
$required_extensions = ['mysqli', 'pdo', 'pdo_mysql', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li class='success'>$ext: Loaded</li>";
    } else {
        echo "<li class='error'>$ext: Not loaded</li>";
    }
}
echo "</ul>";

echo "<h3>Server Information:</h3>";
echo "<table>";
echo "<tr><td>Server Software:</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</td></tr>";
echo "<tr><td>Document Root:</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</td></tr>";
echo "<tr><td>Script Filename:</td><td>" . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "</td></tr>";
echo "</table>";

echo "</div>";

// SECTION 6: Recommendation
echo "<div class='section'>";
echo "<h2>6. Diagnostic Results & Recommendations</h2>";

echo "<p>Based on the diagnostic information above, here are some potential issues and recommendations:</p>";

echo "<ol>";
echo "<li>Look for any errors in the session data section. If the admin_user is missing or the role is not set correctly, this could cause authentication issues.</li>";
echo "<li>Check the authentication functions from admin-auth.php. Pay attention to how require_admin_role() functions and what roles it accepts.</li>";
echo "<li>Verify that the 'superadmin' role exists in your database schema. If it's an ENUM type and 'superadmin' is not listed, this is likely the root issue.</li>";
echo "<li>If you see a redirect loop with the 'unauthorized' error parameter, this confirms that the authorization check is failing.</li>";
echo "</ol>";

echo "<h3>Next Steps:</h3>";
echo "<p>Based on what you find, here are potential fixes:</p>";

echo "<ol>";
echo "<li>If 'superadmin' is not in the database ENUM: Update the database schema to include 'superadmin' in the allowed roles.</li>";
echo "<li>If the issue is in require_admin_role(): Create a modified version that always allows 'superadmin'.</li>";
echo "<li>If the session data is incorrect: Create a script to update the session with the correct role.</li>";
echo "<li>If the user record shows 'superadmin' but it's not being recognized: Check for case sensitivity issues or whitespace in the role value.</li>";
echo "</ol>";

echo "</div>";

// SECTION 7: Quick Fix Tools
echo "<div class='section'>";
echo "<h2>7. Quick Fix Tools</h2>";

echo "<h3>7.1 Update Session Role:</h3>";
echo "<p>If your user has the correct role in the database but it's not reflected in the session, use this tool:</p>";
echo "<form method='post' action=''>";
echo "<input type='hidden' name='action' value='update_session'>";
echo "<table>";
echo "<tr><td>New Role Value:</td><td><input type='text' name='new_role' value='superadmin'></td></tr>";
echo "</table>";
echo "<button type='submit'>Update Session Role</button>";
echo "</form>";

// Process session update
if (isset($_POST['action']) && $_POST['action'] == 'update_session' && isset($_POST['new_role'])) {
    if (isset($_SESSION['admin_user'])) {
        $old_role = $_SESSION['admin_user']['role'] ?? 'none';
        $_SESSION['admin_user']['role'] = $_POST['new_role'];
        echo "<p class='success'>Session role updated from '$old_role' to '" . $_POST['new_role'] . "'.</p>";
        echo "<p>Try accessing the admin dashboard now.</p>";
    } else {
        echo "<p class='error'>No admin_user found in session. Cannot update.</p>";
    }
}

echo "<h3>7.2 Check/Create Database Role:</h3>";
echo "<p>If you need to verify or update the database schema for the role column:</p>";
echo "<form method='post' action=''>";
echo "<input type='hidden' name='action' value='check_role_schema'>";
echo "<table>";
echo "<tr><td>DB Host:</td><td><input type='text' name='db_host' value='localhost'></td></tr>";
echo "<tr><td>DB Name:</td><td><input type='text' name='db_name'></td></tr>";
echo "<tr><td>DB User:</td><td><input type='text' name='db_user'></td></tr>";
echo "<tr><td>DB Password:</td><td><input type='password' name='db_pass'></td></tr>";
echo "<tr><td>User Table:</td><td><input type='text' name='user_table' value='users'></td></tr>";
echo "</table>";
echo "<button type='submit'>Check Role Schema</button>";
echo "</form>";

// Process role schema check
if (isset($_POST['action']) && $_POST['action'] == 'check_role_schema') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $user_table = $_POST['user_table'];
    
    try {
        // Try to connect to the database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check the role column
        $query = "SHOW COLUMNS FROM $user_table WHERE Field = 'role'";
        $stmt = $pdo->query($query);
        $role_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($role_info) {
            echo "<p class='success'>Found role column in $user_table table.</p>";
            echo "<p>Current type: <strong>" . $role_info['Type'] . "</strong></p>";
            
            // Extract ENUM values if applicable
            if (strpos($role_info['Type'], 'enum') === 0) {
                preg_match("/enum\(\'(.*)\'\)/", $role_info['Type'], $matches);
                if (isset($matches[1])) {
                    $enum_values = explode("','", $matches[1]);
                    echo "<p>Current allowed values: " . implode(", ", $enum_values) . "</p>";
                    
                    // Check if superadmin is in the ENUM
                    if (!in_array('superadmin', $enum_values)) {
                        echo "<p class='error'>The 'superadmin' role is not in the allowed values.</p>";
                        
                        // Provide SQL to add superadmin
                        $new_enum_values = array_merge($enum_values, ['superadmin']);
                        $new_enum_str = "'" . implode("','", $new_enum_values) . "'";
                        
                        echo "<h4>SQL to add 'superadmin' to allowed roles:</h4>";
                        echo "<pre>ALTER TABLE $user_table MODIFY COLUMN role ENUM($new_enum_str) NOT NULL;</pre>";
                        
                        echo "<form method='post' action=''>";
                        echo "<input type='hidden' name='action' value='update_role_schema'>";
                        echo "<input type='hidden' name='db_host' value='$db_host'>";
                        echo "<input type='hidden' name='db_name' value='$db_name'>";
                        echo "<input type='hidden' name='db_user' value='$db_user'>";
                        echo "<input type='hidden' name='db_pass' value='$db_pass'>";
                        echo "<input type='hidden' name='user_table' value='$user_table'>";
                        echo "<input type='hidden' name='new_enum' value=\"$new_enum_str\">";
                        echo "<button type='submit'>Execute SQL to Add superadmin</button>";
                        echo "</form>";
                    } else {
                        echo "<p class='success'>The 'superadmin' role is already in the allowed values.</p>";
                    }
                }
            } else {
                echo "<p class='info'>The role column is not an ENUM type. It's a: " . $role_info['Type'] . "</p>";
            }
        } else {
            echo "<p class='error'>Could not find role column in $user_table table.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Process role schema update
if (isset($_POST['action']) && $_POST['action'] == 'update_role_schema') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $user_table = $_POST['user_table'];
    $new_enum = $_POST['new_enum'];
    
    try {
        // Try to connect to the database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Update the role column
        $query = "ALTER TABLE $user_table MODIFY COLUMN role ENUM($new_enum) NOT NULL;";
        $stmt = $pdo->exec($query);
        
        echo "<p class='success'>Successfully updated role column to include 'superadmin'.</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>Failed to update role column: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

echo "<h2>Final Recommendations</h2>";
echo "<p>After running this diagnostic, look for:</p>";
echo "<ol>";
echo "<li>Session data issues - Is the role set correctly?</li>";
echo "<li>Database schema issues - Is 'superadmin' an allowed role?</li>";
echo "<li>Authentication function issues - Does require_admin_role() accept 'superadmin'?</li>";
echo "<li>Redirect loop indicators - Is the unauthorized error showing up?</li>";
echo "</ol>";
echo "<p>Based on your findings, we can develop a targeted fix for your specific issue.</p>";

echo "</body></html>";
?>
