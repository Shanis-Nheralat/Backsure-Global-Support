<?php
/**
 * Admin Chatbot Settings
 * Configures the website chatbot functionality
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
            
            // Sanitize embed script
            if ($key === 'embed_script') {
                // Allow specific HTML tags but sanitize
                $value = strip_tags($value, '<script><div><span><a><iframe>');
            }
            
            // Update the setting
            if (set_setting($group, $key, $value, $type)) {
                $updated++;
            }
        }
    }
    
    if ($updated > 0) {
        set_admin_notification('success', 'Chatbot settings updated successfully.', '#', get_admin_user()['id']);
    } else {
        set_admin_notification('info', 'No changes were made to chatbot settings.', '#', get_admin_user()['id']);
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin-chat-settings.php');
    exit;
}

// Set page variables
$page_title = 'Chatbot Settings';
$current_page = 'chatbot';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Chatbot Settings', 'url' => '#']
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
                <h6 class="m-0 font-weight-bold text-primary">Chatbot Configuration</h6>
            </div>
            <div class="card-body">
                <form action="admin-chat-settings.php" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Settings</h5>
                            
                            <?php echo render_setting_field('chatbot', 'enabled', 'Enable Chatbot', 'boolean', [
                                'description' => 'Display the chatbot widget on your website'
                            ]); ?>
                            
                            <?php echo render_setting_field('chatbot', 'chatbot_type', 'Chatbot Type', 'text', [
                                'placeholder' => 'e.g., basic, openai, dialogflow, tawkto',
                                'description' => 'Select the type of chatbot to use'
                            ]); ?>
                            
                            <?php echo render_setting_field('chatbot', 'default_message', 'Default Welcome Message', 'textarea', [
                                'placeholder' => 'Enter a welcome message for visitors',
                                'rows' => 3,
                                'description' => 'First message shown when a user opens the chat'
                            ]); ?>
                            
                            <?php echo render_setting_field('chatbot', 'notify_admin', 'Notify Admin', 'boolean', [
                                'description' => 'Send notifications when new chat messages are received'
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Display Settings</h5>
                            
                            <?php echo render_setting_field('chatbot', 'interface_position', 'Widget Position', 'text', [
                                'placeholder' => 'e.g., bottom-right, bottom-left',
                                'description' => 'Position of the chat widget on the screen'
                            ]); ?>
                            
                            <?php echo render_setting_field('chatbot', 'show_on_all_pages', 'Show on All Pages', 'boolean', [
                                'description' => 'Display the chatbot on all website pages'
                            ]); ?>
                            
                            <h5 class="mt-4 mb-3">Integration</h5>
                            
                            <?php echo render_setting_field('chatbot', 'gpt_api_key', 'OpenAI API Key', 'text', [
                                'placeholder' => 'Enter your API key',
                                'description' => 'Required for OpenAI GPT integration'
                            ]); ?>
                            
                            <?php echo render_setting_field('chatbot', 'embed_script', 'Embed Script', 'textarea', [
                                'placeholder' => 'Enter third-party chat widget script',
                                'rows' => 6,
                                'description' => 'For third-party providers like Tawk.to or Crisp'
                            ]); ?>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Chatbot Settings
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
                <h6 class="m-0 font-weight-bold text-primary">Chatbot Preview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chat-preview p-3 border rounded">
                            <div class="chat-header bg-primary text-white p-2 rounded-top">
                                <h6 class="m-0">Live Chat</h6>
                            </div>
                            <div class="chat-messages bg-light p-3" style="height: 250px; overflow-y: auto;">
                                <div class="message bot mb-3">
                                    <div class="message-content bg-white p-2 rounded shadow-sm">
                                        <strong>Chatbot:</strong> 
                                        <?php echo get_setting('chatbot', 'default_message', 'Hi there! How can I help you today?'); ?>
                                    </div>
                                </div>
                                <div class="message user mb-3 text-end">
                                    <div class="message-content bg-primary text-white p-2 rounded shadow-sm d-inline-block">
                                        <strong>User:</strong> I have a question about your services.
                                    </div>
                                </div>
                                <div class="message bot mb-3">
                                    <div class="message-content bg-white p-2 rounded shadow-sm">
                                        <strong>Chatbot:</strong> I'd be happy to help! What would you like to know about our services?
                                    </div>
                                </div>
                            </div>
                            <div class="chat-input mt-2 d-flex">
                                <input type="text" class="form-control" placeholder="Type your message...">
                                <button class="btn btn-primary ms-2">Send</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Chatbot Types</h5>
                                <p class="card-text">Choose the right chatbot type for your needs:</p>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>basic</strong> - Simple rule-based chatbot
                                    </li>
                                    <li class="list-group-item">
                                        <strong>openai</strong> - AI-powered chatbot using OpenAI's GPT
                                    </li>
                                    <li class="list-group-item">
                                        <strong>dialogflow</strong> - Google's Dialogflow conversational AI
                                    </li>
                                    <li class="list-group-item">
                                        <strong>tawkto</strong> - Tawk.to live chat integration
                                    </li>
                                </ul>
                                <p class="mt-3">For third-party integrations, paste the provider's embed code in the "Embed Script" field.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dependent fields based on chatbot type
    const typeInput = document.querySelector('[name="settings[chatbot][chatbot_type]"]');
    const apiKeyGroup = document.querySelector('[id*="setting_chatbot_gpt_api_key"]').closest('.mb-3');
    const embedScriptGroup = document.querySelector('[id*="setting_chatbot_embed_script"]').closest('.mb-3');
    
    function toggleFields() {
        const type = typeInput.value.toLowerCase();
        
        if (type === 'openai') {
            apiKeyGroup.style.display = 'block';
            embedScriptGroup.style.display = 'none';
        } else if (type === 'tawkto' || type === 'dialogflow') {
            apiKeyGroup.style.display = 'none';
            embedScriptGroup.style.display = 'block';
        } else {
            apiKeyGroup.style.display = 'none';
            embedScriptGroup.style.display = 'none';
        }
    }
    
    // Initial toggle
    toggleFields();
    
    // Toggle on change
    typeInput.addEventListener('change', toggleFields);
    
    // Toggle main enable/disable
    const enabledInput = document.querySelector('[name="settings[chatbot][enabled]"]');
    const allSettings = document.querySelectorAll('.card-body .mb-3:not(:first-child)');
    
    function toggleAllSettings() {
        const enabled = enabledInput.checked;
        
        allSettings.forEach(setting => {
            setting.style.opacity = enabled ? '1' : '0.5';
            setting.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = !enabled;
            });
        });
    }
    
    // Initial toggle
    toggleAllSettings();
    
    // Toggle on change
    enabledInput.addEventListener('change', toggleAllSettings);
});
</script>

<?php include 'admin-footer.php'; ?>
