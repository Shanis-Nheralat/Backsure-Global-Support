<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> | Backsure Global Support</title>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <!-- Core styles -->
  <link rel="stylesheet" href="assets/css/admin-core.css">
  <link rel="stylesheet" href="assets/css/admin-themes.css">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  
  <!-- Extra CSS files -->
  <?php if (isset($extra_css) && is_array($extra_css)): ?>
    <?php foreach ($extra_css as $css_file): ?>
      <link rel="stylesheet" href="<?php echo $css_file; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Core JavaScript -->
  <script src="assets/js/admin-core.js"></script>
  <script src="assets/js/admin-theme-switcher.js"></script>
</head>
<body class="admin-body">
<div class="admin-container">
