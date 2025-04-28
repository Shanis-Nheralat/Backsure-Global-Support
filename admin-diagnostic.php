<?php
// admin-diagnostics.php
// Show PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Admin Panel Diagnostics</h1>";

// 1. Test Database Connection
echo "<h2>Database Connection Test:</h2>";
require_once 'db_config.php';

try {
    $pdo = get_db_connection();
    echo "<p style='color:green'>✓ Database connection successful</p>";
    
    // Check admin_users table
    echo "<h3>Database Tables:</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Check for admin_users table specifically
    $admin_table_exists = in_array('admin_users', $tables);
    echo "<p>" . ($admin_table_exists ? "✓ admin_users table exists" : "✗ admin_users table does not exist") . "</p>";
    
    if ($admin_table_exists) {
        // Check for admin users
        $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        echo "<p>Found $count admin users</p>";
        
        if ($count > 0) {
            // Show admin emails (without passwords)
            $admins = $pdo->query("SELECT id, name, email, role, status FROM admin_users")->fetchAll();
            echo "<h4>Admin Users:</h4>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr>";
                foreach ($admin as $key => $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:red'>✗ No admin users found. This is why login fails.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Database error: " . $e->getMessage() . "</p>";
}

// 2. Test Session
echo "<h2>Session Test:</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session variables:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 3. File Structure Test
echo "<h2>File Structure Test:</h2>";
$required_files = [
    'admin-login.php',
    'admin-login-process.php',
    'admin-dashboard.php',
    'admin-auth.php',
    'db_config.php'
];

foreach ($required_files as $file) {
    echo "<p>" . htmlspecialchars($file) . ": " . (file_exists($file) ? 
        "<span style='color:green'>✓ Exists</span>" : 
        "<span style='color:red'>✗ Missing</span>") . "</p>";
}

// 4. Include Paths Test
echo "<h2>Include Paths Test:</h2>";
$include_paths = explode(PATH_SEPARATOR, get_include_path());
echo "<ul>";
foreach ($include_paths as $path) {
    echo "<li>" . htmlspecialchars($path) . "</li>";
}
echo "</ul>";

// 5. Authentication Code Test
echo "<h2>Authentication Component Test:</h2>";
if (file_exists('admin-auth.php')) {
    echo "<p style='color:green'>✓ admin-auth.php exists</p>";
    
    // Show the first 10 lines of the file to verify content
    $auth_file = file('admin-auth.php', FILE_IGNORE_NEW_LINES);
    if ($auth_file) {
        echo "<p>First 10 lines of admin-auth.php:</p>";
        echo "<pre>";
        for ($i = 0; $i < min(10, count($auth_file)); $i++) {
            echo htmlspecialchars($auth_file[$i]) . "\n";
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color:red'>✗ admin-auth.php is missing</p>";
}

// 6. Login Process Test
echo "<h2>Login Process Test:</h2>";
if (file_exists('admin-login-process.php')) {
    echo "<p style='color:green'>✓ admin-login-process.php exists</p>";
    
    // Test the login functionality with a dummy admin
    if ($admin_table_exists && isset($pdo)) {
        echo "<h3>Testing admin login with default credentials:</h3>";
        try {
            // Check if default admin exists
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
            $stmt->execute(['admin@backsureglobalsupport.com']);
            $admin = $stmt->fetch();
            
            if ($admin) {
                echo "<p>Default admin exists with email: " . htmlspecialchars($admin['email']) . "</p>";
                
                // Test password hash
                $test_password = 'password';
                $password_verified = password_verify($test_password, $admin['password']);
                echo "<p>Password 'password' verification: " . 
                    ($password_verified ? 
                        "<span style='color:green'>✓ Correct</span>" : 
                        "<span style='color:red'>✗ Incorrect</span>") . "</p>";
                
                if (!$password_verified) {
                    echo "<p>Note: Default admin exists but password may not be 'password'. Try using the original password.</p>";
                }
            } else {
                echo "<p style='color:red'>✗ Default admin does not exist. You need to create an admin user.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>✗ Error checking admin user: " . $e->getMessage() . "</p>";
        }
    }
} else {
    echo "<p style='color:red'>✗ admin-login-process.php is missing</p>";
}

// 7. Component Structure Test
echo "<h2>Component Structure Test:</h2>";
$components = [
    'assets/css',
    'assets/js',
    'assets/images',
    'assets/lib',
    'includes'
];

foreach ($components as $component) {
    echo "<p>" . htmlspecialchars($component) . ": " . (is_dir($component) ? 
        "<span style='color:green'>✓ Exists</span>" : 
        "<span style='color:red'>✗ Missing</span>") . "</p>";
}

// Current directory structure
echo "<h2>Current Directory Structure:</h2>";
function scan_directory($directory, $level = 0) {
    $files = scandir($directory);
    $output = '';
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $directory . '/' . $file;
        $output .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . 
                  (is_dir($path) ? '📁 ' : '📄 ') . 
                  htmlspecialchars($file) . "<br>";
        
        if (is_dir($path) && $level < 3) { // Limit depth to avoid too much output
            $output .= scan_directory($path, $level + 1);
        }
    }
    
    return $output;
}

echo "<div style='font-family: monospace;'>";
echo scan_directory('.');
echo "</div>";

// Helpful links
echo "<h2>Next Steps:</h2>";
echo "<p><a href='admin-login.php'>Go to Admin Login</a></p>";
?>
