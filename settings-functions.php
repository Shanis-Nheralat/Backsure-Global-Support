<?php
/**
 * Settings Functions
 * Core functionality for settings management system
 */

// Prevent direct access
if (!defined('ADMIN_PANEL')) {
    exit('Direct access not permitted');
}

/**
 * Get a setting from the database
 * 
 * @param string $group The settings group (e.g., 'seo_homepage', 'chatbot')
 * @param string $key The setting key (e.g., 'meta_title', 'enabled')
 * @param mixed $default Default value if setting doesn't exist
 * @return mixed The setting value or default
 */
function get_setting($group, $key, $default = null) {
    global $db;
    
    // Static cache to reduce database queries
    static $settings_cache = [];
    
    // Create cache key
    $cache_key = $group . '_' . $key;
    
    // Check if value is in cache
    if (isset($settings_cache[$cache_key])) {
        return format_setting_value($settings_cache[$cache_key]['value'], $settings_cache[$cache_key]['type']);
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $db->prepare("SELECT setting_value, type FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
    $stmt->bind_param("ss", $group, $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Store in cache
        $settings_cache[$cache_key] = [
            'value' => $row['setting_value'],
            'type' => $row['type']
        ];
        
        return format_setting_value($row['setting_value'], $row['type']);
    }
    
    return $default;
}

/**
 * Format setting value based on type
 * 
 * @param mixed $value The setting value
 * @param string $type The setting type
 * @return mixed The formatted value
 */
function format_setting_value($value, $type) {
    switch ($type) {
        case 'boolean':
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        case 'json':
            $decoded = json_decode($value, true);
            return ($decoded !== null) ? $decoded : $value;
        case 'image':
        case 'file':
            // Add cache-busting parameter for images and files
            if (!empty($value)) {
                return $value . '?v=' . time();
            }
            return $value;
        default:
            return $value;
    }
}

/**
 * Set a setting in the database
 * 
 * @param string $group The settings group
 * @param string $key The setting key
 * @param mixed $value The setting value
 * @param string $type The setting type (text, textarea, boolean, image, json, file)
 * @return bool True on success, false on failure
 */
function set_setting($group, $key, $value, $type = 'text') {
    global $db;
    
    // Format value for storage based on type
    if ($type === 'boolean') {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    } elseif ($type === 'json' && is_array($value)) {
        $value = json_encode($value);
    }
    
    // Handle file uploads for image and file types
    if (($type == 'image' || $type == 'file') && $value !== null && $value !== '') {
        // If this is a new file upload, handle the old file
        $old_value = get_setting($group, $key, '');
        
        // Strip cache-busting parameter if present
        if (strpos($old_value, '?v=') !== false) {
            $old_value = substr($old_value, 0, strpos($old_value, '?v='));
        }
        
        if ($old_value && $old_value !== $value && file_exists(UPLOAD_DIR . $old_value)) {
            // Check if the file is used elsewhere before deleting
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM settings 
                                 WHERE setting_value = ? 
                                 AND NOT (setting_group = ? AND setting_key = ?)");
            $stmt->bind_param("sss", $old_value, $group, $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            // If file isn't used elsewhere, delete it
            if ($row['count'] == 0) {
                @unlink(UPLOAD_DIR . $old_value);
                log_admin_activity('file_deleted', 'settings', null, "Deleted unused file: $old_value");
            }
        }
    }
    
    // Check if setting already exists
    $stmt = $db->prepare("SELECT id FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
    $stmt->bind_param("ss", $group, $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get current user ID for tracking
    $admin_user = get_admin_user();
    $updated_by = $admin_user ? $admin_user['id'] : null;
    
    if ($result && $result->num_rows > 0) {
        // Update existing setting
        $stmt = $db->prepare("UPDATE settings SET setting_value = ?, type = ?, updated_by = ? 
                            WHERE setting_group = ? AND setting_key = ?");
        $stmt->bind_param("ssiss", $value, $type, $updated_by, $group, $key);
    } else {
        // Insert new setting
        $stmt = $db->prepare("INSERT INTO settings (setting_group, setting_key, setting_value, type, updated_by) 
                            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $group, $key, $value, $type, $updated_by);
    }
    
    $success = $stmt->execute();
    
    if ($success) {
        // Log the activity
        log_admin_activity('setting_updated', 'settings', null, "Updated setting: $group.$key");
        
        // Clear cache for this setting
        static $settings_cache = [];
        unset($settings_cache[$group . '_' . $key]);
        
        // Set success notification
        set_success_message("Setting '$key' updated successfully.");
        
        return true;
    }
    
    // Set error notification
    set_error_message("Failed to update setting '$key'.");
    return false;
}

/**
 * Handle file upload for settings
 * 
 * @param string $file_input_name The name of the file input field
 * @param string $destination_dir The destination directory
 * @param array $allowed_types Array of allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return string|false The path to the uploaded file or false on failure
 */
function handle_file_upload($file_input_name, $destination_dir = 'uploads/', $allowed_types = ['image/jpeg', 'image/png', 'image/gif'], $max_size = 5242880) {
    // Check if file was uploaded
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $file = $_FILES[$file_input_name];
    
    // Check file size
    if ($file['size'] > $max_size) {
        set_error_message("File is too large. Maximum size is " . ($max_size / 1024 / 1024) . "MB.");
        return false;
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_type = $finfo->file($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        set_error_message("File type not allowed. Allowed types: " . implode(', ', array_map(function($type) {
            return explode('/', $type)[1];
        }, $allowed_types)));
        return false;
    }
    
    // Create destination directory if it doesn't exist
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = $destination_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return str_replace(UPLOAD_DIR, '', $destination);
    }
    
    set_error_message("Failed to upload file. Please try again.");
    return false;
}

/**
 * Render a setting field
 * 
 * @param string $group The settings group
 * @param string $key The setting key
 * @param string $label The field label
 * @param string $type The field type
 * @param array $options Additional options for the field
 * @return string The HTML for the field
 */
function render_setting_field($group, $key, $label, $type = 'text', $options = []) {
    $current_value = get_setting($group, $key);
    $field_id = "setting_{$group}_{$key}";
    $field_name = "settings[$group][$key]";
    
    // Default options
    $default_options = [
        'placeholder' => '',
        'description' => '',
        'required' => false,
        'class' => '',
        'options' => [],
        'min' => null,
        'max' => null,
        'step' => null
    ];
    
    // Merge options
    $options = array_merge($default_options, $options);
    
    // Build HTML
    $html = '<div class="form-group mb-4">';
    $html .= '<label for="' . $field_id . '" class="form-label">' . $label;
    
    // Add required asterisk
    if ($options['required']) {
        $html .= ' <span class="text-danger">*</span>';
    }
    
    $html .= '</label>';
    
    // Strip cache parameter from current value for display
    if (($type === 'image' || $type === 'file') && $current_value) {
        if (strpos($current_value, '?v=') !== false) {
            $display_value = substr($current_value, 0, strpos($current_value, '?v='));
        } else {
            $display_value = $current_value;
        }
    } else {
        $display_value = $current_value;
    }
    
    // Different rendering based on field type
    switch ($type) {
        case 'textarea':
            $html .= '<textarea id="' . $field_id . '" name="' . $field_name . '" class="form-control ' . $options['class'] . '" ' .
                    'placeholder="' . $options['placeholder'] . '" ' .
                    ($options['required'] ? 'required' : '') . '>' .
                    htmlspecialchars($display_value) . '</textarea>';
            break;
            
        case 'boolean':
            $checked = filter_var($display_value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';
            $html .= '<div class="form-check form-switch">';
            $html .= '<input type="checkbox" id="' . $field_id . '" name="' . $field_name . '" class="form-check-input ' . $options['class'] . '" value="1" ' . $checked . '>';
            $html .= '</div>';
            break;
            
        case 'select':
            $html .= '<select id="' . $field_id . '" name="' . $field_name . '" class="form-select ' . $options['class'] . '" ' .
                    ($options['required'] ? 'required' : '') . '>';
            
            foreach ($options['options'] as $value => $option_label) {
                $selected = ($display_value == $value) ? 'selected' : '';
                $html .= '<option value="' . $value . '" ' . $selected . '>' . $option_label . '</option>';
            }
            
            $html .= '</select>';
            break;
            
        case 'number':
            $min = $options['min'] !== null ? 'min="' . $options['min'] . '"' : '';
            $max = $options['max'] !== null ? 'max="' . $options['max'] . '"' : '';
            $step = $options['step'] !== null ? 'step="' . $options['step'] . '"' : '';
            
            $html .= '<input type="number" id="' . $field_id . '" name="' . $field_name . '" value="' . $display_value . '" ' .
                    'class="form-control ' . $options['class'] . '" ' .
                    'placeholder="' . $options['placeholder'] . '" ' .
                    $min . ' ' . $max . ' ' . $step . ' ' .
                    ($options['required'] ? 'required' : '') . '>';
            break;
            
        case 'image':
        case 'file':
            $html .= render_upload_field($group, $key, $type, $options);
            break;
            
        case 'json':
            $html .= '<textarea id="' . $field_id . '" name="' . $field_name . '" class="form-control ' . $options['class'] . '" ' .
                    'placeholder="' . $options['placeholder'] . '" ' .
                    ($options['required'] ? 'required' : '') . '>' .
                    (is_array($display_value) ? json_encode($display_value, JSON_PRETTY_PRINT) : $display_value) . '</textarea>';
            break;
            
        case 'readonly':
            $html .= '<input type="text" id="' . $field_id . '" value="' . $display_value . '" ' .
                    'class="form-control ' . $options['class'] . '" readonly>';
            $html .= '<input type="hidden" name="' . $field_name . '" value="' . $display_value . '">';
            break;
            
        default: // text
            $html .= '<input type="text" id="' . $field_id . '" name="' . $field_name . '" value="' . $display_value . '" ' .
                    'class="form-control ' . $options['class'] . '" ' .
                    'placeholder="' . $options['placeholder'] . '" ' .
                    ($options['required'] ? 'required' : '') . '>';
            break;
    }
    
    // Add description if provided
    if (!empty($options['description'])) {
        $html .= '<div class="form-text text-muted">' . $options['description'] . '</div>';
    }
    
    // Show last updated info if available
    $last_updated = get_setting_meta($group, $key);
    if ($last_updated) {
        $html .= '<div class="setting-meta small text-muted mt-1">';
        $html .= 'Last updated: ' . date('M j, Y g:i A', strtotime($last_updated['updated_at']));
        
        if ($last_updated['updated_by']) {
            $user = get_user_by_id($last_updated['updated_by']);
            if ($user) {
                $html .= ' by ' . htmlspecialchars($user['name']);
            }
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get setting metadata (last updated, updated by)
 * 
 * @param string $group The settings group
 * @param string $key The setting key
 * @return array|null The setting metadata or null if not found
 */
function get_setting_meta($group, $key) {
    global $db;
    
    $stmt = $db->prepare("SELECT updated_at, updated_by FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
    $stmt->bind_param("ss", $group, $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Render an upload field for image or file
 * 
 * @param string $group The settings group
 * @param string $key The setting key
 * @param string $type The field type ('image' or 'file')
 * @param array $options Additional options for the field
 * @return string The HTML for the upload field
 */
function render_upload_field($group, $key, $type = 'image', $options = []) {
    $current_value = get_setting($group, $key);
    $field_id = "setting_{$group}_{$key}";
    $field_name = "settings[$group][$key]";
    $file_input_name = "settings_files[{$group}][{$key}]";
    
    // Strip cache parameter for display
    if ($current_value && strpos($current_value, '?v=') !== false) {
        $display_value = substr($current_value, 0, strpos($current_value, '?v='));
    } else {
        $display_value = $current_value;
    }
    
    // Build HTML
    $html = '<div class="upload-field-container">';
    
    // Hidden field to store the file path
    $html .= '<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' . $display_value . '">';
    
    // File input
    $html .= '<div class="upload-controls d-flex gap-2 mb-2">';
    
    // Device upload button
    $html .= '<div class="device-upload">';
    $html .= '<input type="file" id="file_' . $field_id . '" name="' . $file_input_name . '" class="d-none" ' .
            'onchange="previewUploadedFile(\'' . $field_id . '\', this)">';
    $html .= '<button type="button" class="btn btn-outline-primary" onclick="document.getElementById(\'file_' . $field_id . '\').click();">';
    $html .= '<i class="fas fa-upload me-1"></i> Upload from Device</button>';
    $html .= '</div>';
    
    // Media library button
    $html .= '<button type="button" class="btn btn-outline-secondary" onclick="openMediaLibrary(\'' . $field_id . '\', \'' . $type . '\');">';
    $html .= '<i class="fas fa-photo-video me-1"></i> Choose from Media Library</button>';
    
    // Remove current button (visible only when a file is selected)
    $visibility = !empty($display_value) ? '' : 'd-none';
    $html .= '<button type="button" id="remove_' . $field_id . '" class="btn btn-outline-danger ' . $visibility . '" onclick="removeCurrentFile(\'' . $field_id . '\');">';
    $html .= '<i class="fas fa-trash-alt me-1"></i> Remove Current File</button>';
    
    $html .= '</div>';
    
    // Preview area
    if ($type === 'image') {
        $visibility = !empty($display_value) ? '' : 'd-none';
        $src = !empty($display_value) ? UPLOAD_URL . $display_value : '';
        $html .= '<div id="preview_' . $field_id . '" class="file-preview image-preview ' . $visibility . '">';
        $html .= '<img src="' . $src . '" alt="Preview" class="img-thumbnail mb-2" style="max-height: 150px;">';
        $html .= '<div class="file-name">' . basename($display_value) . '</div>';
        $html .= '</div>';
    } else {
        $visibility = !empty($display_value) ? '' : 'd-none';
        $html .= '<div id="preview_' . $field_id . '" class="file-preview ' . $visibility . '">';
        $html .= '<div class="file-icon"><i class="fas fa-file me-2"></i></div>';
        $html .= '<div class="file-name">' . basename($display_value) . '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    // Add JavaScript for file preview
    $html .= '
    <script>
    function previewUploadedFile(fieldId, fileInput) {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const reader = new FileReader();
            const preview = document.getElementById("preview_" + fieldId);
            const fileField = document.getElementById(fieldId);
            const removeBtn = document.getElementById("remove_" + fieldId);
            
            reader.onload = function(e) {
                if (preview) {
                    preview.classList.remove("d-none");
                    
                    // Handle image preview
                    const img = preview.querySelector("img");
                    if (img && file.type.startsWith("image/")) {
                        img.src = e.target.result;
                    }
                    
                    // Update file name
                    const fileName = preview.querySelector(".file-name");
                    if (fileName) {
                        fileName.textContent = file.name;
                    }
                    
                    // Show remove button
                    if (removeBtn) {
                        removeBtn.classList.remove("d-none");
                    }
                }
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    function openMediaLibrary(fieldId, type) {
        // Open media library in a modal or popup
        window.open("media-library.php?target=" + fieldId + "&type=" + type, "mediaLibrary", "width=800,height=600");
    }
    
    function removeCurrentFile(fieldId) {
        const fileField = document.getElementById(fieldId);
        const preview = document.getElementById("preview_" + fieldId);
        const removeBtn = document.getElementById("remove_" + fieldId);
        const fileInput = document.getElementById("file_" + fieldId);
        
        if (fileField) {
            fileField.value = "";
        }
        
        if (preview) {
            preview.classList.add("d-none");
            const img = preview.querySelector("img");
            if (img) {
                img.src = "";
            }
        }
        
        if (removeBtn) {
            removeBtn.classList.add("d-none");
        }
        
        if (fileInput) {
            fileInput.value = "";
        }
    }
    
    // Function to be called from media library popup
    function setMediaFile(fieldId, filePath, fileUrl) {
        const fileField = document.getElementById(fieldId);
        const preview = document.getElementById("preview_" + fieldId);
        const removeBtn = document.getElementById("remove_" + fieldId);
        
        if (fileField) {
            fileField.value = filePath;
        }
        
        if (preview) {
            preview.classList.remove("d-none");
            
            // Handle image preview
            const img = preview.querySelector("img");
            if (img && fileUrl.match(/\.(jpeg|jpg|gif|png)$/i)) {
                img.src = fileUrl;
            }
            
            // Update file name
            const fileName = preview.querySelector(".file-name");
            if (fileName) {
                fileName.textContent = filePath.split("/").pop();
            }
        }
        
        if (removeBtn) {
            removeBtn.classList.remove("d-none");
        }
    }
    </script>';
    
    return $html;
}

/**
 * Get all settings by group
 * 
 * @param string $group The settings group
 * @return array Array of settings
 */
function get_settings_by_group($group) {
    global $db;
    
    $stmt = $db->prepare("SELECT setting_key, setting_value, type FROM settings WHERE setting_group = ?");
    $stmt->bind_param("s", $group);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = format_setting_value($row['setting_value'], $row['type']);
    }
    
    return $settings;
}

/**
 * Get setting type
 * 
 * @param string $group The settings group
 * @param string $key The setting key
 * @return string The setting type or 'text' if not found
 */
function get_setting_type($group, $key) {
    global $db;
    
    $stmt = $db->prepare("SELECT type FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
    $stmt->bind_param("ss", $group, $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['type'];
    }
    
    return 'text';
}

/**
 * Handle settings form submission
 * 
 * @param array $post_data The $_POST data
 * @param array $files The $_FILES data
 * @return int Number of settings updated
 */
function process_settings_form($post_data, $files) {
    $updated = 0;
    
    if (isset($post_data['settings']) && is_array($post_data['settings'])) {
        foreach ($post_data['settings'] as $group => $settings) {
            foreach ($settings as $key => $value) {
                // Get the field type
                $type = get_setting_type($group, $key);
                
                // Handle file uploads if needed
                if (($type == 'image' || $type == 'file') && isset($files['settings_files']['name'][$group][$key]) && !empty($files['settings_files']['name'][$group][$key])) {
                    // Create custom file array for the specific field
                    $file = [
                        'name' => $files['settings_files']['name'][$group][$key],
                        'type' => $files['settings_files']['type'][$group][$key],
                        'tmp_name' => $files['settings_files']['tmp_name'][$group][$key],
                        'error' => $files['settings_files']['error'][$group][$key],
                        'size' => $files['settings_files']['size'][$group][$key]
                    ];
                    
                    // Define allowed types based on field type
                    $allowed_types = ($type == 'image') 
                        ? ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'] 
                        : ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
                    
                    // Handle the upload
                    $upload_result = handle_file_upload('settings_files', UPLOAD_DIR, $allowed_types);
                    
                    if ($upload_result) {
                        $value = $upload_result;
                    }
                }
                
                // Update the setting
                if (set_setting($group, $key, $value, $type)) {
                    $updated++;
                }
            }
        }
    }
    
    return $updated;
}
