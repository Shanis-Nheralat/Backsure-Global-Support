<?php
/**
 * Database Configuration
 * 
 * Central configuration file for database connections.
 * Include this file in any script that needs database access.
 */

// Database credentials from your cPanel
$db_host = 'localhost';             // Database host
$db_name = 'backzvsg_playground';   // Your database name
$db_user = 'backzvsg_site';         // Your database username
$db_pass = 'Pc*C^y]_ZnzU';          // Your database password

// For backwards compatibility with systems expecting these variables
define('DB_HOST', $db_host);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_pass);

/**
 * Get database connection
 * 
 * @return PDO Database connection
 * @throws PDOException if connection fails
 */
function get_db_connection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    static $db_connection = null;
    
    // Return existing connection if it exists
    if ($db_connection instanceof PDO) {
        return $db_connection;
    }
    
    try {
        $dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            // Add connection pooling for better performance
            PDO::ATTR_PERSISTENT => true
        ];
        $db_connection = new PDO($dsn, $db_user, $db_pass, $options);
        return $db_connection;
    } catch (PDOException $e) {
        // Log error - this keeps credentials out of error messages
        error_log("Database connection error: " . $e->getMessage());
        throw new PDOException("Database connection failed. Please check the error log for details.");
    }
}

// For backward compatibility with scripts that expect $pdo directly
try {
    $pdo = get_db_connection();
} catch (PDOException $e) {
    // Log the error but don't stop execution
    error_log("Database connection error in db_config.php: " . $e->getMessage());
    $pdo = null;
}

/**
 * Get mysqli connection (for backward compatibility)
 * 
 * @return mysqli Database connection
 */
function get_mysqli_connection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    static $mysqli_connection = null;
    
    // Return existing connection if it exists
    if ($mysqli_connection instanceof mysqli) {
        return $mysqli_connection;
    }
    
    // Create new connection
    $mysqli_connection = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check for connection error
    if ($mysqli_connection->connect_error) {
        error_log("mysqli connection error: " . $mysqli_connection->connect_error);
        throw new Exception("Database connection failed. Please check the error log for details.");
    }
    
    // Set charset
    $mysqli_connection->set_charset("utf8");
    
    return $mysqli_connection;
}

// Add this to fix potential issues with config-loader.php
// This prevents other scripts from trying to establish another connection with wrong credentials
$db = $pdo;
$mysqli = get_mysqli_connection();
$dbhost = $db_host;
$dbuser = $db_user;
$dbpass = $db_pass;
$dbname = $db_name;

// Prevent direct script access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.0 403 Forbidden");
    exit("Direct access forbidden.");
}
