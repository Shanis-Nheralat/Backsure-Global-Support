/**
 * Get a setting value
 * @param string $group Setting group
 * @param string $key Setting key
 * @param mixed $default Default value if setting is not found
 * @return mixed Setting value or default
 */
function get_setting($group, $key, $default = '') {
    global $db;
    
    static $settings_cache = [];
    
    // Check cache first
    $cache_key = $group . '_' . $key;
    if (isset($settings_cache[$cache_key])) {
        return $settings_cache[$cache_key];
    }
    
    try {
        $stmt = $db->prepare("SELECT setting_value, type FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
        $stmt->execute([$group, $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Format based on type
            $value = format_setting_value($result['setting_value'], $result['type']);
            $settings_cache[$cache_key] = $value;
            return $value;
        }
    } catch (PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
    }
    
    return $default;
}

/**
 * Format setting value based on type
 */
function format_setting_value($value, $type) {
    switch ($type) {
        case 'boolean':
            return (bool)$value;
        case 'json':
            return json_decode($value, true);
        default:
            return $value;
    }
}

/**
 * Set a setting value
 * @param string $group Setting group
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @param string $type Setting type
 * @return bool True on success, false on failure
 */
function set_setting($group, $key, $value, $type = 'text') {
    global $db;
    
    // Format value for storage
    if ($type == 'json' && !is_string($value)) {
        $value = json_encode($value);
    } elseif ($type == 'boolean') {
        $value = $value ? '1' : '0';
    }
    
    // Check if this is a file field and handle upload
    if (($type == 'image' || $type == 'file') && is_array($value) && isset($value['tmp_name'])) {
        $old_value = get_setting($group, $key);
        $value = handle_file_upload($value, $old_value);
        if ($value === false) {
            return false;
        }
    }
    
    try {
        // Check if setting exists
        $stmt = $db->prepare("SELECT id FROM settings WHERE setting_group = ? AND setting_key = ? LIMIT 1");
        $stmt->execute([$group, $key]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing setting
            $stmt = $db->prepare("UPDATE settings SET setting_value = ?, type = ?, updated_at = NOW() WHERE setting_group = ? AND setting_key = ?");
            $result = $stmt->execute([$value, $type, $group, $key]);
        } else {
            // Insert new setting
            $stmt = $db->prepare("INSERT INTO settings (setting_group, setting_key, setting_value, type, autoload) VALUES (?, ?, ?, ?, 1)");
            $result = $stmt->execute([$group, $key, $value, $type]);
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error setting setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle file upload for settings
 * @param array $file File data from $_FILES
 * @param string $old_file Old file path to delete if replaced
 * @return string|false New file path or false on failure
 */
function handle_file_upload($file, $old_file = '') {
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/' . date('Y') . '/' . date('m') . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $filename = basename($file['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $ext;
    $target_path = $upload_dir . $unique_name;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Delete old file if it exists
        if (!empty($old_file) && file_exists($old_file) && $old_file != $target_path) {
            unlink($old_file);
        }
        return $target_path;
    }
    
    return false;
}

/**
 * Render a setting field
 * @param string $group Setting group
 * @param string $key Setting key
 * @param string $label Field label
 * @param string $type Field type
 * @param array $options Additional options
 * @return string HTML for the field
 */
function render_setting_field($group, $key, $label, $type = 'text', $options = []) {
    $value = get_setting($group, $key);
    $field_id = "setting_{$group}_{$key}";
    $field_name = "settings[{$group}][{$key}]";
    $placeholder = $options['placeholder'] ?? '';
    $required = isset($options['required']) && $options['required'] ? 'required' : '';
    $description = isset($options['description']) ? "<small class=\"form-text text-muted\">{$options['description']}</small>" : '';
    
    $html = "<div class=\"mb-3\">";
    $html .= "<label for=\"{$field_id}\" class=\"form-label\">{$label}</label>";
    
    switch ($type) {
        case 'textarea':
            $rows = $options['rows'] ?? 4;
            $html .= "<textarea class=\"form-control\" id=\"{$field_id}\" name=\"{$field_name}\" rows=\"{$rows}\" {$required} placeholder=\"{$placeholder}\">{$value}</textarea>";
            break;
            
        case 'boolean':
            $checked = $value ? 'checked' : '';
            $html .= "<div class=\"form-check form-switch\">";
            $html .= "<input class=\"form-check-input\" type=\"checkbox\" id=\"{$field_id}\" name=\"{$field_name}\" value=\"1\" {$checked}>";
            $html .= "</div>";
            break;
            
        case 'image':
            $html .= render_upload_field($group, $key, 'image', $options);
            break;
            
        case 'file':
            $html .= render_upload_field($group, $key, 'file', $options);
            break;
            
        default: // text, email, number, etc.
            $html .= "<input type=\"{$type}\" class=\"form-control\" id=\"{$field_id}\" name=\"{$field_name}\" value=\"{$value}\" {$required} placeholder=\"{$placeholder}\">";
    }
    
    $html .= $description;
    $html .= "</div>";
    
    return $html;
}

/**
 * Render a file upload field with media library integration
 * @param string $group Setting group
 * @param string $key Setting key
 * @param string $type 'image' or 'file'
 * @param array $options Additional options
 * @return string HTML for the field
 */
function render_upload_field($group, $key, $type = 'image', $options = []) {
    $value = get_setting($group, $key);
    $field_id = "setting_{$group}_{$key}";
    $field_name = "settings[{$group}][{$key}]";
    $preview_id = "preview_{$field_id}";
    $required = isset($options['required']) && $options['required'] ? 'required' : '';
    
    $html = "<div class=\"upload-field-container\">";
    
    // Hidden field for storing the file path
    $html .= "<input type=\"hidden\" id=\"{$field_id}\" name=\"{$field_name}\" value=\"{$value}\">";
    
    // Preview area
    $html .= "<div class=\"upload-preview mb-3\" id=\"{$preview_id}\">";
    if (!empty($value)) {
        if ($type == 'image') {
            $html .= "<img src=\"{$value}?v=" . time() . "\" alt=\"Preview\" class=\"img-thumbnail\" style=\"max-height: 150px\">";
        } else {
            $filename = basename($value);
            $html .= "<div class=\"file-preview\"><i class=\"fas fa-file\"></i> {$filename}</div>";
        }
        $html .= "<button type=\"button\" class=\"btn btn-sm btn-danger remove-file\" data-target=\"{$field_id}\">Remove</button>";
    } else {
        $html .= "<div class=\"no-file\">No file selected</div>";
    }
    $html .= "</div>";
    
    // Buttons
    $html .= "<div class=\"upload-buttons\">";
    $html .= "<div class=\"btn-group\" role=\"group\">";
    $html .= "<button type=\"button\" class=\"btn btn-primary upload-btn\" data-type=\"{$type}\" data-target=\"{$field_id}\"><i class=\"fas fa-upload\"></i> Upload from Device</button>";
    $html .= "<button type=\"button\" class=\"btn btn-secondary media-library-btn\" data-type=\"{$type}\" data-target=\"{$field_id}\"><i class=\"fas fa-photo-video\"></i> Media Library</button>";
    $html .= "</div>";
    $html .= "</div>";
    
    // Actual file input (hidden, triggered by the upload button)
    $html .= "<input type=\"file\" class=\"hidden-file-input\" id=\"file_{$field_id}\" data-target=\"{$field_id}\" style=\"display: none;\">";
    
    $html .= "</div>";
    
    return $html;
}
