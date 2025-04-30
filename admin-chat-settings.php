<?php
/**
 * Chatbot Settings
 * Configures website chatbot functionality
 */

// Define constants for this page
define('ADMIN_PANEL', true);
$page_title = 'Chatbot Settings';
$current_page = 'chatbot';

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
        set_success_message("Chatbot settings updated successfully.");
    } else {
        set_info_message("No changes were made to chatbot settings.");
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Page variables
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Chatbot Settings', 'url' => '#']
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Get chat stats (optional enhancement)
$chat_stats = [
    'total_sessions' => get_chat_sessions_count(),
    'unanswered' => get_unanswered_chat_count(),
    'today' => get_chat_sessions_count(true)
];

// Include templates
include 'admin-head.php';
include 'admin-sidebar.php';
include 'admin-header.php';

// Helper function to get chat sessions count
function get_chat_sessions_count($today = false) {
    global $db;
    
    $query = "SELECT COUNT(*) as count FROM chat_sessions";
    if ($today) {
        $query .= " WHERE DATE(started_at) = CURDATE()";
    }
    
    $result = $db->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        return $row['count'];
    }
    
    return 0;
}

// Helper function to get unanswered chat count
function get_unanswered_chat_count() {
    global $db;
    
    $query = "SELECT COUNT(DISTINCT c.session_id) as count 
              FROM chat_logs c
              JOIN chat_sessions s ON c.session_id = s.session_id
              WHERE c.sender = 'visitor' 
              AND s.status = 'active'
              AND c.id = (
                  SELECT MAX(id) FROM chat_logs 
                  WHERE session_id = c.session_id
              )";
    
    $result = $db->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        return $row['count'];
    }
    
    return 0;
}
?>

<main class="admin-main">
  <div class="admin-content container-fluid py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <h1><?php echo $page_title; ?></h1>
      
      <div class="page-actions">
        <a href="chatbot-logs.php" class="btn btn-outline-primary">
          <i class="fas fa-comment-dots me-2"></i> View Chat Logs
        </a>
      </div>
    </div>
    
    <?php display_notifications(); ?>
    
    <!-- Stats Cards (optional enhancement) -->
    <div class="row mb-4">
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Chat Sessions</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $chat_stats['total_sessions']; ?></div>
              </div>
              <div class="col-auto">
                <i class="fas fa-comments fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unanswered Messages</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $chat_stats['unanswered']; ?></div>
              </div>
              <div class="col-auto">
                <i class="fas fa-question-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Sessions</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $chat_stats['today']; ?></div>
              </div>
              <div class="col-auto">
                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Chatbot Settings Form -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Chatbot Configuration</h6>
      </div>
      <div class="card-body">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
          <!-- General Settings -->
          <h5 class="mb-4">General Settings</h5>
          
          <?php echo render_setting_field('chatbot', 'enabled', 'Enable Chatbot', 'boolean', [
            'description' => 'Turn the chatbot on or off across the entire website.'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'chatbot_type', 'Chatbot Type', 'select', [
            'options' => [
              'basic' => 'Basic (Simple responses)',
              'openai' => 'OpenAI (GPT/AI-powered)',
              'dialogflow' => 'Google Dialogflow',
              'tawkto' => 'Tawk.to (Third-party)'
            ],
            'description' => 'Select the type of chatbot to use.'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'default_message', 'Welcome Message', 'textarea', [
            'placeholder' => 'Enter the message shown when chatbot first loads',
            'description' => 'This message is displayed when the chat first loads.',
            'class' => 'welcome-message'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'notify_admin', 'Notify Admin', 'boolean', [
            'description' => 'Send admin notifications for new chat sessions.'
          ]); ?>
          
          <!-- Display Settings -->
          <h5 class="mt-5 mb-4">Display Settings</h5>
          
          <?php echo render_setting_field('chatbot', 'interface_position', 'Widget Position', 'select', [
            'options' => [
              'bottom-right' => 'Bottom Right',
              'bottom-left' => 'Bottom Left',
              'top-right' => 'Top Right',
              'top-left' => 'Top Left'
            ],
            'description' => 'Position of the chat widget on the page.'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'show_on_all_pages', 'Show on All Pages', 'boolean', [
            'description' => 'If disabled, you can selectively enable the chatbot on specific pages.'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'chat_icon', 'Chat Icon', 'image', [
            'description' => 'Custom icon for the chat button (recommended size: 64x64px).'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'chat_accent_color', 'Accent Color', 'text', [
            'placeholder' => 'e.g. #007bff',
            'description' => 'Primary color used in the chat interface (hex code).'
          ]); ?>
          
          <!-- AI Integration -->
          <div class="ai-settings mt-5 mb-4">
            <h5 class="mb-4">AI Integration</h5>
            <div class="settings-dependent" data-depends-on="chatbot_type" data-depends-value="openai">
              <?php echo render_setting_field('chatbot', 'gpt_api_key', 'OpenAI API Key', 'text', [
                'placeholder' => 'Enter your OpenAI API key',
                'description' => 'Required for OpenAI GPT integration.',
                'class' => 'api-key'
              ]); ?>
              
              <?php echo render_setting_field('chatbot', 'gpt_model', 'GPT Model', 'select', [
                'options' => [
                  'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                  'gpt-4' => 'GPT-4',
                  'gpt-4-turbo' => 'GPT-4 Turbo'
                ],
                'description' => 'Select which GPT model to use for responses.'
              ]); ?>
              
              <?php echo render_setting_field('chatbot', 'gpt_temperature', 'Temperature', 'number', [
                'placeholder' => '0.7',
                'min' => '0',
                'max' => '2',
                'step' => '0.1',
                'description' => 'Controls randomness of responses (0-2). Lower values are more focused and deterministic.'
              ]); ?>
              
              <?php echo render_setting_field('chatbot', 'system_prompt', 'System Prompt', 'textarea', [
                'placeholder' => 'Enter instructions for the AI assistant',
                'description' => 'Instructions that tell the AI how to behave (e.g., "You are a helpful customer service assistant for a tech company...").'
              ]); ?>
            </div>
            
            <div class="settings-dependent" data-depends-on="chatbot_type" data-depends-value="dialogflow">
              <?php echo render_setting_field('chatbot', 'dialogflow_project_id', 'Project ID', 'text', [
                'placeholder' => 'Enter your Dialogflow project ID',
                'description' => 'Required for Google Dialogflow integration.'
              ]); ?>
              
              <?php echo render_setting_field('chatbot', 'dialogflow_credentials', 'Service Account JSON', 'textarea', [
                'placeholder' => '{"type": "service_account", ...}',
                'description' => 'JSON credentials for your Dialogflow service account.',
                'class' => 'code-field'
              ]); ?>
            </div>
          </div>
          
          <!-- External Integration -->
          <div class="external-settings mt-5 mb-4">
            <h5 class="mb-4">External Chatbot Integration</h5>
            <div class="settings-dependent" data-depends-on="chatbot_type" data-depends-value="tawkto">
              <?php echo render_setting_field('chatbot', 'embed_script', 'Embed Script', 'textarea', [
                'placeholder' => 'Paste the embed script from your third-party chatbot provider',
                'description' => 'For third-party chatbots, paste the provided embed script here.',
                'class' => 'code-field'
              ]); ?>
            </div>
          </div>
          
          <!-- Advanced Settings -->
          <h5 class="mt-5 mb-4">Advanced Settings</h5>
          
          <?php echo render_setting_field('chatbot', 'session_timeout', 'Session Timeout', 'number', [
            'placeholder' => '30',
            'description' => 'Minutes of inactivity before a chat session is closed.',
            'min' => '5',
            'max' => '120'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'log_retention', 'Log Retention', 'number', [
            'placeholder' => '30',
            'description' => 'Number of days to keep chat logs before automatic deletion.',
            'min' => '1',
            'max' => '365'
          ]); ?>
          
          <?php echo render_setting_field('chatbot', 'custom_css', 'Custom CSS', 'textarea', [
            'placeholder' => '.chat-widget { /* your custom CSS */ }',
            'description' => 'Custom CSS to modify the appearance of the chat widget.',
            'class' => 'code-field'
          ]); ?>
          
          <div class="form-actions mt-5">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-2"></i> Save Chatbot Settings
            </button>
            <button type="reset" class="btn btn-secondary ms-2">
              <i class="fas fa-undo me-2"></i> Reset Changes
            </button>
            
            <?php 
            $is_enabled = get_setting('chatbot', 'enabled', false);
            if ($is_enabled):
            ?>
            <a href="chat-preview.php" target="_blank" class="btn btn-success ms-2">
              <i class="fas fa-eye me-2"></i> Preview Chatbot
            </a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <?php include 'admin-footer.php'; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle dependent settings visibility
    const chatbotType = document.querySelector('select[name="settings[chatbot][chatbot_type]"]');
    const dependentSettings = document.querySelectorAll('.settings-dependent');
    
    function updateDependentSettings() {
        const selectedValue = chatbotType.value;
        
        dependentSettings.forEach(section => {
            const requiredValue = section.dataset.dependsValue;
            if (requiredValue === selectedValue) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
    
    // Initial update
    updateDependentSettings();
    
    // Update on change
    chatbotType.addEventListener('change', updateDependentSettings);
    
    // Format code fields
    document.querySelectorAll('.code-field').forEach(field => {
        field.style.fontFamily = 'monospace';
    });
});
</script>
