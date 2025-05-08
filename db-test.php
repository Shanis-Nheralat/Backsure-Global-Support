<?php
/**
 * Database Connection Diagnostic Test
 * Upload this file to your server and access it via browser to diagnose database connection issues.
 */

// Enable detailed error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration from your implementation guide
$db_host = 'localhost';
$db_name = 'backsure_admin';
$db_user = 'shanis@backsureglobalsupport.com';
$db_pass = 'lBzymn$l2h1$wpYoo9RV';
$db_charset = 'utf8mb4';

// HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Database Connection Diagnostic Test</h1>
    <div class='section'>
        <h2>1. Testing PHP PDO Extension</h2>";

// Check if PDO is available
if (!extension_loaded('pdo')) {
    echo "<p class='error'>❌ PDO extension is not loaded. Please install or enable it.</p>";
} else {
    echo "<p class='success'>✅ PDO extension is loaded.</p>";
    
    // Check MySQL PDO driver
    if (!in_array('mysql', PDO::getAvailableDrivers())) {
        echo "<p class='error'>❌ PDO MySQL driver is not available.</p>";
    } else {
        echo "<p class='success'>✅ PDO MySQL driver is available.</p>";
    }
}

echo "</div>
    <div class='section'>
        <h2>2. Testing MySQL Server Connection</h2>";

// Test basic connection without database
try {
    $pdo_server = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    echo "<p class='success'>✅ Successfully connected to MySQL server at '$db_host'.</p>";
    
    // Test if user can create databases (optional)
    try {
        $pdo_server->exec("CREATE DATABASE IF NOT EXISTS test_temp_db");
        $pdo_server->exec("DROP DATABASE test_temp_db");
        echo "<p class='success'>✅ User has privileges to create databases.</p>";
    } catch (PDOException $e) {
        echo "<p class='warning'>⚠️ User cannot create databases (not always required): " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Failed to connect to MySQL server: " . $e->getMessage() . "</p>";
    echo "<p>This suggests your MySQL server is not running, or the username/password is incorrect, or the host is wrong.</p>";
}

echo "</div>
    <div class='section'>
        <h2>3. Testing Database Connection</h2>";

// Test connection with database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=$db_charset", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Successfully connected to database '$db_name'.</p>";
    
    // Check if all required tables exist
    $required_tables = ['users', 'roles', 'permissions', 'role_permissions', 'password_resets', 'admin_activity_log', 'settings'];
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Found tables: " . implode(", ", $tables) . "</p>";
    
    $missing_tables = array_diff($required_tables, $tables);
    if (empty($missing_tables)) {
        echo "<p class='success'>✅ All required tables exist.</p>";
    } else {
        echo "<p class='error'>❌ Missing required tables: " . implode(", ", $missing_tables) . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Failed to connect to database '$db_name': " . $e->getMessage() . "</p>";
    
    // Additional suggestions based on error message
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<p>The database '$db_name' does not exist. You need to create it.</p>";
    } elseif (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<p>The user '$db_user' does not have access to the database '$db_name'.</p>";
    }
}

echo "</div>
    <div class='section'>
        <h2>4. Configuration Analysis</h2>";

// Check hostname
echo "<p><strong>Hostname:</strong> $db_host";
if ($db_host === 'localhost') {
    echo " (using 'localhost' will use a socket connection, not TCP/IP)</p>";
} else {
    echo " (using a hostname will attempt a TCP/IP connection)</p>";
}

// Check if username format is valid
if (strpos($db_user, '@') !== false) {
    echo "<p class='warning'>⚠️ Your username contains '@' which might cause issues with some MySQL configurations.</p>";
}

// Check if password contains special characters
if (preg_match('/[\$\'"\\\\]/', $db_pass)) {
    echo "<p class='warning'>⚠️ Your password contains special characters which might need proper escaping.</p>";
}

echo "</div>
    <div class='section'>
        <h2>5. Recommendations</h2>
        <p>Based on the test results above, here are some suggestions:</p>
        <ul>";

// Add recommendations based on tests
if (!extension_loaded('pdo') || !in_array('mysql', PDO::getAvailableDrivers())) {
    echo "<li>Install or enable the PDO extension and MySQL driver</li>";
}

echo "<li>Check if your database server is running</li>";
echo "<li>Verify the database credentials in db.php</li>";
echo "<li>Ensure the database 'backsure_admin' exists</li>";
echo "<li>Check that the user has proper permissions</li>";
echo "<li>Consider using a simpler username without '@' symbol</li>";
echo "<li>Review your server's error logs for additional information</li>";

echo "</ul>
    </div>
</body>
</html>";
?>
