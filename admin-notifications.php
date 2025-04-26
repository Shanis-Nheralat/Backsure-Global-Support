<?php
/**
 * Admin Panel Standardized Error Handling System
 * 
 * This file provides a unified approach to error/notification handling
 * across all admin pages.
 * 
 * File: admin-notifications.php
 */

/**
 * Message types with their associated CSS classes and icons
 */
$message_types = [
    'success' => [
        'class' => 'alert-success',
        'icon' => 'check-circle'
    ],
    'info' => [
        'class' => 'alert-info',
        'icon' => 'info'
    ],
    'warning' => [
        'class' => 'alert-warning',
        'icon' => 'alert-triangle'
    ],
    'error' => [
        'class' => 'alert-danger',
        'icon' => 'alert-octagon'
    ]
];

/**
 * Store a notification message in the session
 *
 * @param string $message The message to display
 * @param string $type Message type (success, info, warning, error)
 * @param bool $dismissible Whether the message is dismissible
 * @param int $timeout Auto-dismiss timeout in milliseconds (0 for no auto-dismiss)
 * @return void
 */
function set_admin_message($message, $type = 'info', $dismissible = true, $timeout = 0) {
    // Validate message type
    $valid_types = ['success', 'info', 'warning', 'error'];
    if (!in_array($type, $valid_types)) {
        $type = 'info';
    }
    
    // Create message data structure
    $message_data = [
        'message' => $message,
        'type' => $type,
        'dismissible' => $dismissible,
        'timeout' => $timeout
    ];
    
    // Store in session
    if (!isset($_SESSION['admin_messages'])) {
        $_SESSION['admin_messages'] = [];
    }
    
    $_SESSION['admin_messages'][] = $message_data;
}

/**
 * Get and clear all stored notification messages
 *
 * @return array Array of message data
 */
function get_admin_messages() {
    $messages = isset($_SESSION['admin_messages']) ? $_SESSION['admin_messages'] : [];
    
    // Clear messages from session
    $_SESSION['admin_messages'] = [];
    
    return $messages;
}

/**
 * Display all stored notification messages
 *
 * @return string HTML for the messages
 */
function display_admin_messages() {
    global $message_types;
    
    $messages = get_admin_messages();
    
    if (empty($messages)) {
        return '';
    }
    
    $html = '<div id="admin-messages">';
    
    foreach ($messages as $message_data) {
        $type = $message_data['type'];
        $type_data = $message_types[$type];
        
        $class = $type_data['class'];
        $icon = $type_data['icon'];
        $dismissible = $message_data['dismissible'];
        $timeout = $message_data['timeout'];
        
        $html .= '<div class="alert ' . $class . ($dismissible ? ' alert-dismissible' : '') . '" role="alert"';
        if ($timeout > 0) {
            $html .= ' data-auto-dismiss="' . $timeout . '"';
        }
        $html .= '>';
        
        // Icon
        $html .= '<i class="icon-' . $icon . ' alert-icon"></i>';
        
        // Message text
        $html .= '<div class="alert-message">' . $message_data['message'] . '</div>';
        
        // Dismiss button
        if ($dismissible) {
            $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            $html .= '<span aria-hidden="true">&times;</span>';
            $html .= '</button>';
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Shorthand function for success messages
 *
 * @param string $message The success message
 * @param bool $dismissible Whether the message is dismissible
 * @param int $timeout Auto-dismiss timeout in milliseconds
 * @return void
 */
function set_success_message($message, $dismissible = true, $timeout = 5000) {
    set_admin_message($message, 'success', $dismissible, $timeout);
}

/**
 * Shorthand function for info messages
 *
 * @param string $message The info message
 * @param bool $dismissible Whether the message is dismissible
 * @param int $timeout Auto-dismiss timeout in milliseconds
 * @return void
 */
function set_info_message($message, $dismissible = true, $timeout = 0) {
    set_admin_message($message, 'info', $dismissible, $timeout);
}

/**
 * Shorthand function for warning messages
 *
 * @param string $message The warning message
 * @param bool $dismissible Whether the message is dismissible
 * @param int $timeout Auto-dismiss timeout in milliseconds
 * @return void
 */
function set_warning_message($message, $dismissible = true, $timeout = 0) {
    set_admin_message($message, 'warning', $dismissible, $timeout);
}

/**
 * Shorthand function for error messages
 *
 * @param string $message The error message
 * @param bool $dismissible Whether the message is dismissible
 * @param int $timeout Auto-dismiss timeout in milliseconds
 * @return void
 */
function set_error_message($message, $dismissible = true, $timeout = 0) {
    set_admin_message($message, 'error', $dismissible, $timeout);
}

/**
 * Handle database errors and display appropriate messages
 *
 * @param Exception $e The caught exception
 * @param string $context Context description (e.g. "creating user")
 * @return void
 */
function handle_db_error($e, $context = 'database operation') {
    // Log the detailed error for administrators
    error_log("Database error while {$context}: " . $e->getMessage());
    
    // Set a user-friendly error message
    set_error_message("An error occurred while {$context}. Please try again or contact support if the issue persists.");
}

/**
 * Format validation errors from a form
 *
 * @param array $errors Array of error messages
 * @return string HTML for the errors
 */
function format_validation_errors($errors) {
    if (empty($errors)) {
        return '';
    }
    
    $html = '<ul class="validation-errors">';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '</ul>';
    
    return $html;
}

/**
 * JavaScript for message handling - add to admin-core.js
 * This initializes the auto-dismiss functionality for messages
 */
function get_message_js() {
    return <<<'JS'
// Message handling
document.addEventListener('DOMContentLoaded', function() {
    initMessageSystem();
});

function initMessageSystem() {
    // Process any messages with auto-dismiss
    const autoDismissMessages = document.querySelectorAll('.alert[data-auto-dismiss]');
    autoDismissMessages.forEach(function(message) {
        const timeout = parseInt(message.getAttribute('data-auto-dismiss'), 10);
        if (timeout > 0) {
            setTimeout(function() {
                fadeOutAndRemove(message);
            }, timeout);
        }
    });
    
    // Add event listeners to dismiss buttons
    const dismissButtons = document.querySelectorAll('.alert .close');
    dismissButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            fadeOutAndRemove(alert);
        });
    });
}

function fadeOutAndRemove(element) {
    // Add a CSS class for fade out animation
    element.classList.add('fade-out');
    
    // Remove after animation completes
    setTimeout(function() {
        element.parentNode.removeChild(element);
    }, 300); // Match this with CSS transition time
}

// Function to show messages dynamically via JavaScript
function showMessage(message, type = 'info', dismissible = true, timeout = 0) {
    // Get message container, create if it doesn't exist
    let container = document.getElementById('admin-messages');
    if (!container) {
        container = document.createElement('div');
        container.id = 'admin-messages';
        document.body.insertBefore(container, document.body.firstChild);
    }
    
    // Define message types with their classes and icons
    const messageTypes = {
        success: { class: 'alert-success', icon: 'check-circle' },
        info: { class: 'alert-info', icon: 'info' },
        warning: { class: 'alert-warning', icon: 'alert-triangle' },
        error: { class: 'alert-danger', icon: 'alert-octagon' }
    };
    
    // Use info type if invalid type passed
    if (!messageTypes[type]) {
        type = 'info';
    }
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert ${messageTypes[type].class}${dismissible ? ' alert-dismissible' : ''}`;
    alert.setAttribute('role', 'alert');
    
    if (timeout > 0) {
        alert.setAttribute('data-auto-dismiss', timeout);
    }
    
    // Add icon
    const icon = document.createElement('i');
    icon.className = `icon-${messageTypes[type].icon} alert-icon`;
    alert.appendChild(icon);
    
    // Add message text
    const messageDiv = document.createElement('div');
    messageDiv.className = 'alert-message';
    messageDiv.innerHTML = message;
    alert.appendChild(messageDiv);
    
    // Add dismiss button if dismissible
    if (dismissible) {
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'close';
        closeButton.setAttribute('data-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Close');
        
        const closeIcon = document.createElement('span');
        closeIcon.setAttribute('aria-hidden', 'true');
        closeIcon.innerHTML = '&times;';
        
        closeButton.appendChild(closeIcon);
        alert.appendChild(closeButton);
        
        // Add event listener
        closeButton.addEventListener('click', function() {
            fadeOutAndRemove(alert);
        });
    }
    
    // Add to container
    container.appendChild(alert);
    
    // Set auto-dismiss timeout
    if (timeout > 0) {
        setTimeout(function() {
            fadeOutAndRemove(alert);
        }, timeout);
    }
    
    return alert;
}
JS;
}

/**
 * CSS for messages - add to admin-core.css
 */
function get_message_css() {
    return <<<'CSS'
/* Message styles */
#admin-messages {
    position: relative;
    z-index: 1050;
    margin-bottom: var(--spacing-md);
}

.alert {
    display: flex;
    align-items: flex-start;
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    margin-bottom: var(--spacing-md);
    border: 1px solid transparent;
    transition: opacity 0.3s ease-out;
}

.alert-success {
    background-color: rgba(var(--success-color-rgb), 0.1);
    border-color: rgba(var(--success-color-rgb), 0.3);
    color: var(--success-color);
}

.alert-info {
    background-color: rgba(var(--info-color-rgb), 0.1);
    border-color: rgba(var(--info-color-rgb), 0.3);
    color: var(--info-color);
}

.alert-warning {
    background-color: rgba(var(--warning-color-rgb), 0.1);
    border-color: rgba(var(--warning-color-rgb), 0.3);
    color: var(--warning-color);
}

.alert-danger {
    background-color: rgba(var(--danger-color-rgb), 0.1);
    border-color: rgba(var(--danger-color-rgb), 0.3);
    color: var(--danger-color);
}

.alert-icon {
    margin-right: var(--spacing-sm);
    font-size: 1.25rem;
}

.alert-message {
    flex: 1;
}

.alert .close {
    background: transparent;
    border: none;
    font-size: 1.25rem;
    padding: 0 var(--spacing-xs);
    margin-left: var(--spacing-md);
    opacity: 0.7;
    cursor: pointer;
}

.alert .close:hover {
    opacity: 1;
}

.alert.fade-out {
    opacity: 0;
}

.validation-errors {
    padding-left: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    color: var(--danger-color);
}
CSS;
}

/**
 * Usage examples:
 * 
 * // In a form processing script
 * if (empty($_POST['username'])) {
 *     set_error_message("Username is required");
 *     header("Location: admin-users.php?action=add");
 *     exit();
 * }
 * 
 * // After successful operation
 * set_success_message("User created successfully", true, 5000);
 * 
 * // Warning about resource usage
 * set_warning_message("Database storage is at 85% capacity");
 * 
 * // In admin-header.php, right after the breadcrumbs
 * echo display_admin_messages();
 * 
 * // To handle database errors with try/catch
 * try {
 *     // Database operations
 * } catch (PDOException $e) {
 *     handle_db_error($e, "creating a new user");
 *     header("Location: admin-users.php");
 *     exit();
 * }
 * 
 * // To display validation errors in a form
 * $errors = validate_form($_POST);
 * if (!empty($errors)) {
 *     set_error_message("Please fix the following errors: " . format_validation_errors($errors));
 *     // Handle form redisplay with values
 * }
 * 
 * // Using JavaScript notification
 * <button onclick="showMessage('Settings saved', 'success', true, 5000)">Save</button>
 */
