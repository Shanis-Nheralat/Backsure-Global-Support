<?php
/**
 * Database Configuration
 * This file provides database connection functionality for the admin panel
 */

// Import main configuration if available
if (file_exists('config.php')) {
    require_once 'config.php';
}

/**
 * Get database connection
 * @return PDO Database connection
 */
function get_db_connection() {
    // Check if constants are defined from config.php
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD')) {
        $db_host = DB_HOST;
        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_pass = DB_PASSWORD;
    } else {
        // Fallback database configuration
        $db_host = 'localhost';
        $db_name = 'backsure_admin';  // Update this with your actual database name
        $db_user = 'shanis@backsureglobalsupport.com';  // Update this with your actual username
        $db_pass = 'lBzymn$l2h1$wpYoo9RV';  // Update this with your actual password
    }
    
    // Create DSN
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    
    // Set PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    // Create and return PDO instance
    try {
        return new PDO($dsn, $db_user, $db_pass, $options);
    } catch (PDOException $e) {
        // Log error but don't expose details
        error_log("Database connection error: " . $e->getMessage());
        throw new PDOException("Database connection failed. Please check your configuration.");
    }
}

// For backward compatibility with older scripts using direct $pdo variable
try {
    $pdo = get_db_connection();
} catch (PDOException $e) {
    // Log error but don't expose details in production
    error_log("Database connection error in db_config.php: " . $e->getMessage());
    
    // Only define the variable to prevent errors, but it won't work
    $pdo = null;
}
