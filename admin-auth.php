<?php
/**
 * Admin Authentication
 * 
 * Central file for admin authentication across all admin pages.
 * This file should be included at the top of all admin pages.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Not logged in, redirect to login page
    header('Location: admin-login.php');
    exit;
}

// Include database configuration
require_once 'db_config.php';

// Get admin user data
function get_admin_user($admin_id) {
    try {
        $db = get_db_connection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$admin_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching admin user: ' . $e->getMessage());
        return false;
    }
}

// Check if user has specific role
function has_role($required_role) {
    if (!isset($_SESSION['admin_role'])) {
        return false;
    }
    
    // Admin role has access to everything
    if ($_SESSION['admin_role'] === 'admin') {
        return true;
    }
    
    // Check if user's role matches required role
    return $_SESSION['admin_role'] === $required_role;
}

// Get current admin user
$current_admin = isset($_SESSION['admin_id']) ? get_admin_user($_SESSION['admin_id']) : false;

// Check for admin message (for alerts/notifications)
$admin_message = null;
if (isset($_SESSION['admin_message'])) {
    $admin_message = $_SESSION['admin_message'];
    unset($_SESSION['admin_message']); // Clear message after reading
}
?>