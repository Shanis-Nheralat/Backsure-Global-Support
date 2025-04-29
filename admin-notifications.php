<?php
/**
 * Standardized message/alert system
 * Handles session-based notifications
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize notifications array in session if not exists
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

/**
 * Add a success message to be displayed
 * 
 * @param string $message The message to display
 * @param bool $dismissible Whether the message can be dismissed (default: true)
 * @param int $timeout Automatic dismissal timeout in milliseconds, 0 for no timeout (default: 0)
 * @return void
 */
function set_success_message($message, $dismissible = true, $timeout = 0) {
    $_SESSION['notifications'][] = [
        'type' => 'success',
        'message' => $message,
        'dismissible' => $dismissible,
        'timeout' => $timeout
    ];
}

/**
 * Add an error message to be displayed
 * 
 * @param string $message The message to display
 * @param bool $dismissible Whether the message can be dismissed (default: true)
 * @param int $timeout Automatic dismissal timeout in milliseconds, 0 for no timeout (default: 0)
 * @return void
 */
function set_error_message($message, $dismissible = true, $timeout = 0) {
    $_SESSION['notifications'][] = [
        'type' => 'error',
        'message' => $message,
        'dismissible' => $dismissible,
        'timeout' => $timeout
    ];
}

/**
 * Add a warning message to be displayed
 * 
 * @param string $message The message to display
 * @param bool $dismissible Whether the message can be dismissed (default: true)
 * @param int $timeout Automatic dismissal timeout in milliseconds, 0 for no timeout (default: 0)
 * @return void
 */
function set_warning_message($message, $dismissible = true, $timeout = 0) {
    $_SESSION['notifications'][] = [
        'type' => 'warning',
        'message' => $message,
        'dismissible' => $dismissible,
        'timeout' => $timeout
    ];
}

/**
 * Add an info message to be displayed
 * 
 * @param string $message The message to display
 * @param bool $dismissible Whether the message can be dismissed (default: true)
 * @param int $timeout Automatic dismissal timeout in milliseconds, 0 for no timeout (default: 0)
 * @return void
 */
function set_info_message($message, $dismissible = true, $timeout = 0) {
    $_SESSION['notifications'][] = [
        'type' => 'info',
        'message' => $message,
        'dismissible' => $dismissible,
        'timeout' => $timeout
    ];
}

/**
 * Display all notifications and clear them from session
 * Compatible with Bootstrap 5 alerts
 * 
 * @return void
 */
function display_notifications() {
    if (empty($_SESSION['notifications'])) {
        return;
    }
    
    echo '<div class="admin-notifications container-fluid py-2">';
    
    foreach ($_SESSION['notifications'] as $notification) {
        // Convert notification type to Bootstrap alert type
        $type = $notification['type'];
        if ($type === 'error') {
            $type = 'danger';
        }
        
        $dismissible_class = $notification['dismissible'] ? ' alert-dismissible fade show' : '';
        $data_timeout = $notification['timeout'] > 0 ? ' data-timeout="' . $notification['timeout'] . '"' : '';
        
        echo '<div class="alert alert-' . $type . $dismissible_class . '"' . $data_timeout . ' role="alert">';
        
        // Add icon based on notification type
        switch ($notification['type']) {
            case 'success':
                echo '<i class="fas fa-check-circle me-2"></i>';
                break;
            case 'error':
                echo '<i class="fas fa-times-circle me-2"></i>';
                break;
            case 'warning':
                echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                break;
            case 'info':
                echo '<i class="fas fa-info-circle me-2"></i>';
                break;
        }
        
        echo $notification['message'];
        
        if ($notification['dismissible']) {
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }
        
        echo '</div>';
    }
    
    echo '</div>';
    
    // Add script for auto-dismissing notifications
    if (hasTimeoutNotifications($_SESSION['notifications'])) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('[data-timeout]');
            notifications.forEach(notification => {
                const timeout = notification.getAttribute('data-timeout');
                if (timeout && !isNaN(parseInt(timeout))) {
                    setTimeout(() => {
                        const closeBtn = notification.querySelector('.btn-close');
                        if (closeBtn) {
                            closeBtn.click();
                        } else {
                            notification.remove();
                        }
                    }, parseInt(timeout));
                }
            });
        });
        </script>
        <?php
    }
    
    // Clear notifications
    $_SESSION['notifications'] = [];
}

/**
 * Check if any notifications have a timeout
 * 
 * @param array $notifications Notifications array
 * @return bool True if any notification has a timeout
 */
function hasTimeoutNotifications($notifications) {
    foreach ($notifications as $notification) {
        if ($notification['timeout'] > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Get notification icon class based on type
 * 
 * @param string $type Notification type
 * @return string Font Awesome icon name
 */
function get_notification_icon($type) {
    switch ($type) {
        case 'inquiry':
            return 'envelope';
        case 'user':
            return 'user-plus';
        case 'system':
            return 'server';
        case 'content':
            return 'edit';
        case 'testimonial':
            return 'star';
        case 'lead':
            return 'funnel-dollar';
        default:
            return 'info-circle';
    }
}

/**
 * Get unread notifications count (placeholder function)
 * 
 * @return int Count of unread notifications
 */
function get_unread_notifications_count() {
    // TODO: Implement actual database query
    return 5; // Placeholder
}

/**
 * Get recent notifications (placeholder function)
 * 
 * @param int $limit Max number of notifications to return
 * @return array Recent notifications
 */
function get_recent_notifications($limit = 5) {
    // TODO: Implement actual database query
    // For now, return sample notifications for testing
    $notifications = [
        [
            'read' => false,
            'link' => 'admin-inquiries.php',
            'type' => 'inquiry',
            'message' => 'New inquiry received from John Smith',
            'time' => '2 hours ago'
        ],
        [
            'read' => false,
            'link' => 'admin-users.php',
            'type' => 'user',
            'message' => 'New user registered: Sarah Johnson',
            'time' => 'Yesterday'
        ],
        [
            'read' => true,
            'link' => 'admin-backup.php',
            'type' => 'system',
            'message' => 'System backup completed successfully',
            'time' => '2 days ago'
        ],
        [
            'read' => true,
            'link' => 'admin-blog.php',
            'type' => 'content',
            'message' => 'New blog post published',
            'time' => '3 days ago'
        ],
        [
            'read' => true,
            'link' => 'admin-testimonials.php',
            'type' => 'testimonial',
            'message' => 'New testimonial added',
            'time' => '4 days ago'
        ],
        [
            'read' => true,
            'link' => 'admin-leads.php',
            'type' => 'lead',
            'message' => 'New lead assigned to you',
            'time' => '5 days ago'
        ]
    ];
    
    return array_slice($notifications, 0, $limit);
}

/**
 * Get pending tasks (placeholder function)
 * 
 * @param int $limit Max number of tasks to return
 * @return array Pending tasks
 */
function get_pending_tasks($limit = 3) {
    // TODO: Implement actual database query
    // For now, return sample tasks for testing
    $tasks = [
        [
            'id' => 1,
            'link' => 'admin-tasks.php?task=1',
            'title' => 'Update homepage banner',
            'priority' => 'high',
            'due' => 'Today'
        ],
        [
            'id' => 2,
            'link' => 'admin-tasks.php?task=2',
            'title' => 'Review new testimonials',
            'priority' => 'medium',
            'due' => 'Tomorrow'
        ],
        [
            'id' => 3,
            'link' => 'admin-tasks.php?task=3',
            'title' => 'Prepare monthly report',
            'priority' => 'medium',
            'due' => 'In 2 days'
        ],
        [
            'id' => 4,
            'link' => 'admin-tasks.php?task=4',
            'title' => 'Update team page content',
            'priority' => 'low',
            'due' => 'Next week'
        ]
    ];
    
    return array_slice($tasks, 0, $limit);
}

/**
 * Get pending tasks count (placeholder function)
 * 
 * @return int Count of pending tasks
 */
function get_pending_tasks_count() {
    // TODO: Implement actual database query
    return 4; // Placeholder
}
