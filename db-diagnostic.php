<?php
// File: db-diagnostic.php
// Place this file in your root directory and access it via browser

// Enable error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2, h3 { color: #062767; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Database Connection Diagnostic</h1>";

// Check for 'root' credentials in files
echo "<h2>1. Checking for hardcoded root credentials</h2>";
$files_to_check = [
    'db_config.php',
    'config-loader.php',
    'admin-login-process.php',
    'admin-auth.php',
    'config.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (preg_match('/[\'"]root[\'"]/i', $content)) {
            echo "<p class='warning'>⚠️ Found 'root' in {$file}! This might be causing the issue.</p>";
            
            // Show specific lines with 'root'
            $lines = explode("\n", $content);
            echo "<pre>";
            foreach ($lines as $i => $line) {
                if (stripos($line, 'root') !== false) {
                    echo "Line " . ($i + 1) . ": " . htmlspecialchars($line) . "\n";
                }
            }
            echo "</pre>";
        } else {
            echo "<p class='success'>✓ No 'root' credentials found in {$file}</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ File {$file} not found</p>";
    }
}

// Test db_config.php connection
echo "<h2>2. Testing db_config.php connection</h2>";
try {
    if (file_exists('db_config.php')) {
        require_once 'db_config.php';
        echo "<p class='success'>✓ db_config.php loaded successfully</p>";
        
        // Check variables
        echo "<h3>Database variables:</h3>";
        echo "<table>";
        echo "<tr><th>Variable</th><th>Value</th></tr>";
        echo "<tr><td>db_host</td><td>" . (isset($db_host) ? htmlspecialchars($db_host) : 'Not set') . "</td></tr>";
        echo "<tr><td>db_name</td><td>" . (isset($db_name) ? htmlspecialchars($db_name) : 'Not set') . "</td></tr>";
        echo "<tr><td>db_user</td><td>" . (isset($db_user) ? htmlspecialchars($db_user) : 'Not set') . "</td></tr>";
        echo "<tr><td>db_pass</td><td>" . (isset($db_pass) ? '********' : 'Not set') . "</td></tr>";
        echo "</table>";
        
        // Test connection
        try {
            $db = get_db_connection();
            echo "<p class='success'>✓ Connection successful using get_db_connection()</p>";
            
            // Test a query
            $stmt = $db->query("SELECT DATABASE()");
            $current_db = $stmt->fetchColumn();
            echo "<p>Currently connected to database: <strong>{$current_db}</strong></p>";
            
            // Check global $pdo
            if (isset($pdo) && $pdo instanceof PDO) {
                echo "<p class='success'>✓ Global \$pdo variable is properly set</p>";
            } else {
                echo "<p class='error'>✗ Global \$pdo variable is NOT properly set</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>✗ Connection failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>✗ db_config.php not found!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error loading db_config.php: " . $e->getMessage() . "</p>";
}

// Test table structure
echo "<h2>3. Checking database tables</h2>";
try {
    if (isset($db) && $db instanceof PDO) {
        // Check for admins table
        $tables_stmt = $db->query("SHOW TABLES LIKE 'admins'");
        $admins_exists = $tables_stmt->fetchColumn();
        
        // Check for admin_users table
        $tables_stmt = $db->query("SHOW TABLES LIKE 'admin_users'");
        $admin_users_exists = $tables_stmt->fetchColumn();
        
        if ($admins_exists) {
            echo "<p class='success'>✓ 'admins' table exists</p>";
            
            // Check for required columns in admins table
            $cols_stmt = $db->query("DESCRIBE admins");
            $cols = $cols_stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>Structure of 'admins' table:</h3>";
            echo "<ul>";
            foreach ($cols as $col) {
                echo "<li>{$col}</li>";
            }
            echo "</ul>";
            
            // Check for required columns
            $required_cols = ['id', 'username', 'email', 'password', 'role', 'status'];
            $missing_cols = array_diff($required_cols, $cols);
            
            if (!empty($missing_cols)) {
                echo "<p class='warning'>⚠️ Missing columns in 'admins' table: " . implode(', ', $missing_cols) . "</p>";
            } else {
                echo "<p class='success'>✓ All required columns exist in 'admins' table</p>";
            }
        } else {
            echo "<p class='warning'>⚠️ 'admins' table does not exist</p>";
        }
        
        if ($admin_users_exists) {
            echo "<p class='success'>✓ 'admin_users' table exists</p>";
            
            // Check for required columns in admin_users table
            $cols_stmt = $db->query("DESCRIBE admin_users");
            $cols = $cols_stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>Structure of 'admin_users' table:</h3>";
            echo "<ul>";
            foreach ($cols as $col) {
                echo "<li>{$col}</li>";
            }
            echo "</ul>";
            
            // Check for required columns
            $required_cols = ['id', 'username', 'email', 'password', 'role', 'status'];
            $missing_cols = array_diff($required_cols, $cols);
            
            if (!empty($missing_cols)) {
                echo "<p class='warning'>⚠️ Missing columns in 'admin_users' table: " . implode(', ', $missing_cols) . "</p>";
            } else {
                echo "<p class='success'>✓ All required columns exist in 'admin_users' table</p>";
            }
        } else {
            echo "<p class='warning'>⚠️ 'admin_users' table does not exist</p>";
        }
        
        if (!$admins_exists && !$admin_users_exists) {
            echo "<p class='error'>✗ Neither 'admins' nor 'admin_users' table exists!</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error checking tables: " . $e->getMessage() . "</p>";
}

// Test config-loader.php
echo "<h2>4. Testing config-loader.php impact</h2>";
if (file_exists('config-loader.php')) {
    echo "<p>config-loader.php exists and might be affecting your connection:</p>";
    
    // Save original variables if they exist
    $original_db_host = isset($db_host) ? $db_host : null;
    $original_db_name = isset($db_name) ? $db_name : null;
    $original_db_user = isset($db_user) ? $db_user : null;
    $original_db_pass = isset($db_pass) ? $db_pass : null;
    
    try {
        require_once 'config-loader.php';
        
        // Check if variables changed
        echo "<table>";
        echo "<tr><th>Variable</th><th>Before loading config-loader.php</th><th>After loading config-loader.php</th></tr>";
        echo "<tr><td>db_host</td><td>" . htmlspecialchars($original_db_host ?? 'Not set') . "</td><td>" . htmlspecialchars($db_host ?? 'Not set') . "</td></tr>";
        echo "<tr><td>db_user</td><td>" . htmlspecialchars($original_db_user ?? 'Not set') . "</td><td>" . htmlspecialchars($db_user ?? 'Not set') . "</td></tr>";
        echo "<tr><td>db_name</td><td>" . htmlspecialchars($original_db_name ?? 'Not set') . "</td><td>" . htmlspecialchars($db_name ?? 'Not set') . "</td></tr>";
        echo "<tr><td>db_pass</td><td>" . ($original_db_pass ? '********' : 'Not set') . "</td><td>" . ($db_pass ? '********' : 'Not set') . "</td></tr>";
        echo "</table>";
        
        if ($original_db_user !== $db_user) {
            echo "<p class='error'>✗ config-loader.php changed the db_user from '{$original_db_user}' to '{$db_user}'!</p>";
        }
        
        // Check for $db variable
        if (isset($db)) {
            echo "<p class='warning'>⚠️ config-loader.php sets a \$db variable which might conflict with your PDO connection</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error loading config-loader.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='success'>✓ config-loader.php not found (this is good, less chance of conflicts)</p>";
}

// Test admin-auth.php database usage
echo "<h2>5. Checking admin-auth.php database usage</h2>";
if (file_exists('admin-auth.php')) {
    $content = file_get_contents('admin-auth.php');
    
    if (stripos($content, 'get_db_connection') !== false) {
        echo "<p class='success'>✓ admin-auth.php uses get_db_connection()</p>";
    } else {
        echo "<p class='warning'>⚠️ admin-auth.php might not be using get_db_connection()</p>";
    }
    
    // Check database table references
    preg_match_all('/FROM\s+[\'"`]?([a-z0-9_]+)[\'"`]?/i', $content, $matches);
    
    if (!empty($matches[1])) {
        echo "<p>Tables referenced in admin-auth.php:</p>";
        echo "<ul>";
        foreach (array_unique($matches[1]) as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>✗ admin-auth.php not found!</p>";
}

// Simulate login process
echo "<h2>6. Simulating login process database connection</h2>";
try {
    // Reset connection variables
    unset($pdo);
    unset($db);
    
    // Load db_config.php (like admin-login-process.php does)
    require_once 'db_config.php';
    
    // Attempt connection like login process would
    $login_pdo = get_db_connection();
    echo "<p class='success'>✓ Login process connection successful</p>";
    
    // Check for login table
    $tables_stmt = $login_pdo->query("SHOW TABLES LIKE 'admins'");
    $admins_exists = $tables_stmt->fetchColumn();
    
    if (!$admins_exists) {
        $tables_stmt = $login_pdo->query("SHOW TABLES LIKE 'admin_users'");
        $admin_users_exists = $tables_stmt->fetchColumn();
        
        $table_name = $admin_users_exists ? 'admin_users' : '';
    } else {
        $table_name = 'admins';
    }
    
    if (!empty($table_name)) {
        // Try to find a username
        $stmt = $login_pdo->prepare("SELECT COUNT(*) FROM `{$table_name}`");
        $stmt->execute();
        $user_count = $stmt->fetchColumn();
        
        echo "<p>Found {$user_count} users in '{$table_name}' table</p>";
        
        if ($user_count > 0) {
            // Try to find the specific user
            $stmt = $login_pdo->prepare("SELECT id, username, email FROM `{$table_name}` WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute(['shanisbsg', 'shanisbsg']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p class='success'>✓ Found user 'shanisbsg' in database!</p>";
            } else {
                echo "<p class='warning'>⚠️ User 'shanisbsg' not found in database</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error in login process simulation: " . $e->getMessage() . "</p>";
}

// Final recommendations
echo "<h2>7. Diagnosis and Recommendations</h2>";
echo "<p>Based on the tests above, here are the likely issues and solutions:</p>";
echo "<ol>";
echo "<li>If you're seeing 'root' credentials in any files, those need to be updated to use your correct credentials</li>";
echo "<li>If config-loader.php is overwriting your database credentials, consider modifying it to respect existing connections</li>";
echo "<li>If tables are missing, you need to create them using the SQL in your database-Schema.sql file</li>";
echo "<li>If required columns are missing from your tables, add them using ALTER TABLE statements</li>";
echo "</ol>";

echo "<h3>Solution template for db_config.php:</h3>";
echo "<p>Replace your current db_config.php with an improved version that prevents credential overwriting:</p>";

echo "<pre>";
echo "<?php
/**
 * Database Configuration
 * 
 * Central configuration file for database connections.
 * Include this file in any script that needs database access.
 */

// Database credentials from your cPanel
\$db_host = 'localhost';             // Database host
\$db_name = 'backzvsg_playground';   // Your database name
\$db_user = 'backzvsg_site';         // Your database username
\$db_pass = 'Pc*C^y]_ZnzU';          // Your database password

// For backwards compatibility
define('DB_HOST', \$db_host);
define('DB_NAME', \$db_name);
define('DB_USER', \$db_user);
define('DB_PASSWORD', \$db_pass);

/**
 * Get database connection
 * 
 * @return PDO Database connection
 * @throws PDOException if connection fails
 */
function get_db_connection() {
    global \$db_host, \$db_name, \$db_user, \$db_pass;
    
    static \$db_connection = null;
    
    // Return existing connection if it exists
    if (\$db_connection instanceof PDO) {
        return \$db_connection;
    }
    
    try {
        \$dsn = \"mysql:host=\" . \$db_host . \";dbname=\" . \$db_name . \";charset=utf8\";
        \$options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            // Add connection pooling for better performance
            PDO::ATTR_PERSISTENT => true
        ];
        \$db_connection = new PDO(\$dsn, \$db_user, \$db_pass, \$options);
        return \$db_connection;
    } catch (PDOException \$e) {
        // Log error - this keeps credentials out of error messages
        error_log(\"Database connection error: \" . \$e->getMessage());
        throw new PDOException(\"Database connection failed. Please check the error log for details.\");
    }
}

// For backward compatibility with scripts that expect \$pdo directly
try {
    \$pdo = get_db_connection();
} catch (PDOException \$e) {
    // Log the error but don't stop execution
    error_log(\"Database connection error in db_config.php: \" . \$e->getMessage());
    \$pdo = null;
}

// These variables help prevent overwriting by config-loader.php
\$db = \$pdo;
\$mysqli = null; // We're using PDO, not mysqli
\$dbhost = \$db_host;
\$dbuser = \$db_user;
\$dbpass = \$db_pass;
\$dbname = \$db_name;
</pre>";

echo "</body></html>";
