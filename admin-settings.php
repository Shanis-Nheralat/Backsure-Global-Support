<?php
/**
 * Admin Site Settings
 * Manages general configuration for the website
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
            
            // Handle file uploads
            if (($type == 'image' || $type == 'file') && isset($_FILES['settings_files']) && 
                isset($_FILES['settings_files']['name'][$group][$key]) && 
                !empty($_FILES['settings_files']['name'][$group][$key])) {
                
                $file = [
                    'name' => $_FILES['settings_files']['name'][$group][$key],
                    'type' => $_FILES['settings_files']['type'][$group][$key],
                    'tmp_name' => $_FILES['settings_files']['tmp_name'][$group][$key],
                    'error' => $_FILES['settings_files']['error'][$group][$key],
                    'size' => $_FILES['settings_files']['size'][$group][$key]
                ];
                
                $value = handle_file_upload($file, get_setting($group, $key));
            }
            
            // Update the setting
            if (set_setting($group, $key, $value, $type)) {
                $updated++;
            }
        }
    }
    
    if ($updated > 0) {
        set_admin_notification('success', 'Site settings updated successfully.', '#', get_admin_user()['id']);
    } else {
        set_admin_notification('info', 'No changes were made to site settings.', '#', get_admin_user()['id']);
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin-settings.php');
    exit;
}

// Set page variables
$page_title = 'Site Settings';
$current_page = 'site_settings';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Site Settings', 'url' => '#']
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
                <h6 class="m-0 font-weight-bold text-primary">Basic Site Configuration</h6>
            </div>
            <div class="card-body">
                <form action="admin-settings.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo render_setting_field('site_general', 'site_name', 'Site Name', 'text', [
                                'placeholder' => 'Enter your website name',
                                'required' => true
                            ]); ?>
                            
                            <?php echo render_setting_field('site_general', 'site_tagline', 'Site Tagline', 'text', [
