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

/**
 * Get database connection
 * 
 * @return PDO Database connection
 * @throws PDOException if connection fails
 */
function get_db_connection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    try {
        $dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $db_user, $db_pass, $options);
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
