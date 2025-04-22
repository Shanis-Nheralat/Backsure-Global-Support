<?php
/**
 * Admin Login Process
 * Handles admin authentication with enhanced security
 */

// IMPORTANT: No whitespace, comments, or output before this point!
// Start session first thing
session_start();

// Enable error logging - but don't display errors
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Create a debug log file
function debug_log($message) {
    file_put_contents('login_debug.txt', date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

// Start debug logging
debug_log("Login process started");
debug_log("POST data: " . print_r($_POST, true));

// Include database configuration
debug_log("Including config files");
try {
    require_once 'db_config.php';
    debug_log("db_config.php included successfully");
} catch (Exception $e) {
    debug_log("Error including db_config.php: " . $e->getMessage());
    header("Location: admin-login.php?error=" . urlencode("Configuration error: " . $e->getMessage()));
    exit;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    header("Location: admin-login.php?error=" . urlencode("Invalid request method."));
    exit;
}

// Get login data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;

debug_log("Login attempt for username: " . $username);

// Validate inputs
$errors = [];

if (empty($username)) {
    $errors[] = 'Username is required.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

// If there are validation errors, return them
if (!empty($errors)) {
    debug_log("Validation errors: " . implode(', ', $errors));
    header("Location: admin-login.php?error=" . urlencode(implode(' ', $errors)));
    exit;
}

try {
    debug_log("Attempting database connection using get_db_connection()");
    // Use the function from db_config.php
    $pdo = get_db_connection();
    debug_log("Database connection successful");
    
    // Check if admins table exists
    debug_log("Checking if 'admins' table exists");
    $tables_stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    $table_exists = $tables_stmt->fetchColumn();
    
    if (!$table_exists) {
        debug_log("'admins' table does not exist. Checking 'admin_users' table.");
        $tables_stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
        $admin_users_exists = $tables_stmt->fetchColumn();
        
        if (!$admin_users_exists) {
            debug_log("Neither 'admins' nor 'admin_users' table exists!");
            header("Location: admin-login.php?error=" . urlencode("Admin system not set up properly. Tables do not exist."));
            exit;
        } else {
            debug_log("Using 'admin_users' table instead.");
            $table_name = 'admin_users';
        }
    } else {
        debug_log("Using 'admins' table.");
        $table_name = 'admins';
    }
    
    // Check if username is an email
    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
    debug_log("Is username an email? " . ($isEmail ? "Yes" : "No"));
    
    // Find admin by username or email
    if ($isEmail) {
        debug_log("Searching for admin by email in $table_name table");
        $stmt = $pdo->prepare("SELECT id, username, email, password, role, status, login_attempts, last_attempt_time FROM $table_name WHERE email = ?");
    } else {
        debug_log("Searching for admin by username in $table_name table");
        $stmt = $pdo->prepare("SELECT id, username, email, password, role, status, login_attempts, last_attempt_time FROM $table_name WHERE username = ?");
    }
    
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // If admin not found or is blocked
    if (!$admin) {
        debug_log("Admin not found");
        // For security, use the same message for non-existent accounts to prevent user enumeration
        header("Location: admin-login.php?error=" . urlencode("Invalid username or password."));
        exit;
    }
    
    debug_log("Admin found: " . print_r($admin, true));
    
    // Check if account is blocked
    if (isset($admin['status']) && $admin['status'] === 'blocked') {
        debug_log("Admin account is blocked");
        header("Location: admin-login.php?error=" . urlencode("Your account has been blocked. Please contact support."));
        exit;
    }
    
    // Verify password
    debug_log("Verifying password");
    if (!password_verify($password, $admin['password'])) {
        debug_log("Password verification failed");
        
        // Increment login attempts if that column exists
        if (isset($admin['login_attempts'])) {
            debug_log("Incrementing login attempts");
            $stmt = $pdo->prepare("UPDATE $table_name SET login_attempts = login_attempts + 1, last_attempt_time = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);
        }
        
        header("Location: admin-login.php?error=" . urlencode("Invalid username or password."));
        exit;
    }
    
    debug_log("Password verified successfully");
    
    // Reset login attempts on successful login if that column exists
    if (isset($admin['login_attempts'])) {
        debug_log("Resetting login attempts");
        $stmt = $pdo->prepare("UPDATE $table_name SET login_attempts = 0, last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);
    }
    
    // Set session variables
    debug_log("Setting session variables");
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'] ?? '';
    $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
    
    // Set current IP for session security
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    
    // Generate session token for CSRF protection
    $_SESSION['token'] = bin2hex(random_bytes(32));
    
    // Determine redirect based on role
    $redirectUrl = 'admin-dashboard.php';
    
    debug_log("Login successful, redirecting to $redirectUrl");
    
    // Direct redirect instead of JSON response
    header("Location: $redirectUrl");
    exit;
    
} catch (PDOException $e) {
    // Log detailed error
    debug_log("Database error: " . $e->getMessage());
    error_log('Admin login error: ' . $e->getMessage());
    
    header("Location: admin-login.php?error=" . urlencode("A database error occurred. Please try again later."));
    exit;
} catch (Exception $e) {
    debug_log("General error: " . $e->getMessage());
    error_log('Admin login general error: ' . $e->getMessage());
    
    header("Location: admin-login.php?error=" . urlencode("An error occurred. Please try again later."));
    exit;
}
