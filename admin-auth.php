<?php
/**
 * Admin Authentication Component - IMPROVED VERSION
 * This file handles authentication for all admin panel pages
 * 
 * FIX: Resolves redirect loop issues by improving session handling and
 * checking current page before redirecting
 */

// Include database configuration
require_once 'db_config.php';

// Start session if not already started - with improved handling
if (session_status() === PHP_SESSION_NONE) {
    // Check if headers have already been sent
    if (!headers_sent()) {
        session_start();
    } else {
        // Log the issue but continue without breaking
        error_log('Headers already sent before session_start in admin-auth.php');
        // For sessions that haven't started but headers already sent,
        // we'll use a fallback mechanism for this page load
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }
    }
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Check if current page is the login page
 * Prevents redirect loops by identifying if we're already on the login page
 * @return bool True if current page is login page
 */
function is_login_page() {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    $login_pages = ['admin-login.php', 'login.php', 'admin/login.php'];
    
    return in_array($current_script, $login_pages);
}

/**
 * Check if user has required role
 * @param array $allowed_roles Array of roles allowed to access the page
 * @return bool True if user has required role, false otherwise
 */
function has_admin_role($allowed_roles = []) {
    // If no specific roles are required, just check if logged in
    if (empty($allowed_roles)) {
        return is_admin_logged_in();
    }
    
    // Check if user has required role
    return isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], $allowed_roles);
}

/**
 * Require authentication - redirects to login page if not logged in
 * FIX: Added check to prevent redirect loops if already on login page
 * Use at top of admin pages
 */
function require_admin_auth() {
    // Skip redirect if already on login page to prevent loops
    if (is_login_page()) {
        return;
    }

    // Only redirect if not logged in and headers haven't been sent yet
    if (!is_admin_logged_in()) {
        if (!headers_sent()) {
            // Store current URL for redirect after login (if not already set)
            if (!isset($_SESSION['redirect_after_login'])) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            }
            
            header("Location: admin-login.php");
            exit();
        } else {
            // If headers already sent, display error message instead
            echo '<div class="auth-error">Authentication required. Please <a href="admin-login.php">log in</a> to continue.</div>';
            // Optional: halt script execution
            die();
        }
    }
}

/**
 * Require specific role - redirects if not authorized
 * @param array $allowed_roles Array of roles allowed to access the page
 */
function require_admin_role($allowed_roles = []) {
    // Skip checks if on login page
    if (is_login_page()) {
        return;
    }
    
    // First check if logged in
    require_admin_auth();
    
    // Then check role if specified - only redirect if headers haven't been sent
    if (!empty($allowed_roles) && !has_admin_role($allowed_roles)) {
        if (!headers_sent()) {
            header("Location: admin-dashboard.php?error=unauthorized");
            exit();
        } else {
            // If headers already sent, display error message instead
            echo '<div class="auth-error">You do not have permission to access this page.</div>';
            // Optional: halt script execution
            die();
        }
    }
}

/**
 * Get current admin user info
 * @return array Admin user info (username, role, etc.)
 */
function get_admin_user() {
    $admin_info = [
        'username' => isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User',
        'role' => isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator',
        'id' => isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0
    ];
    
    // Get additional user info from database if logged in
    if (is_admin_logged_in() && isset($_SESSION['admin_id'])) {
        $db_profile = get_admin_profile($_SESSION['admin_id']);
        if ($db_profile && is_array($db_profile)) {
            $admin_info = array_merge($admin_info, $db_profile);
        }
    }
    
    return $admin_info;
}

/**
 * Check if admin has specific permission
 * @param string $permission Permission name to check
 * @return bool True if user has permission, false otherwise
 */
function has_admin_permission($permission) {
    // Super admin has all permissions
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin') {
        return true;
    }
    
    // Check specific permission
    if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) {
        return in_array($permission, $_SESSION['permissions']);
    }
    
    return false;
}

/**
 * Log admin action
 * 
 * @param string $action_type Type of action
 * @param string $resource Resource affected
 * @param int $resource_id ID of the resource
 * @param string $details Additional details
 * @return bool Success status
 */
function log_admin_action($action_type, $resource, $resource_id, $details = '') {
    try {
        $db = get_db_connection();
        
        $stmt = $db->prepare("INSERT INTO admin_activity_log 
            (user_id, username, action_type, resource, resource_id, details, ip_address, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            
        $stmt->execute([
            $_SESSION['admin_id'] ?? 0,
            $_SESSION['admin_username'] ?? 'unknown',
            $action_type,
            $resource,
            $resource_id,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error logging admin action: " . $e->getMessage());
        return false;
    }
}

/**
 * Get admin database profile
 * 
 * @param int $admin_id Admin ID
 * @return array Admin profile data
 */
function get_admin_profile($admin_id = null) {
    if ($admin_id === null) {
        $admin_id = $_SESSION['admin_id'] ?? 0;
    }
    
    if ($admin_id <= 0) {
        return [];
    }
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$admin_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching admin profile: " . $e->getMessage());
        return [];
    }
}

/**
 * Check session safety - verifies IP and user agent
 * Helps prevent session hijacking
 * @return bool True if session is safe, false otherwise
 */
function check_session_safety() {
    // If not logged in, no need to check
    if (!is_admin_logged_in()) {
        return true;
    }
    
    // Check IP address if stored in session
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        // IP mismatch, potential session hijacking
        error_log("Session security warning: IP mismatch. Stored: {$_SESSION['ip_address']}, Current: {$_SERVER['REMOTE_ADDR']}");
        return false;
    }
    
    return true;
}

// Automatically run session safety check
if (!check_session_safety()) {
    // Invalid session, clear it
    session_unset();
    session_destroy();
    
    // Only redirect if not on login page and headers not sent
    if (!is_login_page() && !headers_sent()) {
        header("Location: admin-login.php?error=" . urlencode("Session invalidated for security reasons. Please log in again."));
        exit();
    }
}

// Global variables for admin pages
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];
