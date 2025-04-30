<?php
/**
 * Admin Authentication Component
 * This file handles authentication for all admin panel pages
 */

// Include database configuration
require_once 'db_config.php';

// Start session if not already started - with header check
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
 * Use at top of admin pages
 */
function require_admin_auth() {
    // Only redirect if headers haven't been sent yet
    if (!is_admin_logged_in()) {
        if (!headers_sent()) {
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

// Global variables for admin pages
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];
