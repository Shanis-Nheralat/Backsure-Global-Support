<?php
/**
 * Admin Notifications Component
 * Handles system notifications and alerts
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

/**
 * Set a notification message
 * @param string $type Notification type (success, error, warning, info)
 * @param string $message Notification message
 * @param string $link Optional link related to notification
 * @param int $user_id User ID (null for session-only notifications)
 * @return bool True on success, false on failure
 */
function set_admin_notification($type, $message, $link = null, $user_id = null) {
    global $db;
    
    // Always store in session for current request
    if (!isset($_SESSION['admin_notifications'])) {
        $_SESSION['admin_notifications'] = [];
    }
    
    $_SESSION['admin_notifications'][] = [
        'type' => $type,
        'message' => $message,
        'link' => $link,
        'time' => time()
    ];
    
    // If user_id is provided, store in database
    if ($user_id && $db) {
        try {
            $stmt = $db->prepare("INSERT INTO admin_notifications (user_id, type, message, link, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$user_id, $type, $message, $link]);
        } catch (PDOException $e) {
            error_log("Error storing notification: " . $e->getMessage());
            return false;
        }
    }
    
    return true;
}

/**
 * Set success notification
 * @param string $message Notification message
 * @param string $link Optional link
 * @param int $user_id User ID (optional)
 * @return bool Success status
 */
function set_success_message($message, $link = null, $user_id = null) {
    return set_admin_notification('success', $message, $link, $user_id);
}

/**
 * Set error notification
 * @param string $message Notification message
 * @param string $link Optional link
 * @param int $user_id User ID (optional)
 * @return bool Success status
 */
function set_error_message($message, $link = null, $user_id = null) {
    return set_admin_notification('error', $message, $link, $user_id);
}

/**
 * Set warning notification
 * @param string $message Notification message
 * @param string $link Optional link
 * @param int $user_id User ID (optional)
 * @return bool Success status
 */
function set_warning_message($message, $link = null, $user_id = null) {
    return set_admin_notification('warning', $message, $link, $user_id);
}

/**
 * Set info notification
 * @param string $message Notification message
 * @param string $link Optional link
 * @param int $user_id User ID (optional)
 * @return bool Success status
 */
function set_info_message($message, $link = null, $user_id = null) {
    return set_admin_notification('info', $message, $link, $user_id);
}

/**
 * Get user's notifications from database
 * @param int $user_id User ID
 * @param int $limit Maximum number of notifications to retrieve
 * @param bool $unread_only Get only unread notifications
 * @return array Notifications
 */
function get_admin_notifications($user_id, $limit = 10, $unread_only = false) {
    global $db;
    
    if (!$db) return [];
    
    try {
        $query = "SELECT * FROM admin_notifications WHERE user_id = ?";
        
        if ($unread_only) {
            $query .= " AND `read` = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Mark notification as read
 * @param int $notification_id Notification ID
 * @return bool Success status
 */
function mark_notification_read($notification_id) {
    global $db;
    
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare("UPDATE admin_notifications SET `read` = 1 WHERE id = ?");
        return $stmt->execute([$notification_id]);
    } catch (PDOException $e) {
        error_log("Error marking notification read: " . $e->getMessage());
        return false;
    }
}

/**
 * Get notification icon class based on type
 * @param string $type Notification type
 * @return string FontAwesome icon class
 */
function get_notification_icon($type) {
    switch ($type) {
        case 'success':
            return 'fa-check-circle text-success';
        case 'error':
            return 'fa-times-circle text-danger';
        case 'warning':
            return 'fa-exclamation-triangle text-warning';
        case 'info':
        default:
            return 'fa-info-circle text-info';
    }
}

/**
 * Display flash notifications
 * Shows and clears session notifications
 */
function display_notifications() {
    if (isset($_SESSION['admin_notifications']) && !empty($_SESSION['admin_notifications'])) {
        foreach ($_SESSION['admin_notifications'] as $notification) {
            // Map type to Bootstrap alert class
            $alertClass = 'info';
            switch ($notification['type']) {
                case 'success':
                    $alertClass = 'success';
                    break;
                case 'error':
                    $alertClass = 'danger';
                    break;
                case 'warning':
                    $alertClass = 'warning';
                    break;
            }
            
            $icon = get_notification_icon($notification['type']);
            
            echo '<div class="alert alert-' . $alertClass . ' alert-dismissible fade show" role="alert">';
            echo '<i class="fas ' . $icon . ' me-2"></i> ' . $notification['message'];
            
            if (!empty($notification['link'])) {
                echo ' <a href="' . $notification['link'] . '" class="alert-link">Learn more</a>';
            }
            
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        
        // Clear the session notifications
        $_SESSION['admin_notifications'] = [];
    }
}
