<?php
/**
 * Database Configuration
 * 
 * Central configuration file for database connections.
 * Include this file in any script that needs database access.
 */

// Database credentials
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'bsg_support');   // Database name
define('DB_USER', 'bsg_user');      // Database username
define('DB_PASS', 'password');      // Database password - CHANGE THIS TO A SECURE PASSWORD!

/**
 * Get database connection
 * 
 * @return PDO Database connection
 * @throws PDOException if connection fails
 */
function get_db_connection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Log error - this keeps credentials out of error messages
        error_log("Database connection error: " . $e->getMessage());
        throw new PDOException("Database connection failed. Please check the error log for details.");
    }
}

/**
 * Quick test function to verify database connection
 * 
 * @return bool True if connection successful, false otherwise
 */
function test_db_connection() {
    try {
        $db = get_db_connection();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>
