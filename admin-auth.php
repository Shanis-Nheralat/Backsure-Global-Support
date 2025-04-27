<?php
/**
 * Admin Authentication Component
 * This file handles authentication for all admin panel pages
 */

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

// Global variables for admin pages
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];
