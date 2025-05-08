<?php
/**
 * Centralized Database Connection
 * Provides consistent database access across all admin files
 */

// Database credentials
$db_host = 'localhost';
$db_name = 'backsure_admin';  // From diagnostic report
$db_user = 'shanis@backsureglobalsupport.com';
$db_pass = 'lBzymn$l2h1$wpYoo9RV';
$db_charset = 'utf8mb4';

// Legacy variables for backward compatibility
global $conn, $db_connection, $connection, $database;

// Connection options for PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// DSN (Data Source Name)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

// Establish PDO connection
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    
    // Create mysqli connection for backward compatibility
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Map to common variable names for existing code
    $conn = $mysqli;
    $db_connection = $mysqli;
    $connection = $mysqli;
    $database = $mysqli;
    
} catch (PDOException $e) {
    // Log error and display user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}

/**
 * Helper function to execute a query and return a single value
 */
function db_get_value($query, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database Query Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Helper function to execute a query and return multiple rows
 */
function db_get_rows($query, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database Query Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to execute a query and return a single row
 */
function db_get_row($query, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database Query Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Helper function to execute an insert, update, or delete query
 */
function db_execute($query, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Database Query Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Helper function to get the last inserted ID
 */
function db_last_insert_id() {
    global $pdo;
    return $pdo->lastInsertId();
}
