<?php
/**
 * Enhanced Diagnostic Script for Admin Panel
 * Tests specific functionality related to admin profile implementation
 */

// Styling for the diagnostic page
echo "<!DOCTYPE html>
<html>
<head>
    <title>Enhanced Admin Panel Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { margin-top: 20px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .code { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow: auto; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Enhanced Admin Panel Diagnostic</h1>";

// Check if we can access db_config.php
echo "<div class='section'>";
echo "<h2>Database Configuration Check</h2>";

$db_config_exists = file_exists('db_config.php');
echo $db_config_exists 
    ? "<p class='success'>✓ db_config.php file exists</p>" 
    : "<p class='error'>✗ db_config.php file not found</p>";

if ($db_config_exists) {
    // Try to include db_config safely
    try {
        ob_start();
        include_once 'db_config.php';
        $output = ob_get_clean();
        
        if (!empty($output)) {
            echo "<p class='warning'>db_config.php produced output: " . htmlspecialchars($output) . "</p>";
        } else {
            echo "<p class='success'>✓ db_config.php included successfully with no output</p>";
        }
        
        // Check if we got a database connection
        if (function_exists('get_db_connection')) {
            echo "<p class='success'>✓ get_db_connection() function exists</p>";
            
            try {
                $db = get_db_connection();
                if ($db instanceof PDO) {
                    echo "<p class='success'>✓ Successfully connected to database</p>";
                    
                    // Test if we can query
                    try {
                        $stmt = $db->query("SELECT 1");
                        if ($stmt && $stmt->fetchColumn() === 1) {
                            echo "<p class='success'>✓ Successfully executed a test query</p>";
                        } else {
                            echo "<p class='error'>✗ Failed to execute a test query</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p class='error'>✗ Query error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                } else {
                    echo "<p class='error'>✗ Failed to get a PDO connection</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>✗ Connection error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p class='error'>✗ get_db_connection() function not found in db_config.php</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error including db_config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Display database credentials (masking password)
    if (isset($db_host) && isset($db_name) && isset($db_user)) {
        echo "<p>Database Host: <strong>" . htmlspecialchars($db_host) . "</strong></p>";
        echo "<p>Database Name: <strong>" . htmlspecialchars($db_name) . "</strong></p>";
        echo "<p>Database User: <strong>" . htmlspecialchars($db_user) . "</strong></p>";
        echo "<p>Database Password: <strong>" . (isset($db_pass) ? "******" : "Not defined") . "</strong></p>";
    } else {
        echo "<p class='warning'>⚠ Database credentials not found in expected variables</p>";
    }
}
echo "</div>";

// Check admin-auth.php
echo "<div class='section'>";
echo "<h2>Admin Authentication Check</h2>";

$admin_auth_exists = file_exists('admin-auth.php');
echo $admin_auth_exists 
    ? "<p class='success'>✓ admin-auth.php file exists</p>" 
    : "<p class='error'>✗ admin-auth.php file not found</p>";

if ($admin_auth_exists) {
    // Try to include admin-auth safely
    try {
        ob_start();
        include_once 'admin-auth.php';
        $output = ob_get_clean();
        
        if (!empty($output)) {
            echo "<p class='warning'>admin-auth.php produced output: " . htmlspecialchars($output) . "</p>";
        } else {
            echo "<p class='success'>✓ admin-auth.php included successfully with no output</p>";
        }
        
        // Check for required functions
        $required_functions = [
            'is_admin_logged_in',
            'require_admin_auth',
            'get_admin_user',
            'get_admin_profile'
        ];
        
        foreach ($required_functions as $function) {
            if (function_exists($function)) {
                echo "<p class='success'>✓ Function '$function' exists</p>";
                
                // Test get_admin_profile if logged in
                if ($function === 'get_admin_profile' && function_exists('is_admin_logged_in') && is_admin_logged_in()) {
                    try {
                        $profile = get_admin_profile();
                        if (is_array($profile)) {
                            echo "<p class='success'>✓ get_admin_profile() returned data:</p>";
                            echo "<div class='code'><pre>" . print_r($profile, true) . "</pre></div>";
                        } else {
                            echo "<p class='warning'>⚠ get_admin_profile() returned non-array data</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p class='error'>✗ Error in get_admin_profile(): " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
            } else {
                echo "<p class='error'>✗ Function '$function' not found</p>";
                
                // If get_admin_profile is missing, show the file content to analyze
                if ($function === 'get_admin_profile') {
                    $auth_content = file_get_contents('admin-auth.php');
                    echo "<p>admin-auth.php content:</p>";
                    echo "<div class='code'><pre>" . htmlspecialchars($auth_content) . "</pre></div>";
                }
            }
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error including admin-auth.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
echo "</div>";

// Check admin profile table structure
echo "<div class='section'>";
echo "<h2>Admin Table Structure Check</h2>";

if (function_exists('get_db_connection')) {
    try {
        $db = get_db_connection();
        $stmt = $db->query("DESCRIBE admins");
        
        if ($stmt) {
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p class='success'>✓ Successfully retrieved admins table structure</p>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            foreach ($columns as $column) {
                echo "<tr>";
                foreach ($column as $key => $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Check if profile columns exist
            $profile_columns = ['full_name', 'phone', 'department', 'bio', 'avatar', 'notify_email', 'notify_system', 'notify_sms'];
            $column_names = array_column($columns, 'Field');
            
            echo "<h3>Profile Columns Check</h3>";
            foreach ($profile_columns as $column) {
                if (in_array($column, $column_names)) {
                    echo "<p class='success'>✓ Column '$column' exists</p>";
                } else {
                    echo "<p class='error'>✗ Column '$column' not found - needs to be added</p>";
                }
            }
        } else {
            echo "<p class='error'>✗ Failed to retrieve admins table structure</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error checking table structure: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>✗ Cannot check table structure - get_db_connection() function not found</p>";
}
echo "</div>";

// Check activity log table
echo "<div class='section'>";
echo "<h2>Activity Log Table Check</h2>";

if (function_exists('get_db_connection')) {
    try {
        $db = get_db_connection();
        $stmt = $db->query("SHOW TABLES LIKE 'admin_activity_log'");
        
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✓ admin_activity_log table exists</p>";
            
            // Check table structure
            $stmt = $db->query("DESCRIBE admin_activity_log");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>Table structure:</p>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            foreach ($columns as $column) {
                echo "<tr>";
                foreach ($column as $key => $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p class='error'>✗ admin_activity_log table does not exist - needs to be created</p>";
            
            // Show create table statement
            echo "<p>You need to run this SQL:</p>";
            echo "<div class='code'>
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    action_type VARCHAR(20),
    resource VARCHAR(30),
    resource_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    timestamp DATETIME,
    FOREIGN KEY (user_id) REFERENCES admins(id) ON DELETE SET NULL
);
</div>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error checking activity log table: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>✗ Cannot check activity log table - get_db_connection() function not found</p>";
}
echo "</div>";

// Check profile directory
echo "<div class='section'>";
echo "<h2>Profile Images Directory Check</h2>";

$profile_dir = "media-library/admin-profiles";
if (file_exists($profile_dir)) {
    echo "<p class='success'>✓ Profile images directory exists: $profile_dir</p>";
    
    // Check permissions
    $perms = substr(sprintf('%o', fileperms($profile_dir)), -4);
    echo "<p>Directory permissions: $perms</p>";
    
    if (is_writable($profile_dir)) {
        echo "<p class='success'>✓ Directory is writable</p>";
    } else {
        echo "<p class='error'>✗ Directory is not writable - permissions need to be fixed</p>";
        echo "<p>Run: <code>chmod 755 $profile_dir</code></p>";
    }
} else {
    echo "<p class='error'>✗ Profile images directory does not exist</p>";
    echo "<p>You need to create this directory:</p>";
    echo "<div class='code'>mkdir -p $profile_dir
chmod 755 $profile_dir</div>";
}
echo "</div>";

// Check admin-profile.php
echo "<div class='section'>";
echo "<h2>Admin Profile Page Check</h2>";

$profile_file = 'admin-profile.php';
if (file_exists($profile_file)) {
    echo "<p class='success'>✓ $profile_file exists</p>";
    
    // Analyze the file structure
    $profile_content = file_get_contents($profile_file);
    
    if (strpos($profile_content, 'admin-auth.php') !== false) {
        echo "<p class='success'>✓ File includes admin-auth.php</p>";
    } else {
        echo "<p class='error'>✗ File does not include admin-auth.php</p>";
    }
    
    if (strpos($profile_content, 'get_admin_profile') !== false) {
        echo "<p class='success'>✓ File uses get_admin_profile() function</p>";
    } else {
        echo "<p class='error'>✗ File does not use get_admin_profile() function</p>";
    }
    
    if (strpos($profile_content, 'avatar-upload') !== false) {
        echo "<p class='success'>✓ File contains avatar upload functionality</p>";
    } else {
        echo "<p class='error'>✗ File does not contain avatar upload functionality</p>";
    }
    
    if (strpos($profile_content, 'form method="POST"') !== false) {
        echo "<p class='success'>✓ File contains form with POST method</p>";
    } else {
        echo "<p class='error'>✗ File does not contain form with POST method</p>";
    }
} else {
    echo "<p class='error'>✗ $profile_file does not exist</p>";
}
echo "</div>";

// Session check
echo "<div class='section'>";
echo "<h2>Session Variables Check</h2>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION)) {
    echo "<p class='success'>✓ Session variables exist</p>";
    echo "<table>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    
    foreach ($_SESSION as $key => $value) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($key) . "</td>";
        echo "<td>";
        if (is_array($value)) {
            echo "<pre>" . htmlspecialchars(print_r($value, true)) . "</pre>";
        } else {
            echo htmlspecialchars(is_string($value) ? $value : var_export($value, true));
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check admin ID
    if (isset($_SESSION['admin_id'])) {
        echo "<p class='success'>✓ admin_id is set: " . $_SESSION['admin_id'] . "</p>";
    } else {
        echo "<p class='error'>✗ admin_id is not set in session</p>";
    }
} else {
    echo "<p class='error'>✗ No session variables found - you may not be logged in</p>";
}
echo "</div>";

// Summary and recommendations
echo "<div class='section'>";
echo "<h2>Summary and Recommendations</h2>";

// Create a simple checklist for implementation
echo "<h3>Implementation Checklist</h3>";
echo "<ol>";
echo "<li>" . (function_exists('get_admin_profile') ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Update admin-auth.php with get_admin_profile() function</li>";

$has_profile_columns = false;
if (function_exists('get_db_connection')) {
    try {
        $db = get_db_connection();
        $stmt = $db->query("SHOW COLUMNS FROM admins LIKE 'full_name'");
        $has_profile_columns = $stmt->rowCount() > 0;
    } catch (Exception $e) {
        // Ignore for checklist
    }
}
echo "<li>" . ($has_profile_columns ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Add profile columns to admins table</li>";

$has_activity_log = false;
if (function_exists('get_db_connection')) {
    try {
        $db = get_db_connection();
        $stmt = $db->query("SHOW TABLES LIKE 'admin_activity_log'");
        $has_activity_log = $stmt->rowCount() > 0;
    } catch (Exception $e) {
        // Ignore for checklist
    }
}
echo "<li>" . ($has_activity_log ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Create admin_activity_log table</li>";

$has_profile_dir = file_exists("media-library/admin-profiles");
echo "<li>" . ($has_profile_dir ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Create profile images directory</li>";

$has_profile_page = file_exists('admin-profile.php');
echo "<li>" . ($has_profile_page ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . " Add admin-profile.php file</li>";

echo "</ol>";

// Provide recommendations
echo "<h3>Next Steps</h3>";
echo "<ul>";

if (!function_exists('get_admin_profile')) {
    echo "<li><strong>Add profile functions to admin-auth.php:</strong> The get_admin_profile() function is missing.</li>";
}

if (!$has_profile_columns) {
    echo "<li><strong>Run SQL to add profile columns:</strong> The admins table needs additional columns.</li>";
}

if (!$has_activity_log) {
    echo "<li><strong>Create activity log table:</strong> The admin_activity_log table needs to be created.</li>";
}

if (!$has_profile_dir) {
    echo "<li><strong>Create profile directory:</strong> Create the media-library/admin-profiles directory.</li>";
}

echo "</ul>";

echo "</div>";

echo "<p><strong>Note:</strong> Delete this diagnostic file after use for security reasons.</p>";
echo "</body></html>";
