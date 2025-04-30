<?php
/**
 * Notification Settings
 * Configures system notification behavior
 */

// Define constants for this page
define('ADMIN_PANEL', true);
$page_title = 'Notification Settings';
$current_page = 'notification_settings';

// Authentication and permissions
require_once 'admin-auth.php';
require_admin_auth();
require_admin_role(['admin']);

// Include notifications system
require_once 'admin-notifications.php';

// Include settings functions
require_once 'settings-functions.php';

// Track page view for analytics
require_once 'admin-analytics.php';
log_page_view(basename($_SERVER['PHP_SELF']));

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings'])) {
    $updated = process_settings_form($_POST, $_FILES);
    
    if ($updated > 0) {
        set_success_message("Notification settings updated successfully.");
    } else {
        set_info_message("No changes were made to notification settings.");
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Page variables
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Notification Settings', 'url' => '#']
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Define notification types
$notification_types = [
    'success' => [
        'name' => 'Success',
        'description' => 'Used for successful operations and positive confirmations.',
        'default_icon' => 'fas fa-check-circle',
        'default_color' => '#28a745'
    ],
    'error' => [
        'name' => 'Error',
        'description' => 'Used for failed operations and critical errors.',
        'default_icon' => 'fas fa-times-circle',
        'default_color' => '#dc3545'
    ],
    'warning' => [
        'name' => 'Warning',
        'description' => 'Used for important notices that require attention.',
        'default_icon' => 'fas fa-exclamation-triangle',
        'default_color' => '#ffc107'
    ],
    'info' => [
        'name' => 'Information',
        'description' => 'Used for general informational messages.',
        'default_icon' => 'fas fa-info-circle',
        'default_color' => '#17a2b8'
    ]
];

// Include templates
include 'admin-head.php';
include 'admin-sidebar.php';
include 'admin-header.php';
?>

<main class="admin-main">
  <div class="admin-content container-fluid py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <h1><?php echo $page_title; ?></h1>
      
      <div class="page-actions">
        <button type="button" id="test-notifications" class="btn btn-outline-primary">
          <i class="fas fa-bell me-2"></i> Test Notifications
        </button>
      </div>
    </div>
    
    <?php display_notifications(); ?>
    
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs" id="notificationTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">
              <i class="fas fa-cog me-1"></i> General
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab">
              <i class="fas fa-envelope me-1"></i> Email
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="types-tab" data-bs-toggle="tab" href="#types" role="tab">
              <i class="fas fa-palette me-1"></i> Style & Types
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="templates-tab" data-bs-toggle="tab" href="#templates" role="tab">
              <i class="fas fa-file-alt me-1"></i> Templates
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
          <div class="tab-content" id="notificationTabsContent">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
              <h4 class="mb-4">General Notification Settings</h4>
              
              <?php echo render_setting_field('notification_config', 'desktop_notifications', 'Desktop Notifications', 'boolean', [
                'description' => 'Enable browser desktop notifications for important alerts (requires user permission).'
              ]); ?>
              
              <?php echo render_setting_field('notification_config', 'default_popup_duration', 'Default Duration', 'number', [
                'placeholder' => '5000',
                'description' => 'How long notifications display (in milliseconds).',
                'min' => '1000',
                'max' => '20000',
                'step' => '1000'
              ]); ?>
              
              <?php echo render_setting_field('notification_config', 'allow_user_opt_out', 'Allow User Opt-Out', 'boolean', [
                'description' => 'Allow users to disable certain notification types.'
              ]); ?>
              
              <?php echo render_setting_field('notification_config', 'sound_enabled', 'Sound Notifications', 'boolean', [
                'description' => 'Play sound when notifications appear.'
              ]); ?>
              
              <div class="settings-dependent" data-depends-on="sound_enabled" data-depends-value="1">
                <?php echo render_setting_field('notification_config', 'notification_sound', 'Notification Sound', 'file', [
                  'description' => 'Upload a custom notification sound (MP3, WAV, OGG).'
                ]); ?>
              </div>
              
              <?php echo render_setting_field('notification_config', 'stack_behavior', 'Stack Behavior', 'select', [
                'options' => [
                  'stack' => 'Stack (Show all)',
                  'replace' => 'Replace (Show only latest)',
                  'limit' => 'Limit (Show max 3)'
                ],
                'description' => 'How to handle multiple notifications.'
              ]); ?>
              
              <?php echo render_setting_field('notification_config', 'position', 'Position', 'select', [
                'options' => [
                  'top-right' => 'Top Right',
                  'top-left' => 'Top Left',
                  'bottom-right' => 'Bottom Right',
                  'bottom-left' => 'Bottom Left',
                  'top-center' => 'Top Center',
                  'bottom-center' => 'Bottom Center'
                ],
                'description' => 'Where notifications appear on the screen.'
              ]); ?>
            </div>
            
            <!-- Email Tab -->
            <div class="tab-pane fade" id="email" role="tabpanel">
              <h4 class="mb-4">Email Notification Settings</h4>
              
              <?php echo render_setting_field('notification_config', 'enable_email', 'Email Notifications', 'boolean', [
                'description' => 'Send important notifications via email.'
              ]); ?>
              
              <div class="settings-dependent" data-depends-on="enable_email" data-depends-value="1">
                <?php echo render_setting_field('notification_config', 'sender_name', 'Sender Name', 'text', [
                  'placeholder' => 'Website Notifications',
                  'description' => 'Name to use in the "From" field.'
                ]); ?>
                
                <?php echo render_setting_field('notification_config', 'sender_email', 'Sender Email', 'text', [
                  'placeholder' => 'notifications@example.com',
                  'description' => 'Email address to use in the "From" field.'
                ]); ?>
                
                <?php echo render_setting_field('notification_config', 'default_email_subject', 'Default Subject', 'text', [
                  'placeholder' => '[Site Name] Notification',
                  'description' => 'Default subject line for notification emails.'
                ]); ?>
                
                <?php echo render_setting_field('notification_config', 'email_logo', 'Email Logo', 'image', [
                  'description' => 'Logo to display in notification emails.'
                ]); ?>
                
                <?php echo render_setting_field('notification_config', 'email_footer_text', 'Email Footer Text', 'textarea', [
                  'placeholder' => 'You received this notification because you are a user of [Site Name].',
                  'description' => 'Text to display at the bottom of notification emails.'
                ]); ?>
              </div>
            </div>
            
            <!-- Style & Types Tab -->
            <div class="tab-pane fade" id="types" role="tabpanel">
              <h4 class="mb-4">Notification Types & Styling</h4>
              
              <div class="notification-types-container">
                <?php foreach ($notification_types as $type => $type_info): ?>
                  <div class="notification-type-card mb-4 p-3 border rounded">
                    <h5 class="mb-3"><?php echo $type_info['name']; ?> Notifications</h5>
                    <p class="text-muted mb-3"><?php echo $type_info['description']; ?></p>
                    
                    <div class="row">
                      <div class="col-md-6">
                        <?php echo render_setting_field('notification_types', "{$type}_icon", 'Icon Class', 'text', [
                          'placeholder' => $type_info['default_icon'],
                          'description' => 'Font Awesome icon class (e.g., fas fa-check-circle).'
                        ]); ?>
                      </div>
                      
                      <div class="col-md-6">
                        <?php echo render_setting_field('notification_types', "{$type}_color", 'Color', 'text', [
                          'placeholder' => $type_info['default_color'],
                          'description' => 'Color for this notification type (hex code).'
                        ]); ?>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6">
                        <?php echo render_setting_field('notification_types', "{$type}_duration", 'Duration (ms)', 'number', [
                          'placeholder' => '5000',
                          'description' => 'How long this notification type displays (in milliseconds).',
                          'min' => '1000',
                          'max' => '20000',
                          'step' => '1000'
                        ]); ?>
                      </div>
                      
                      <div class="col-md-6">
                        <?php echo render_setting_field('notification_types', "{$type}_auto_close", 'Auto Close', 'boolean', [
                          'description' => 'Automatically close this notification type.'
                        ]); ?>
                      </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="notification-preview mt-3 p-3 rounded" 
                         style="background-color: <?php echo get_setting('notification_types', "{$type}_color", $type_info['default_color']); ?>20; 
                                border-left: 4px solid <?php echo get_setting('notification_types', "{$type}_color", $type_info['default_color']); ?>">
                      <div class="d-flex">
                        <div class="notification-icon me-3">
                          <i class="<?php echo get_setting('notification_types', "{$type}_icon", $type_info['default_icon']); ?> fa-lg" 
                             style="color: <?php echo get_setting('notification_types', "{$type}_color", $type_info['default_color']); ?>"></i>
                        </div>
                        <div class="notification-content">
                          <div class="notification-title fw-bold">Sample <?php echo $type_info['name']; ?> Notification</div>
                          <div class="notification-message">This is how your <?php echo strtolower($type_info['name']); ?> notifications will appear.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Templates Tab -->
            <div class="tab-pane fade" id="templates" role="tabpanel">
              <h4 class="mb-4">Notification Templates</h4>
              
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Use these variables in your templates: <code>{site_name}</code>, <code>{message}</code>, <code>{user_name}</code>, <code>{date}</code>
              </div>
              
              <?php echo render_setting_field('notification_templates', 'user_welcome', 'User Welcome', 'textarea', [
                'placeholder' => 'Welcome to {site_name}, {user_name}!',
                'description' => 'Sent when a new user registers.'
              ]); ?>
              
              <?php echo render_setting_field('notification_templates', 'password_reset', 'Password Reset', 'textarea', [
                'placeholder' => 'Your password reset link is ready.',
                'description' => 'Sent for password reset requests.'
              ]); ?>
              
              <?php echo render_setting_field('notification_templates', 'new_comment', 'New Comment', 'textarea', [
                'placeholder' => 'New comment on your post.',
                'description' => 'Sent when a new comment is added to user\'s content.'
              ]); ?>
              
              <?php echo render_setting_field('notification_templates', 'admin_login', 'Admin Login', 'textarea', [
                'placeholder' => 'New admin login detected from {ip_address}',
                'description' => 'Sent on new admin login for security.'
              ]); ?>
            </div>
          </div>
          
          <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-2"></i> Save Notification Settings
            </button>
            <button type="reset" class="btn btn-secondary ms-2">
              <i class="fas fa-undo me-2"></i> Reset Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <?php include 'admin-footer.php'; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test notifications button
    document.getElementById('test-notifications').addEventListener('click', function() {
        // Success notification
        setTimeout(function() {
            showNotification('success', 'Success Notification', 'This is a sample success notification.');
        }, 0);
        
        // Info notification
        setTimeout(function() {
            showNotification('info', 'Information Notification', 'This is a sample information notification.');
        }, 1500);
        
        // Warning notification
        setTimeout(function() {
            showNotification('warning', 'Warning Notification', 'This is a sample warning notification.');
        }, 3000);
        
        // Error notification
        setTimeout(function() {
            showNotification('error', 'Error Notification', 'This is a sample error notification.');
        }, 4500);
    });
    
    // Function to show a notification
    function showNotification(type, title, message) {
        // Use the admin panel's notification system if available
        if (typeof window.adminShowNotification === 'function') {
            window.adminShowNotification(type, title, message);
            return;
        }
        
        // Fallback to creating notifications directly
        const container = document.querySelector('.notifications-container') || createNotificationsContainer();
        const position = document.querySelector('select[name="settings[notification_config][position]"]').value || 'top-right';
        container.className = 'notifications-container position-fixed ' + position;
        
        // Get notification settings
        const color = document.querySelector('input[name="settings[notification_types][' + type + '_color]"]').value || 
                     getDefaultColor(type);
        const icon = document.querySelector('input[name="settings[notification_types][' + type + '_icon]"]').value || 
                    getDefaultIcon(type);
        const duration = parseInt(document.querySelector('input[name="settings[notification_types][' + type + '_duration]"]').value) || 5000;
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'notification-item alert d-flex align-items-center mb-2';
        notification.style.backgroundColor = color + '20';
        notification.style.borderLeft = '4px solid ' + color;
        notification.style.maxWidth = '350px';
        notification.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        
        notification.innerHTML = `
            <div class="notification-icon me-3">
                <i class="${icon} fa-lg" style="color: ${color}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title fw-bold">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button type="button" class="btn-close ms-auto" aria-label="Close"></button>
        `;
        
        // Add to container
        container.appendChild(notification);
        
        // Add close button functionality
        notification.querySelector('.btn-close').addEventListener('click', function() {
            container.removeChild(notification);
        });
        
        // Auto close after duration
        setTimeout(function() {
            if (notification.parentNode === container) {
                container.removeChild(notification);
            }
        }, duration);
    }
    
    // Helper function to create notifications container
    function createNotificationsContainer() {
        const container = document.createElement('div');
        container.className = 'notifications-container position-fixed top-right';
        document.body.appendChild(container);
        return container;
    }
    
    // Helper function to get default notification color
    function getDefaultColor(type) {
        switch (type) {
            case 'success': return '#28a745';
            case 'info': return '#17a2b8';
            case 'warning': return '#ffc107';
            case 'error': return '#dc3545';
            default: return '#17a2b8';
        }
    }
    
    // Helper function to get default notification icon
    function getDefaultIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle';
            case 'info': return 'fas fa-info-circle';
            case 'warning': return 'fas fa-exclamation-triangle';
            case 'error': return 'fas fa-times-circle';
            default: return 'fas fa-bell';
        }
    }
    
    // Handle dependent settings visibility
    const toggleFields = document.querySelectorAll('input[type="checkbox"]');
    toggleFields.forEach(field => {
        field.addEventListener('change', updateDependentSettings);
    });
    
    function updateDependentSettings() {
        document.querySelectorAll('.settings-dependent').forEach(section => {
            const dependsOn = section.dataset.dependsOn;
            const dependsValue = section.dataset.dependsValue;
            const toggleField = document.querySelector(`input[name="settings[notification_config][${dependsOn}]"]`) || 
                               document.querySelector(`input[name="settings[notification_types][${dependsOn}]"]`);
            
            if (toggleField) {
                if ((toggleField.checked && dependsValue === '1') || 
                    (!toggleField.checked && dependsValue === '0')) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            }
        });
    }
    
    // Initial update
    updateDependentSettings();
    
    // Preserve active tab after form submission
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`a[href="${hash}"]`);
        if (tab) {
            tab.click();
        }
    }
});
</script>
