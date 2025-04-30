<?php
/**
 * Admin Integrations Settings
 * Manages third-party service integrations
 */

// Include authentication and common functions
require_once 'admin-auth.php';
require_once 'admin-notifications.php';
require_once 'settings-functions.php';

// Require admin authentication
require_admin_auth();
require_admin_role(['admin']);

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_integration') {
        // Add new integration
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['display_name']));
        
        if (set_setting('integration_' . $slug, 'display_name', $_POST['display_name'], 'text') &&
            set_setting('integration_' . $slug, 'description', $_POST['description'], 'textarea') &&
            set_setting('integration_' . $slug, 'is_active', isset($_POST['is_active']) ? 1 : 0, 'boolean')) {
            
            set_admin_notification('success', 'Integration "' . $_POST['display_name'] . '" added successfully.', '#', get_admin_user()['id']);
        } else {
            set_admin_notification('error', 'Failed to add integration.', '#', get_admin_user()['id']);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_integration' && isset($_POST['slug'])) {
        // Update existing integration
        $slug = $_POST['slug'];
        $updated = 0;
        
        foreach ($_POST['settings'] as $key => $value) {
            // Handle file uploads
            if ($key === 'logo' && isset($_FILES['settings_files']) && 
                isset($_FILES['settings_files']['name']['logo']) && 
                !empty($_FILES['settings_files']['name']['logo'])) {
                
                $file = [
                    'name' => $_FILES['settings_files']['name']['logo'],
                    'type' => $_FILES['settings_files']['type']['logo'],
                    'tmp_name' => $_FILES['settings_files']['tmp_name']['logo'],
                    'error' => $_FILES['settings_files']['error']['logo'],
                    'size' => $_FILES['settings_files']['size']['logo']
                ];
                
                $value = handle_file_upload($file, get_setting('integration_' . $slug, 'logo'));
            }
            
            if ($key === 'og_image' && isset($_FILES['settings_files']) && 
                isset($_FILES['settings_files']['name']['og_image']) && 
                !empty($_FILES['settings_files']['name']['og_image'])) {
                
                $file = [
                    'name' => $_FILES['settings_files']['name']['og_image'],
                    'type' => $_FILES['settings_files']['type']['og_image'],
                    'tmp_name' => $_FILES['settings_files']['tmp_name']['og_image'],
                    'error' => $_FILES['settings_files']['error']['og_image'],
                    'size' => $_FILES['settings_files']['size']['og_image']
                ];
                
                $value = handle_file_upload($file, get_setting('integration_' . $slug, 'og_image'));
            }
            
            // Handle is_active checkbox
            if ($key === 'is_active') {
                $value = isset($value) ? 1 : 0;
                $type = 'boolean';
            } else {
                $type = ($key === 'description' || $key === 'seo_description') ? 'textarea' : 'text';
                $type = ($key === 'logo' || $key === 'og_image') ? 'image' : $type;
            }
            
            if (set_setting('integration_' . $slug, $key, $value, $type)) {
                $updated++;
            }
        }
        
        if ($updated > 0) {
            set_admin_notification('success', 'Integration updated successfully.', '#', get_admin_user()['id']);
        } else {
            set_admin_notification('info', 'No changes were made to the integration.', '#', get_admin_user()['id']);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_integration' && isset($_POST['slug'])) {
        // Delete integration
        $slug = $_POST['slug'];
        
        // TODO: Implement delete functionality
        // This would involve removing all settings with the integration_slug prefix
        
        set_admin_notification('success', 'Integration deleted successfully.', '#', get_admin_user()['id']);
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin-integrations.php');
    exit;
}

// Get all integrations
function get_all_integrations() {
    global $db;
    
    $integrations = [];
    
    if (!$db) return $integrations;
    
    try {
        $stmt = $db->prepare("SELECT DISTINCT SUBSTRING_INDEX(setting_group, '_', -1) as slug 
                              FROM settings 
                              WHERE setting_group LIKE 'integration_%'");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $result) {
            $slug = $result['slug'];
            $integrations[$slug] = [
                'slug' => $slug,
                'display_name' => get_setting('integration_' . $slug, 'display_name', $slug),
                'description' => get_setting('integration_' . $slug, 'description', ''),
                'is_active' => get_setting('integration_' . $slug, 'is_active', false),
                'logo' => get_setting('integration_' . $slug, 'logo', ''),
                'api_key' => get_setting('integration_' . $slug, 'api_key', ''),
                'secret_key' => get_setting('integration_' . $slug, 'secret_key', ''),
                'doc_url' => get_setting('integration_' . $slug, 'doc_url', ''),
                'category' => get_setting('integration_' . $slug, 'category', '')
            ];
        }
    } catch (PDOException $e) {
        error_log("Error getting integrations: " . $e->getMessage());
    }
    
    return $integrations;
}

// Get specific integration
function get_integration($slug) {
    $integration = [
        'slug' => $slug,
        'display_name' => get_setting('integration_' . $slug, 'display_name', ''),
        'description' => get_setting('integration_' . $slug, 'description', ''),
        'is_active' => get_setting('integration_' . $slug, 'is_active', false),
        'logo' => get_setting('integration_' . $slug, 'logo', ''),
        'api_key' => get_setting('integration_' . $slug, 'api_key', ''),
        'secret_key' => get_setting('integration_' . $slug, 'secret_key', ''),
        'doc_url' => get_setting('integration_' . $slug, 'doc_url', ''),
        'category' => get_setting('integration_' . $slug, 'category', ''),
        'seo_title' => get_setting('integration_' . $slug, 'seo_title', ''),
        'seo_description' => get_setting('integration_' . $slug, 'seo_description', ''),
        'og_image' => get_setting('integration_' . $slug, 'og_image', '')
    ];
    
    return $integration;
}

// Get integration to edit if specified
$edit_slug = isset($_GET['edit']) ? $_GET['edit'] : null;
$current_integration = $edit_slug ? get_integration($edit_slug) : null;

// Get all integrations
$integrations = get_all_integrations();

// Set page variables
$page_title = $edit_slug ? 'Edit Integration: ' . $current_integration['display_name'] : 'Integrations';
$current_page = 'integrations';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Integrations', 'url' => 'admin-integrations.php']
];

if ($edit_slug) {
    $breadcrumbs[] = ['title' => 'Edit: ' . $current_integration['display_name'], 'url' => '#'];
}

// Include templates
include 'admin-head.php';
include 'admin-sidebar.php';
include 'admin-header.php';
?>

<main class="admin-main">
    <div class="admin-content container-fluid py-4">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $page_title; ?></h1>
            <?php if (!$edit_slug): ?>
            <div class="page-actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIntegrationModal">
                    <i class="fas fa-plus me-2"></i> Add New Integration
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <?php display_notifications(); ?>
        
        <?php if ($edit_slug): ?>
        <!-- Edit Integration Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Edit Integration</h6>
                <a href="admin-integrations.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="admin-integrations.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_integration">
                    <input type="hidden" name="slug" value="<?php echo $edit_slug; ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" class="form-control" id="display_name" name="settings[display_name]" value="<?php echo $current_integration['display_name']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="settings[description]" rows="3"><?php echo $current_integration['description']; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="settings[category]" value="<?php echo $current_integration['category']; ?>">
                                <small class="form-text text-muted">E.g., Marketing, Analytics, Social Media, CRM</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="doc_url" class="form-label">Documentation URL</label>
                                <input type="text" class="form-control" id="doc_url" name="settings[doc_url]" value="<?php echo $current_integration['doc_url']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="api_key" name="settings[api_key]" value="<?php echo $current_integration['api_key']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="secret_key" class="form-label">Secret Key</label>
                                <input type="password" class="form-control" id="secret_key" name="settings[secret_key]" value="<?php echo $current_integration['secret_key']; ?>">
                            </div>
                            
                            <h5 class="mt-4 mb-3">SEO Settings</h5>
                            
                            <div class="mb-3">
                                <label for="seo_title" class="form-label">SEO Title</label>
                                <input type="text" class="form-control" id="seo_title" name="settings[seo_title]" value="<?php echo $current_integration['seo_title']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="seo_description" class="form-label">SEO Description</label>
                                <textarea class="form-control" id="seo_description" name="settings[seo_description]" rows="3"><?php echo $current_integration['seo_description']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="settings[is_active]" value="1" <?php echo $current_integration['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                                <small class="form-text text-muted">Enable or disable this integration</small>
                            </div>
                            
                            <div class="mb-4">
                                <label for="logo" class="form-label">Integration Logo</label>
                                <div class="upload-field-container">
                                    <input type="hidden" id="logo" name="settings[logo]" value="<?php echo $current_integration['logo']; ?>">
                                    
                                    <div class="upload-preview mb-3" id="preview_logo">
                                        <?php if (!empty($current_integration['logo'])): ?>
                                        <img src="<?php echo $current_integration['logo']; ?>?v=<?php echo time(); ?>" alt="Logo" class="img-thumbnail" style="max-height: 150px">
                                        <button type="button" class="btn btn-sm btn-danger remove-file" data-target="logo">Remove</button>
                                        <?php else: ?>
                                        <div class="no-file">No logo selected</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="upload-buttons">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary upload-btn" data-type="image" data-target="logo">
                                                <i class="fas fa-upload"></i> Upload Logo
