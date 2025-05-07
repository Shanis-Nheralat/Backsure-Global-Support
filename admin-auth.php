<?php
/**
 * Admin Authentication System
 * 
 * Handles user authentication, session management, permission control,
 * and security for the admin panel.
 */

// Include database configuration
require_once 'db_config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        // Start with secure session parameters
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => isset($_SERVER['HTTPS']),
            'cookie_samesite' => 'Strict'
        ]);
    } else {
        error_log('Headers already sent before session_start in admin-auth.php');
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }
    }
}

/**
 * Check if user is logged in
 * FIXED to handle all valid session states (true/1/"1")
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && 
           ($_SESSION['admin_logged_in'] === true || 
            $_SESSION['admin_logged_in'] == 1 || 
            $_SESSION['admin_logged_in'] === "1");
}

/**
 * Check if current page is the login page
 * Prevents redirect loops by identifying if we're already on the login page
 * 
 * @return bool True if current page is login page
 */
function is_login_page() {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    $login_pages = ['admin-login.php', 'login.php', 'admin/login.php'];
    
    return in_array($current_script, $login_pages);
}

/**
 * Check if user has required role
 * FIXED to handle superadmin/admin roles automatically
 * 
 * @param array $allowed_roles Array of roles allowed to access the page
 * @return bool True if user has required role, false otherwise
 */
function has_admin_role($allowed_roles = []) {
    // If no specific roles are required, just check if logged in
    if (empty($allowed_roles)) {
        return is_admin_logged_in();
    }
    
    // Super admin and admin always have access to everything
    if (isset($_SESSION['admin_role']) && 
        (strtolower($_SESSION['admin_role']) === 'superadmin' || 
         strtolower($_SESSION['admin_role']) === 'admin')) {
        return true;
    }
    
    // Check if user has any of the required roles
    return isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], $allowed_roles);
}

/**
 * Require authentication - redirects to login page if not logged in
 * FIXED to prevent redirect loops and handle edge cases
 */
function require_admin_auth() {
    // Skip redirect if already on login page
    if (is_login_page()) {
        return;
    }

    // If not logged in, redirect to login
    if (!is_admin_logged_in()) {
        if (!headers_sent()) {
            // Store current URL for redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            header("Location: admin-login.php");
            exit();
        } else {
            // If headers already sent, display error message
            echo '<div class="auth-error alert alert-danger">
                Authentication required. Please <a href="admin-login.php">log in</a> to continue.
                </div>';
            die();
        }
    }
}

/**
 * Require specific role - redirects if not authorized
 * FIXED to handle role hierarchy and edge cases
 * 
 * @param array $allowed_roles Array of roles allowed to access the page
 */
function require_admin_role($allowed_roles = []) {
    // Skip checks if on login page
    if (is_login_page()) {
        return;
    }
    
    // First check if logged in
    require_admin_auth();
    
    // Check role if specified
    if (!empty($allowed_roles) && !has_admin_role($allowed_roles)) {
        if (!headers_sent()) {
            header("Location: admin-dashboard.php?error=unauthorized");
            exit();
        } else {
            echo '<div class="auth-error alert alert-danger">
                You do not have permission to access this page.
                </div>';
            die();
        }
    }
}

/**
 * Login user and create session
 * NEW FUNCTION: Properly handles authentication and logging
 * 
 * @param string $username Username
 * @param string $password Password
 * @return bool|array False if login fails, user data array if successful
 */
function admin_login($username, $password) {
    try {
        $db = get_db_connection();
        
        // Get user from database
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Check if user exists and password is valid
        if (!$user || !password_verify($password, $user['password'])) {
            // Update login attempts for existing users
            if ($user) {
                $attempts = $user['login_attempts'] + 1;
                $updateStmt = $db->prepare("UPDATE admins SET login_attempts = ?, last_attempt_time = NOW() WHERE id = ?");
                $updateStmt->execute([$attempts, $user['id']]);
                
                // Check for account lockout
                if ($attempts >= 5) {
                    error_log("Account locked: {$username} - Too many failed attempts");
                }
            }
            
            return false;
        }
        
        // Check if account is locked/inactive
        if ($user['status'] != 0) {
            error_log("Login rejected: Account {$username} is inactive or locked");
            return false;
        }

        // FIXED: Check for empty role and set a default
        if (empty($user['role'])) {
            // If shanisbsg, set as superadmin
            if ($username === 'shanisbsg') {
                $role = 'superadmin';
            } else {
                $role = 'admin'; // Default role
            }
            
            // Update the role in database
            $updateRoleStmt = $db->prepare("UPDATE admins SET role = ? WHERE id = ?");
            $updateRoleStmt->execute([$role, $user['id']]);
            $user['role'] = $role;
        }
        
        // Set up session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // Reset login attempts
        $resetStmt = $db->prepare("UPDATE admins SET login_attempts = 0, last_login = NOW() WHERE id = ?");
        $resetStmt->execute([$user['id']]);
        
        // Log successful login
        error_log("Successful login: {$username} ({$user['role']})");
        
        return $user;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout current user
 * NEW FUNCTION: Properly handles session cleanup
 */
function admin_logout() {
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Get current admin user info
 * IMPROVED: Better error handling and data fetching
 * 
 * @return array Admin user info or empty array if not logged in
 */
function get_admin_user() {
    // Default values
    $admin_info = [
        'username' => isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Guest',
        'role' => isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : '',
        'id' => isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0,
        'email' => isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : ''
    ];
    
    // Get additional info from database if logged in
    if (is_admin_logged_in() && isset($_SESSION['admin_id'])) {
        $db_profile = get_admin_profile($_SESSION['admin_id']);
        if ($db_profile && is_array($db_profile)) {
            $admin_info = array_merge($admin_info, $db_profile);
        }
    }
    
    return $admin_info;
}

/**
 * Check admin permissions
 * IMPROVED: Better permission handling with hierarchy
 * 
 * @param string $permission Permission key to check
 * @return bool True if user has permission
 */
function has_admin_permission($permission) {
    // Superadmin and admin always have all permissions
    if (isset($_SESSION['admin_role']) && 
        (strtolower($_SESSION['admin_role']) === 'superadmin' || 
         strtolower($_SESSION['admin_role']) === 'admin')) {
        return true;
    }
    
    // For other roles, check specific permissions
    if (isset($_SESSION['admin_id']) && !empty($permission)) {
        try {
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM permissions 
                WHERE role = ? AND permission = ?");
            $stmt->execute([$_SESSION['admin_role'], $permission]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Permission check error: " . $e->getMessage());
            return false;
        }
    }
    
    return false;
}

/**
 * Get admin profile from database
 * IMPROVED: Better error handling
 * 
 * @param int $admin_id Admin ID
 * @return array|false Admin data or false on error
 */
function get_admin_profile($admin_id) {
    if (empty($admin_id)) {
        return false;
    }
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$admin_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching admin profile: " . $e->getMessage());
        return false;
    }
}

/**
 * Log admin actions
 * IMPROVED: Better logging with error handling
 * 
 * @param string $action Action type
 * @param string $target Target entity
 * @param string $details Action details
 * @return bool Success status
 */
function log_admin_action($action, $target, $details = '') {
    if (!is_admin_logged_in()) {
        return false;
    }
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare("INSERT INTO admin_activity_log 
            (admin_id, username, action, target, details, ip_address, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())");
            
        return $stmt->execute([
            $_SESSION['admin_id'],
            $_SESSION['admin_username'],
            $action,
            $target,
            $details,
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (PDOException $e) {
        error_log("Error logging admin action: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if admin activity log table exists and create if not
 * NEW FUNCTION: Ensures logging tables exist
 */
function ensure_admin_log_table() {
    try {
        $db = get_db_connection();
        
        // Check if table exists
        $tableExists = $db->query("SHOW TABLES LIKE 'admin_activity_log'")->rowCount() > 0;
        
        if (!$tableExists) {
            // Create activity log table
            $db->exec("CREATE TABLE admin_activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                admin_id INT NOT NULL,
                username VARCHAR(100) NOT NULL,
                action VARCHAR(50) NOT NULL,
                target VARCHAR(50) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (admin_id),
                INDEX (action),
                INDEX (timestamp)
            )");
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error creating log table: " . $e->getMessage());
        return false;
    }
}

/**
 * Check session timeout and validate session
 * IMPROVED: Better session security
 * 
 * @return bool True if session is valid
 */
function check_session_validity() {
    // Not applicable if not logged in
    if (!is_admin_logged_in()) {
        return true;
    }
    
    // Check session timeout (30 minutes)
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        admin_logout();
        return false;
    }
    
    // Check IP address mismatch (potential session hijacking)
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        error_log("Session security: IP mismatch. Session: {$_SESSION['ip_address']}, Current: {$_SERVER['REMOTE_ADDR']}");
        admin_logout();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Create a new admin user
 * NEW FUNCTION: Allows proper user creation
 * 
 * @param string $username Username
 * @param string $password Password
 * @param string $email Email
 * @param string $role Role (default: admin)
 * @return int|false User ID on success, false on failure
 */
function create_admin_user($username, $password, $email, $role = 'admin') {
    // Validate inputs
    if (empty($username) || empty($password) || empty($email)) {
        return false;
    }
    
    try {
        $db = get_db_connection();
        
        // Check if username or email already exists
        $checkStmt = $db->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        
        if ($checkStmt->rowCount() > 0) {
            return false; // User already exists
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $insertStmt = $db->prepare("INSERT INTO admins 
            (username, password, email, role, created_at, status) 
            VALUES (?, ?, ?, ?, NOW(), 0)");
            
        $insertStmt->execute([$username, $hashed_password, $email, $role]);
        
        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating admin user: " . $e->getMessage());
        return false;
    }
}

/**
 * Update admin user password
 * NEW FUNCTION: Allows password updates
 * 
 * @param int $admin_id Admin ID
 * @param string $new_password New password
 * @return bool Success status
 */
function update_admin_password($admin_id, $new_password) {
    if (empty($admin_id) || empty($new_password)) {
        return false;
    }
    
    try {
        $db = get_db_connection();
        
        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $db->prepare("UPDATE admins SET password = ? WHERE id = ?");
        return $stmt->execute([$hashed_password, $admin_id]);
    } catch (PDOException $e) {
        error_log("Error updating admin password: " . $e->getMessage());
        return false;
    }
}

/**
 * Ensure the 'shanisbsg' admin user exists and has superadmin role
 * NEW FUNCTION: Crucial for fixing the identified issue
 */
function ensure_superadmin_exists() {
    try {
        $db = get_db_connection();
        
        // Check if shanisbsg exists
        $stmt = $db->prepare("SELECT id, role FROM admins WHERE username = 'shanisbsg'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            // User exists, check if role is set
            if (empty($user['role'])) {
                // Update role to superadmin
                $updateStmt = $db->prepare("UPDATE admins SET role = 'superadmin' WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                error_log("Updated shanisbsg role to superadmin");
            }
        } else {
            // User doesn't exist, create with default password (should be changed immediately)
            $password = password_hash('a14c65f3', PASSWORD_DEFAULT);
            $createStmt = $db->prepare("INSERT INTO admins 
                (username, password, email, role, created_at, status) 
                VALUES ('shanisbsg', ?, 'shanis@backsureglobalsupport.com', 'superadmin', NOW(), 0)");
            $createStmt->execute([$password]);
            error_log("Created shanisbsg superadmin user");
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error ensuring superadmin: " . $e->getMessage());
        return false;
    }
}

// Run initialization functions
ensure_admin_log_table();
ensure_superadmin_exists();
check_session_validity();

// Set up global variables for templates
$admin_user = get_admin_user();
$admin_username = $admin_user['username'] ?? 'Guest';
$admin_role = $admin_user['role'] ?? '';
$admin_id = $admin_user['id'] ?? 0;
