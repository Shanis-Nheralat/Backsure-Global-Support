<?php
/**
 * Media Library
 * File for managing media uploads and selection
 */

// Include authentication and common functions
require_once 'admin-auth.php';
require_once 'settings-functions.php';

// Require admin authentication
require_admin_auth();

// Handle file upload
if (isset($_FILES['upload']) && !empty($_FILES['upload']['name'])) {
    $response = [];
    
    // Create upload directory
    $upload_dir = '../uploads/' . date('Y') . '/' . date('m') . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['upload'];
    $filename = basename($file['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $ext;
    $target_path = $upload_dir . $unique_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $response['success'] = true;
        $response['file'] = $target_path;
        $response['filename'] = $filename;
        
        // For images, get dimensions
        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
            $dimensions = getimagesize($target_path);
            $response['width'] = $dimensions[0];
            $response['height'] = $dimensions[1];
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Failed to upload file';
    }
    
    // Return JSON response for AJAX uploads
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Get all files in the uploads directory
function get_media_files() {
    $files = [];
    $base_dir = '../uploads/';
    
    // Recursive function to get all files
    function scan_dir($dir, &$files) {
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            
            $path = $dir . '/' . $item;
            
            if (is_dir($path)) {
                scan_dir($path, $files);
            } else {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'file';
                
                $files[] = [
                    'path' => $path,
                    'name' => $item,
                    'type' => $type,
                    'size' => filesize($path),
                    'modified' => filemtime($path)
                ];
            }
        }
    }
    
    if (is_dir($base_dir)) {
        scan_dir($base_dir, $files);
    }
    
    // Sort by modified date (newest first)
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    return $files;
}

$media_files = get_media_files();
$is_modal = isset($_GET['modal']) && $_GET['modal'] == 1;
$target_field = $_GET['target'] ?? '';
$file_type = $_GET['type'] ?? 'all';

// Set page variables
$page_title = 'Media Library';
$current_page = 'media_library';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Media Library', 'url' => '#']
];

// For modal view, we don't need the header/footer
if (!$is_modal) {
    include 'admin-head.php';
    include 'admin-sidebar.php';
    include 'admin-header.php';
}
?>

<?php if ($is_modal): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding: 15px; }
        .media-item { cursor: pointer; transition: all 0.2s; }
        .media-item:hover { transform: scale(1.05); }
        .media-item.selected { border: 2px solid #0d6efd; }
        .media-thumbnail { height: 120px; object-fit: cover; }
    </style>
</head>
<body>
<?php endif; ?>

<div class="<?php echo $is_modal ? 'container-fluid' : 'admin-content container-fluid py-4'; ?>">
    <?php if (!$is_modal): ?>
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1>Media Library</h1>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" id="uploadBtn">
                <i class="fas fa-upload me-2"></i> Upload New
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php echo $is_modal ? 'Select a file' : 'Upload New File'; ?>
            </h6>
            <?php if ($is_modal): ?>
            <button type="button" class="btn btn-primary btn-sm" id="uploadBtn">
                <i class="fas fa-upload me-2"></i> Upload New
            </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="uploadForm" action="media-library.php<?php echo $is_modal ? '?modal=1&target='.$target_field.'&type='.$file_type : ''; ?>" method="post" enctype="multipart/form-data" style="display: none;">
                <div class="mb-3">
                    <label for="fileUpload" class="form-label">Choose File</label>
                    <input type="file" class="form-control" id="fileUpload" name="upload" required>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
                <button type="button" class="btn btn-secondary" id="cancelUpload">Cancel</button>
            </form>
            
            <!-- Filter and Search -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="image">Images</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="file">Documents</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchMedia" placeholder="Search files...">
                </div>
            </div>
            
            <!-- Media Grid -->
            <div class="row" id="mediaGrid">
                <?php foreach ($media_files as $file): ?>
                <?php 
                    $is_image = $file['type'] == 'image';
                    $file_icon = $is_image ? '' : 'fa-file-alt';
                    $thumbnail = $is_image ? $file['path'] : '';
                    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    
                    // Skip if filtering by type
                    if ($file_type != 'all' && $file['type'] != $file_type) continue;
                ?>
                <div class="col-md-2 col-sm-3 mb-4 media-item" data-type="<?php echo $file['type']; ?>" data-path="<?php echo $file['path']; ?>" data-name="<?php echo $file['name']; ?>">
                    <div class="card h-100">
                        <div class="card-body text-center p-2">
                            <?php if ($is_image): ?>
                            <img src="<?php echo $file['path']; ?>" class="img-fluid media-thumbnail mb-2" alt="<?php echo $file['name']; ?>">
                            <?php else: ?>
                            <div class="file-icon mb-2">
                                <i class="fas fa-file fa-3x text-secondary"></i>
                                <span class="file-ext"><?php echo strtoupper($file_ext); ?></span>
                            </div>
                            <?php endif; ?>
                            <small class="d-block text-truncate"><?php echo $file['name']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($is_modal): ?>
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-secondary" id="cancelSelect">Cancel</button>
                <button type="button" class="btn btn-primary" id="selectFile" disabled>Select</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($is_modal): ?>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle upload form
    $('#uploadBtn').click(function() {
        $('#uploadForm').toggle();
    });
    
    $('#cancelUpload').click(function() {
        $('#uploadForm').hide();
    });
    
    // Filter media files
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.media-item').show();
        } else {
            $('.media-item').hide();
            $(`.media-item[data-type="${filter}"]`).show();
        }
    });
    
    // Search functionality
    $('#searchMedia').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        
        $('.media-item').filter(function() {
            const name = $(this).data('name').toLowerCase();
            $(this).toggle(name.indexOf(value) > -1);
        });
    });
    
    // Select media item
    $('.media-item').click(function() {
        $('.media-item').removeClass('selected');
        $(this).addClass('selected');
        $('#selectFile').prop('disabled', false);
    });
    
    // Select button click
    $('#selectFile').click(function() {
        const selected = $('.media-item.selected');
        
        if (selected.length > 0) {
            const path = selected.data('path');
            const name = selected.data('name');
            
            // Send selected file back to parent window
            window.opener.setMediaFile('<?php echo $target_field; ?>', path, name);
            window.close();
        }
    });
    
    // Cancel button click
    $('#cancelSelect').click(function() {
        window.close();
    });
});
</script>
<?php else: ?>
<?php include 'admin-footer.php'; ?>
<?php endif; ?>
