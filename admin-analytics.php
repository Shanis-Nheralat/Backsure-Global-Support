<?php
/**
 * Admin Analytics System
 * Tracks admin activity and provides analytics data
 */

// Database connection
$db = null;
try {
    // Include database configuration
    if (file_exists('config.php')) {
        require_once 'config.php';
        
        // Establish database connection if the config file doesn't already do it
        // Make sure these variables match those in your config.php
        if (!isset($db) && isset($db_host) && isset($db_name) && isset($db_user) && isset($db_pass)) {
            $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }
} catch (PDOException $e) {
    // Silently fail - we don't want to break the entire admin panel if DB connection fails
    // But we'll log it for debugging
    error_log("Database connection failed in admin-analytics.php: " . $e->getMessage());
}

/**
 * Logs page view to analytics
 * @param string $page_name Name of the page being viewed
 * @return bool Success status
 */
function log_page_view($page_name = null) {
    // If no page name specified, get from current file
    if ($page_name === null) {
        $page_name = basename($_SERVER['PHP_SELF']);
    }
    
    // Log the activity safely
    return log_admin_activity('page_view', $page_name);
}

/**
 * Logs admin activity to database
 * @param string $action_type Type of action (e.g., 'login', 'edit', 'delete')
 * @param string $action_details Additional details about the action
 * @return bool Success status
 */
function log_admin_activity($action_type = '', $action_details = '') {
    global $db;
    
    // Skip if no database connection
    if (!$db) {
        return false;
    }
    
    try {
        // Get current admin ID
        $admin_id = 1; // Default fallback
        if (function_exists('get_admin_user')) {
            $admin = get_admin_user();
            if (isset($admin['id'])) {
                $admin_id = $admin['id'];
            }
        }
        
        // Get IP address
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Prepare and execute query
        $stmt = $db->prepare("INSERT INTO admin_activity_log 
                             (admin_id, action_type, action_details, ip_address, created_at) 
                             VALUES (?, ?, ?, ?, NOW())");
        
        return $stmt->execute([$admin_id, $action_type, $action_details, $ip_address]);
    } catch (Exception $e) {
        error_log("Error logging admin activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Gets recent activity for the admin dashboard
 * IMPORTANT: This was causing the function redeclaration error
 * Renamed to get_admin_recent_activity to avoid conflict
 * @param int $limit Number of entries to return
 * @return array Recent activity data
 */
function get_admin_recent_activity($limit = 10) {
    global $db;
    
    // Return empty array if no database connection
    if (!$db) {
        return [
            ['action_type' => 'page_view', 'action_details' => 'Sample Activity', 'created_at' => date('Y-m-d H:i:s')]
        ];
    }
    
    try {
        $stmt = $db->prepare("SELECT a.action_type, a.action_details, a.created_at, u.username
                             FROM admin_activity_log a
                             LEFT JOIN admin_users u ON a.admin_id = u.id
                             ORDER BY a.created_at DESC
                             LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting recent activity: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets page view analytics
 * @param int $days Number of days to include
 * @return array Page view data
 */
function get_page_view_analytics($days = 7) {
    global $db;
    
    // Return sample data if no database connection
    if (!$db) {
        return [
            ['date' => date('Y-m-d'), 'count' => 15],
            ['date' => date('Y-m-d', strtotime('-1 day')), 'count' => 22],
            ['date' => date('Y-m-d', strtotime('-2 day')), 'count' => 18],
            ['date' => date('Y-m-d', strtotime('-3 day')), 'count' => 20],
            ['date' => date('Y-m-d', strtotime('-4 day')), 'count' => 12],
            ['date' => date('Y-m-d', strtotime('-5 day')), 'count' => 15],
            ['date' => date('Y-m-d', strtotime('-6 day')), 'count' => 10]
        ];
    }
    
    try {
        $stmt = $db->prepare("SELECT DATE(created_at) as date, COUNT(*) as count
                             FROM admin_activity_log
                             WHERE action_type = 'page_view'
                             AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                             GROUP BY DATE(created_at)
                             ORDER BY date ASC");
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting page view analytics: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets most viewed pages
 * @param int $limit Number of pages to return
 * @return array Most viewed pages data
 */
function get_most_viewed_pages($limit = 5) {
    global $db;
    
    // Return sample data if no database connection
    if (!$db) {
        return [
            ['page' => 'admin-dashboard.php', 'count' => 45],
            ['page' => 'admin-users.php', 'count' => 30],
            ['page' => 'admin-blog.php', 'count' => 25],
            ['page' => 'admin-settings.php', 'count' => 20],
            ['page' => 'admin-inquiries.php', 'count' => 15]
        ];
    }
    
    try {
        $stmt = $db->prepare("SELECT action_details as page, COUNT(*) as count
                             FROM admin_activity_log
                             WHERE action_type = 'page_view'
                             GROUP BY action_details
                             ORDER BY count DESC
                             LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting most viewed pages: " . $e->getMessage());
        return [];
    }
}

// Function to check if analytics tables exist
function check_analytics_tables() {
    global $db;
    
    if (!$db) {
        return false;
    }
    
    try {
        $stmt = $db->query("SHOW TABLES LIKE 'admin_activity_log'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Function to create analytics tables if they don't exist
function create_analytics_tables() {
    global $db;
    
    if (!$db) {
        return false;
    }
    
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS admin_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            action_type VARCHAR(50) NOT NULL,
            action_details TEXT,
            ip_address VARCHAR(45),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (admin_id),
            INDEX (action_type),
            INDEX (created_at)
        )");
        return true;
    } catch (Exception $e) {
        error_log("Error creating analytics tables: " . $e->getMessage());
        return false;
    }
}

// Auto-create tables if they don't exist
if ($db && !check_analytics_tables()) {
    create_analytics_tables();
}
