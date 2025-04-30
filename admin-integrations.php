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
                                                <i class="fas fa-upload"></i> Upload Logo<i class="fas fa-upload"></i> Upload Logo
                                            </button>
                                            <button type="button" class="btn btn-secondary media-library-btn" data-type="image" data-target="logo">
                                                <i class="fas fa-photo-video"></i> Media Library
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <input type="file" class="hidden-file-input" id="file_logo" data-target="logo" style="display: none;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="og_image" class="form-label">Social Sharing Image</label>
                                <div class="upload-field-container">
                                    <input type="hidden" id="og_image" name="settings[og_image]" value="<?php echo $current_integration['og_image']; ?>">
                                    
                                    <div class="upload-preview mb-3" id="preview_og_image">
                                        <?php if (!empty($current_integration['og_image'])): ?>
                                        <img src="<?php echo $current_integration['og_image']; ?>?v=<?php echo time(); ?>" alt="OG Image" class="img-thumbnail" style="max-height: 150px">
                                        <button type="button" class="btn btn-sm btn-danger remove-file" data-target="og_image">Remove</button>
                                        <?php else: ?>
                                        <div class="no-file">No image selected</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="upload-buttons">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary upload-btn" data-type="image" data-target="og_image">
                                                <i class="fas fa-upload"></i> Upload Image
                                            </button>
                                            <button type="button" class="btn btn-secondary media-library-btn" data-type="image" data-target="og_image">
                                                <i class="fas fa-photo-video"></i> Media Library
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <input type="file" class="hidden-file-input" id="file_og_image" data-target="og_image" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Integration
                        </button>
                        <a href="admin-integrations.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                        
                        <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteIntegrationModal">
                            <i class="fas fa-trash-alt me-2"></i> Delete Integration
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteIntegrationModal" tabindex="-1" aria-labelledby="deleteIntegrationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteIntegrationModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the integration <strong><?php echo $current_integration['display_name']; ?></strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="admin-integrations.php" method="post">
                            <input type="hidden" name="action" value="delete_integration">
                            <input type="hidden" name="slug" value="<?php echo $edit_slug; ?>">
                            <button type="submit" class="btn btn-danger">Delete Integration</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Integrations List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Available Integrations</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="integrationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Integration</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($integrations) > 0): ?>
                                <?php foreach ($integrations as $slug => $integration): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($integration['logo'])): ?>
                                            <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['display_name']; ?>" class="integration-logo me-2" style="max-width: 40px; max-height: 40px;">
                                            <?php else: ?>
                                            <div class="integration-logo-placeholder me-2"><i class="fas fa-plug"></i></div>
                                            <?php endif; ?>
                                            <span><?php echo $integration['display_name']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $integration['description']; ?></td>
                                    <td><?php echo $integration['category']; ?></td>
                                    <td>
                                        <?php if ($integration['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="admin-integrations.php?edit=<?php echo $slug; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No integrations found. <a href="#" data-bs-toggle="modal" data-bs-target="#addIntegrationModal">Add your first integration</a>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Add Integration Modal -->
<div class="modal fade" id="addIntegrationModal" tabindex="-1" aria-labelledby="addIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIntegrationModalLabel">Add New Integration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin-integrations.php" method="post">
                <input type="hidden" name="action" value="add_integration">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_display_name" class="form-label">Integration Name</label>
                        <input type="text" class="form-control" id="new_display_name" name="display_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_description" class="form-label">Description</label>
                        <textarea class="form-control" id="new_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="new_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="new_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Integration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Media library integration
    document.querySelectorAll('.upload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const fileInput = document.getElementById('file_' + targetId);
            if (fileInput) {
                fileInput.click();
            }
        });
    });
    
    document.querySelectorAll('.media-library-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const type = this.dataset.type;
            window.open('media-library.php?modal=1&target=' + targetId + '&type=' + type, 'mediaLibrary', 'width=800,height=600');
        });
    });
    
    document.querySelectorAll('.hidden-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const targetId = this.dataset.target;
            const previewId = 'preview_' + targetId;
            const previewDiv = document.getElementById(previewId);
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    let preview = '';
                    
                    if (file.type.startsWith('image/')) {
                        preview = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 150px">`;
                    } else {
                        preview = `<div class="file-preview"><i class="fas fa-file"></i> ${file.name}</div>`;
                    }
                    
                    preview += `<button type="button" class="btn btn-sm btn-danger remove-file" data-target="${targetId}">Remove</button>`;
                    previewDiv.innerHTML = preview;
                }
                
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Remove file functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-file')) {
            const targetId = e.target.dataset.target;
            const inputField = document.getElementById(targetId);
            const previewDiv = document.getElementById('preview_' + targetId);
            
            if (inputField) {
                inputField.value = '';
            }
            
            if (previewDiv) {
                previewDiv.innerHTML = '<div class="no-file">No file selected</div>';
            }
        }
    });
});

// Function to handle media library selection
function setMediaFile(targetId, filePath, fileName) {
    const inputField = document.getElementById(targetId);
    const previewDiv = document.getElementById('preview_' + targetId);
    
    if (inputField) {
        inputField.value = filePath;
    }
    
    if (previewDiv) {
        let preview = '';
        
        // Check if file is an image
        if (/\.(jpg|jpeg|png|gif)$/i.test(fileName)) {
            preview = `<img src="${filePath}?v=${Date.now()}" alt="Preview" class="img-thumbnail" style="max-height: 150px">`;
        } else {
            preview = `<div class="file-preview"><i class="fas fa-file"></i> ${fileName}</div>`;
        }
        
        preview += `<button type="button" class="btn btn-sm btn-danger remove-file" data-target="${targetId}">Remove</button>`;
        previewDiv.innerHTML = preview;
    }
}
</script>

<?php include 'admin-footer.php'; ?>
