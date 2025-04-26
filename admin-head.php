<?php
/**
 * Admin Head Component
 * This file contains the head section for all admin panel pages
 */

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'Admin Panel';
}

// Set additional CSS files if needed
if (!isset($extra_css)) {
    $extra_css = [];
}

// Set additional JS files if needed
if (!isset($extra_js)) {
    $extra_js = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title><?php echo htmlspecialchars($page_title); ?> | Backsure Global Support</title>
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <!-- Favicon -->
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  
  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/css/admin-core.css">
  
  <!-- Page specific CSS -->
  <?php foreach ($extra_css as $css_file): ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>">
  <?php endforeach; ?>
  
  <!-- Core JS -->
  <script src="assets/js/jquery.min.js"></script>
  
  <!-- Page specific JS -->
  <?php foreach ($extra_js as $js_file): ?>
  <script src="<?php echo htmlspecialchars($js_file); ?>"></script>
  <?php endforeach; ?>
  <script src="assets/js/admin-lazy-load.js"></script>
  <link rel="stylesheet" href="assets/css/admin-themes.css">
</head>
<body class="admin-body">
  <div class="admin-container">
