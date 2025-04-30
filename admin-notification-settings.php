<?php
/**
 * Admin Notification Settings
 * Configures system notifications and alerts
 */

// Include authentication and common functions
require_once 'admin-auth.php';
require_once 'admin-notifications.php';
require_once 'settings-functions.php';

// Require admin authentication
require_admin_auth();
require_admin_role(['admin']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings'])) {
    $updated = 0;
    
    foreach ($_POST['settings'] as $group => $settings) {
        foreach ($settings as $key => $value) {
            // Get the field type
            $type = get_setting_type($group, $key);
            
            // Update the setting
            if (set_setting($group, $key, $value, $type)) {
                $updated++;
            }
        }
    }
    
    if ($updated > 0) {
        set_admin_notification('success', 'Notification settings updated successfully.', '#', get_admin_user()['id']);
    } else {
        set_admin_notification('info', 'No changes were made to notification settings.', '#', get_admin_user()['id']);
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin-notification-settings.php');
    exit;
}

// Set page variables
$page_title = 'Notification Settings';
$current_page = 'notification_settings';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Notification Settings', 'url' => '#']
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
        </div>
        
        <?php display_notifications(); ?>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Notification Settings</h6>
            </div>
            <div class="card-body">
                <form action="admin-notification-settings.php" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Email Notifications</h5>
                            
                            <?php echo render_setting_field('notification_config', 'enable_email', 'Enable Email Notifications', 'boolean', [
                                'description' => 'Send notifications via email'
                            ]); ?>
                            
                            <?php echo render_setting_field('notification_config', 'default_email_subject', 'Default Email Subject', 'text', [
                                'placeholder' => 'e.g., [Site Name] - New Notification',
                                'description' => 'Default subject line for notification emails'
                            ]); ?>
                            
                            <?php echo render_setting_field('notification_config', 'sender_name', 'Sender Name', 'text', [
                                'placeholder' => 'e.g., Site Notifications',
                                'description' => 'Name that appears in the "From" field of notification emails'
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Popup Notifications</h5>
                            
                            <?php echo render_setting_field('notification_config', 'default_popup_duration', 'Default Popup Duration', 'text', [
                                'placeholder' => 'e.g., 5000',
                                'description' => 'How long popup notifications are shown (in milliseconds)'
                            ]); ?>
                            
                            <?php echo render_setting_field('notification_config', 'allow_user_opt_out', 'Allow User Opt-Out', 'boolean', [
                                'description' => 'Let users disable certain notifications'
                            ]); ?>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Notification Types</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Success Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <?php echo render_setting_field('notification_types', 'success_title', 'Success Title', 'text', [
                                        'placeholder' => 'e.g., Success!',
                                        'description' => 'Default title for success messages'
                                    ]); ?>
                                    
                                    <?php echo render_setting_field('notification_types', 'success_duration', 'Duration (ms)', 'text', [
                                        'placeholder' => 'e.g., 3000',
                                        'description' => 'How long success messages are shown'
                                    ]); ?>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Error Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <?php echo render_setting_field('notification_types', 'error_title', 'Error Title', 'text', [
                                        'placeholder' => 'e.g., Error!',
                                        'description' => 'Default title for error messages'
                                    ]); ?>
                                    
                                    <?php echo render_setting_field('notification_types', 'error_duration', 'Duration (ms)', 'text', [
                                        'placeholder' => 'e.g., 5000',
                                        'description' => 'How long error messages are shown'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">Warning Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <?php echo render_setting_field('notification_types', 'warning_title', 'Warning Title', 'text', [
                                        'placeholder' => 'e.g., Warning!',
                                        'description' => 'Default title for warning messages'
                                    ]); ?>
                                    
                                    <?php echo render_setting_field('notification_types', 'warning_duration', 'Duration (ms)', 'text', [
                                        'placeholder' => 'e.g., 4000',
                                        'description' => 'How long warning messages are shown'
                                    ]); ?>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Info Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <?php echo render_setting_field('notification_types', 'info_title', 'Info Title', 'text', [
                                        'placeholder' => 'e.g., Information',
                                        'description' => 'Default title for info messages'
                                    ]); ?>
                                    
                                    <?php echo render_setting_field('notification_types', 'info_duration', 'Duration (ms)', 'text', [
                                        'placeholder' => 'e.g., 4000',
                                        'description' => 'How long info messages are shown'
                                    ]); ?>
                                </div>
                            </div>
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
        
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Notification Preview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-3">Test Notifications</h5>
                        <p>Click the buttons below to see how each type of notification will appear:</p>
                        
                        <div class="btn-group" role="group" aria-label="Test notifications">
                            <button type="button" class="btn btn-success test-notification" data-type="success">
                                <i class="fas fa-check-circle me-1"></i> Success
                            </button>
                            <button type="button" class="btn btn-danger test-notification" data-type="error">
                                <i class="fas fa-times-circle me-1"></i> Error
                            </button>
                            <button type="button" class="btn btn-warning test-notification" data-type="warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Warning
                            </button>
                            <button type="button" class="btn btn-info test-notification" data-type="info">
                                <i class="fas fa-info-circle me-1"></i> Info
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Notifications Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="notification-tips">
                                    <li>Keep messages short and to the point</li>
                                    <li>Use appropriate notification types</li>
                                    <li>Success messages can auto-dismiss quickly</li>
                                    <li>Errors should stay visible longer</li>
                                    <li>Consider using icons to enhance visibility</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="notification-preview" class="mt-4 d-none">
                    <h5 class="mb-3">Notification Preview</h5>
                    <div id="preview-alert" class="alert" role="alert">
                        <strong id="preview-title">Notification Title</strong>
                        <span id="preview-message">This is a preview of how your notification will appear.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test notification buttons
    document.querySelectorAll('.test-notification').forEach(function(button) {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            let alertClass, icon, title, message;
            
            switch(type) {
                case 'success':
                    alertClass = 'alert-success';
                    icon = 'fa-check-circle';
                    title = document.querySelector('[name="settings[notification_types][success_title]"]').value || 'Success!';
                    message = 'Your action was completed successfully.';
                    break;
                case 'error':
                    alertClass = 'alert-danger';
                    icon = 'fa-times-circle';
                    title = document.querySelector('[name="settings[notification_types][error_title]"]').value || 'Error!';
                    message = 'There was a problem completing your request.';
                    break;
                case 'warning':
                    alertClass = 'alert-warning';
                    icon = 'fa-exclamation-triangle';
                    title = document.querySelector('[name="settings[notification_types][warning_title]"]').value || 'Warning!';
                    message = 'Please note this important information.';
                    break;
                case 'info':
                    alertClass = 'alert-info';
                    icon = 'fa-info-circle';
                    title = document.querySelector('[name="settings[notification_types][info_title]"]').value || 'Information';
                    message = 'Here is some helpful information.';
                    break;
            }
            
            // Update preview
            const previewAlert = document.getElementById('preview-alert');
            const previewTitle = document.getElementById('preview-title');
            const previewMessage = document.getElementById('preview-message');
            
            previewAlert.className = 'alert ' + alertClass;
            previewTitle.textContent = title + ' ';
            previewMessage.textContent = message;
            
            // Show preview section
            document.getElementById('notification-preview').classList.remove('d-none');
            
            // Create actual notification
            const notificationHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} me-2"></i> <strong>${title}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Insert at the top of the page
            const firstCard = document.querySelector('.card');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = notificationHtml;
            firstCard.parentNode.insertBefore(tempDiv.firstChild, firstCard);
        });
    });
});
</script>

<?php include 'admin-footer.php'; ?>
